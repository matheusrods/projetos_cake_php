<?php
App::import('Model', 'RamalUsuario');
class ModIvrPesquisa extends AppModel {

    var $name = 'ModIvrPesquisa';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbComunicacao';
    var $useTable = 'mod_ivr_pesquisa';
    var $primaryKey = 'logid'; 
    var $actsAs = array('Secure');


    function bindUsuarioComDepartamento() {
        $this->bindModel(array(
            'hasOne' => array(
                'UsuarioContato' => array(
                    'foreignKey' => false, 
                    'type' => 'LEFT', 
                    'conditions' => array('UsuarioContato.descricao = '.$this->databaseTable.'.'.$this->tableSchema.'.'.'removestr(ModIvrPesquisa.agtext)')
                    ),
                'Usuario' => array(
                    'foreignKey' => false, 
                    'type' => 'LEFT', 
                    'conditions' => array('Usuario.codigo = UsuarioContato.codigo_usuario')
                ),
                'Departamento' => array(
                    'foreignKey' => false, 
                    'type' => 'LEFT', 
                    'conditions' => array('Departamento.codigo = Usuario.codigo_departamento')
                )
            )), false
        );
    }

    function unbindCorporacao() {
        $this->unbindModel(array(
            'hasOne' => array(
                'UsuarioContato',
                'Usuario',
                'Departamento',
            )
        ));
    }


	public function converteFiltroEmCondition($dados) {
		$conditions = array();
		if (!empty($dados['ModIvrPesquisa']['logid'])) {
			$conditions["ModIvrPesquisa.logid"] = $dados['ModIvrPesquisa']["logid"];
		}
        if (!empty($dados['ModIvrPesquisa']['oani'])) {
            $conditions["ModIvrPesquisa.oani"] = $dados['ModIvrPesquisa']["oani"];
        }
        if (!empty($dados['ModIvrPesquisa']['odnis'])) {
            $conditions["ModIvrPesquisa.odnis"] = $dados['ModIvrPesquisa']["odnis"];
        }
        if (!empty($dados['ModIvrPesquisa']['otrkid'])) {
            $conditions["ModIvrPesquisa.otrkid"] = $dados['ModIvrPesquisa']["otrkid"];
        }
        if (!empty($dados['ModIvrPesquisa']['queue'])) {
            $conditions["ModIvrPesquisa.queue"] = $dados['ModIvrPesquisa']["queue"];
        }
        if (!empty($dados['ModIvrPesquisa']['agtext'])) {
            if($dados['ModIvrPesquisa']['agtext'] == '99') {
                $conditions['OR'] = array("ModIvrPesquisa.agtext = ''", 'ModIvrPesquisa.agtext is null');
            }else {
                $conditions[] = $this->databaseTable.'.'.$this->tableSchema.'.'.'removestr(ModIvrPesquisa.agtext) = '. $dados['ModIvrPesquisa']["agtext"];
            }
        }
        if (!empty($dados['ModIvrPesquisa']['agtid'])) {
            $conditions["ModIvrPesquisa.agtid"] = $dados['ModIvrPesquisa']["AGTID"];
        }
        if (!empty($dados['ModIvrPesquisa']['status']) || isset($dados['ModIvrPesquisa']['status']) ) {
            if($dados['ModIvrPesquisa']['status'] == 1)
                $conditions["ModIvrPesquisa.status"] = 0;
            if($dados['ModIvrPesquisa']['status'] == 2)
                $conditions["ModIvrPesquisa.status"] = 1;
        }
        if (!empty($dados['ModIvrPesquisa']['score']) || isset($dados['ModIvrPesquisa']['score']) && $dados['ModIvrPesquisa']['score'] === '0') {
            if($dados['ModIvrPesquisa']['score'] == '99') {
                $conditions["ModIvrPesquisa.score"] = '0';
                $conditions["ModIvrPesquisa.status"] = '0';
            }else {
                $conditions["ModIvrPesquisa.status"] = '1';
                $conditions["ModIvrPesquisa.score"] = $dados['ModIvrPesquisa']["score"];
            }
        }
        if (!empty($dados['ModIvrPesquisa']['startivr']) || !empty($dados['ModIvrPesquisa']['endivr'])) {
            $conditions['ModIvrPesquisa.startivr >'] = AppModel::dateToDbDate2($dados['ModIvrPesquisa']['startivr']).' 00:00:00';
            $conditions['ModIvrPesquisa.endivr <'] = AppModel::dateToDbDate2($dados['ModIvrPesquisa']['endivr']).' 23:59:59';
        }
        if (!empty($dados['ModIvrPesquisa']['startq']) || !empty($dados['ModIvrPesquisa']['endq'])) {
            $conditions['ModIvrPesquisa.startq >'] = AppModel::dateToDbDate2($dados['ModIvrPesquisa']['startq']).' 00:00:00';
            $conditions['ModIvrPesquisa.endq <'] = AppModel::dateToDbDate2($dados['ModIvrPesquisa']['endq']).' 23:59:59';
        }
        if (!empty($dados['ModIvrPesquisa']['departamento'])) {
            if($dados['ModIvrPesquisa']['departamento'] == '99') {
                $conditions["Departamento.codigo"] = null;
            } else {
                $conditions["Departamento.codigo"] = $dados['ModIvrPesquisa']["departamento"];
            }
        }
        return $conditions; 
	}

    public function countAvaliacaoUra($options = array(),$method_find = 'count') {
        $fields =array('(COUNT( distinct descricao)) as [count]');
        $sql_retornos = $this->find('count', array(
            'conditions' => $options['conditions'],  
            'recursive' => $options['recursive'],
            'joins' => $options['joins'],
            'group' => $options['group'],
            'fields' => $fields,
           ));        
        return $sql_retornos;
    }

    public function paginateCount( $conditions = null, $recursive = 0, $extra = array()) {
        if( isset( $extra['method'] ) && $extra['method'] == 'pesquisa_satisfacao' ){
            $options['conditions'] = ($conditions != null ? $conditions : null );
            $options['recursive'] = ($recursive != null ? $recursive : null );
            $options['joins'] = $extra['joins'];
            $options['group'] = $extra['group'];
            return $this->countAvaliacaoUra($options, 'count');
        }
        $joins = null;
        if (isset($extra['joins']))
            $joins = $extra['joins'];
        return $this->find('count', compact('conditions', 'recursive', 'joins'));
    }

/*
Informações da Tabela
LOGID - Chave primeira - ID do Registro
STARTIVR - Data/hora do INICIO do registro da ligação no momento da interação automatizada (URA)
ENDIVR - Data/hora do FIM do registro da ligação no momento da interação automatizada (URA)
http://pt.wikipedia.org/wiki/Interactive_voice_response 

STARTQ - Data/hora do INICIO na Pesquisa (momento da avaliação)
ENDQ - Data/hora do FIM da Pesquisa (momento da avaliação)

OANI - Numero de A Original  - Número de identificação automatica (Automatic Identification Number)
ODNIS - Numero de B Original - Número pelo qual o usuario tentou realizar a ligação
http://technet.microsoft.com/pt-br/library/cc738447%28v=ws.10%29.aspx

OTRKID - ID do Tronco Original
QUEUE - Piloto da Fila de Atendimento
AGTEXT - Ramal do Agente
AGTID - ID do Agente
STATUS - 0: Pesquisa Nao Avaliada, 1: Pesquisa Avaliada
SCORE - Nota / Avaliação do Cliente
*/

}
