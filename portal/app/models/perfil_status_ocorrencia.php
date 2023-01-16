<?php

class PerfilStatusOcorrencia extends AppModel {

    var $name = 'PerfilStatusOcorrencia';
    var $tableSchema = 'monitora';
    var $databaseTable = 'dbMonitora';
    var $useTable = 'perfis_status_ocorrencias';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $belongsTo = array(
        'StatusOcorrencia' => array(
            'className' => 'StatusOcorrencia',
            'foreignKey' => 'codigo_status_ocorrencia'
        )
    );
    
    const OBJ_OPERADOR_BUONNYSAT = 1;
    const OBJ_ACIONAMENTO_BUONNYSAT = 2;
    const OBJ_OPERADOR_PRONTA_RESPOSTA = 3;
    
    function statusPorPerfil($codigo) {
        $condicoes = array(
            'fields' => array('StatusOcorrencia.codigo', 'StatusOcorrencia.descricao'),
            'conditions' => array('PerfilStatusOcorrencia.codigo_perfil' => $codigo, 'PerfilStatusOcorrencia.ativo' => 1),
            'recursive' => 1
        );
        $retorno = $this->find('list', $condicoes);
        return $retorno;
    }
    
    function statusPerfilOperadorBuonnySat() {
        $this->bindModel(array(
                'belongsTo' => array(
                    'Perfil' => array(
                        'className' => 'Perfil',
                        'foreignKey' => 'codigo_perfil',
                    )
                )
            )
        );
        $condicoes = array(
            'fields' => array('StatusOcorrencia.codigo', 'StatusOcorrencia.descricao'),
            'conditions' => array('Perfil.descricao' => 'OPERADOR BUONNYSAT', 'PerfilStatusOcorrencia.ativo' => 1),
            'recursive' => 1
        );
        $retorno = $this->find('list', $condicoes);
        $this->unbindModel(array('belongsTo' => array('Perfil')));
        return $retorno;
    }
    
    function statusPorObjeto($codigo_obj) {
        if ($codigo_obj == null)
            return false;
        $condicoes = array(
            'fields' => array('StatusOcorrencia.codigo', 'StatusOcorrencia.descricao'),
            'conditions' => array('PerfilStatusOcorrencia.codigo_objeto ' => $codigo_obj, 'PerfilStatusOcorrencia.ativo' => 1),
            'recursive' => 1
        );
        $retorno = $this->find('list', $condicoes);
        return $retorno;
    }
    
    function statusObjetoOperadorBuonnySat() {
        return $this->StatusPorObjeto(PerfilStatusOcorrencia::OBJ_OPERADOR_BUONNYSAT);
    }
    
    function constantes() {
        return array(
            1 => 'obj_operador-buonnysat',
            2 => 'obj_acionamento-buonnysat',
            3 => 'obj_operador-pronta-resposta',
        );
    }
    
}