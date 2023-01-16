<?php
class CronogramaAcao extends AppModel {

	var $name = 'CronogramaAcao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'cronogramas_acoes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
    var $belongsTo = array(
        'ClienteMatriz' => array(
            'className' => 'Cliente',
            'foreignKey'    => 'codigo_cliente_matriz'
        ),
        'ClienteUnidade' => array(
            'className' => 'Cliente',
            'foreignKey'    => 'codigo_cliente_unidade'
        ),
        'Setor' => array(
            'className' => 'Setor',
            'foreignKey'    => 'codigo_setor'
        ),
        'TipoAcao' => array(
            'className' => 'TipoAcao',
            'foreignKey'    => 'codigo_tipo_acao'
        ),
    );

	var $validate = array(
	    'codigo_cliente_matriz' => array(
	        'rule' => 'notEmpty',
            'message' => 'Especifique o cliente matriz!',
        ),
		'codigo_cliente_unidade' => array(
		    'rule' => 'notEmpty',
            'message' => 'Especifique o cliente!',
        ),
		'codigo_setor' => array(
		    'rule' => 'notEmpty',
            'message' => 'Selecione um setor!',
        ),
        'codigo_tipo_acao' => array(
            'rule' => 'notEmpty',
            'message' => 'Selecione uma ação!',
        ),
	);

	public function submeter(array $data){
	    return self::inclui($data);
    }

    private function valida_dados(array $data){
	    $this->set($data);
	    if(!$this->validates()){
	        $errors = $this->invalidFields();
            throw new Exception(join("; ", $errors));
        }
    }

    private function deleta_atuais($data){
        $codigo_cliente_matriz = $data['CronogramaAcao'][key($data['CronogramaAcao'])]['codigo_cliente_matriz'];
        $codigo_cliente_unidade = $data['CronogramaAcao'][key($data['CronogramaAcao'])]['codigo_cliente_unidade'];
        $conditions = array(
            'CronogramaAcao.codigo_cliente_matriz' => $codigo_cliente_matriz,
            'CronogramaAcao.codigo_cliente_unidade' => $codigo_cliente_unidade
        );
        $return = $this->deleteAll($conditions);

        if(!$return)
            throw new Exception("Erro ao tentar deletar cronograma ação: ".join("; ", $this->validationErrors));
        return true;
    }

    private function inclui($data){
        $this->query("BEGIN TRANSACTION;");
        try{
            self::valida_dados($data);
            self::deleta_atuais($data);

            $return = parent::incluirTodos($data['CronogramaAcao'], array('validate' => false));

            if(isset($this->validationErrors) && !empty($this->validationErrors))
                throw new Exception(join("; ", $this->validationErrors));

            $this->commit();
            return array(
                'status' => 'success',
                'message' => 'Dados inseridos com sucesso!',
                'data' => $return,
            );
        }catch(Exception $ex){
            $this->rollback();
            return array(
                'status' => 'error',
                'message' => $ex->getMessage(),
            );
        }
    }

    public function converte_filtro_em_conditions_listagem(array $data = array()){
	    $conditions = array();

	    if(isset($data['descricao']) && $data['descricao'] != '')
	        $conditions["TipoAcao.descricao LIKE '%{$data['descricao']}%'"];
        if(isset($data['classificacao']) && $data['classificacao'] != '')
            $conditions['TipoAcao.classificacao'] = $data['classificacao'];
        if(isset($data['status']) && $data['status'] != '')
            $conditions['TipoAcao.status'] = $data['status'];
	    $conditions['TipoAcao.codigo_empresa'] = (empty($data['codigo_empresa']) ? $_SESSION['Auth']['Usuario']['codigo_empresa'] : $data['codigo_empresa']);

	    return $conditions;
    }

    public function get_parametros_para_consulta(array $data = array()){
        $fields = array(
            'TipoAcao.codigo as codigo',
            'TipoAcao.descricao as descricao',
            'CASE TipoAcao.classificacao ' .
                'WHEN \'0\' THEN \'PPRA\' ' .
                'WHEN \'1\' THEN \'PCMSO\' ' .
                'ELSE \'DESCONHECIDO\' ' .
            'END as classificacao',
            'TipoAcao.status as status'
        );
        $conditions = self::converte_filtro_em_conditions_listagem($data);
        $limit = 50;
        $order = 'TipoAcao.descricao ASC';
        return compact('fields', 'conditions', 'limit', 'order');
    }

    public function get_all($codigo_cliente_matriz, $codigo_cliente_unidade){
        $conditions = array(
            'CronogramaAcao.codigo_cliente_matriz' => $codigo_cliente_matriz,
            'CronogramaAcao.codigo_cliente_unidade' => $codigo_cliente_unidade,
        );
        $data = $this->find('all', array('conditions' => $conditions, 'recursive' => true));

        if(empty($data) || is_null($data) || count($data) <= 0)
            return array();

        $CA = array('CronogramaAcao' => array());

        array_map(function($data_object) use (&$CA) {
            $CA['CronogramaAcao'][] = $data_object['CronogramaAcao'];
        }, $data);

        return $CA;
    }

    public function get_cliente_informacoes($codigo_cliente){
	    $fields = array(
            'Cliente.codigo as codigo',
            'Cliente.razao_social as razao_social',
            'ClienteEndereco.bairro as bairro',
            'ClienteEndereco.cidade as cidade',
            'ClienteEndereco.estado_descricao as estado',
            'OrdemServico.inicio_vigencia_pcmso as data_inicio_vigencia',
            'count(Funcionario.codigo) as quantidade_funcionario'
        );
	    $joins = array(
            array(
                'table' => 'cliente_endereco',
                'alias' => 'ClienteEndereco',
                'type' => 'LEFT',
                'conditions' => array(
                    'ClienteEndereco.codigo_cliente = Cliente.codigo'
                )
            ),
            array(
                'table' => 'cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'LEFT',
                'conditions' => array(
                    'ClienteFuncionario.codigo_cliente = Cliente.codigo'
                )
            ),
            array(
                'table' => 'funcionarios',
                'alias' => 'Funcionario',
                'type' => 'LEFT',
                'conditions' => array(
                    'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
                )
            ),
            array(
                'table' => 'ordem_servico',
                'alias' => 'OrdemServico',
                'type' => 'LEFT',
                'conditions' => array(
                    'OrdemServico.codigo_cliente = Cliente.codigo AND OrdemServico.status_ordem_servico != 3'//finalizado
                )
            ),
        );
	    $conditions = array('Cliente.codigo' => $codigo_cliente);
        $group = array(
            'Cliente.codigo',
            'Cliente.razao_social',
            'ClienteEndereco.bairro',
            'ClienteEndereco.cidade',
            'ClienteEndereco.estado_descricao',
            'OrdemServico.inicio_vigencia_pcmso',
        );
        $order = 'OrdemServico.inicio_vigencia_pcmso DESC';
        unset($group[6]);

	    $Cliente = ClassRegistry::init('Cliente');
	    return $Cliente->find('first', compact('fields', 'joins', 'conditions', 'group', 'order'));

    }

}
