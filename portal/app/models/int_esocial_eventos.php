<?php
class IntEsocialEventos extends AppModel {

	public $name		   	= 'IntEsocialEventos';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'int_esocial_eventos';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Containable','Loggable' => array('foreign_key' => 'codigo_int_esocial_eventos'));

	public $validate = array(
		'codigo_empresa' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe a Empresa!'
		),
		'codigo_cliente' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente!'
		),
		'codigo_int_esocial_certificado' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o certificado relacionado!'
		),
		'codigo_int_esocial_tipo_evento' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o tipo de evento relacionado!'
		),
		'codigo_int_esocial_status' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o status do evento!'
		),
		'codigo_registro_sistema' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o codigo do sistema ITHealth do evento gerado!'
		),
		'dados_evento' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe os dados do evento que está gerando!'
		),
		'ativo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe se está ativo!'
		),
	);


	public function convertFiltroEmCondition($data) 
    {
        //seta a variavel para inicio do metodo
        $conditions = array();

        //verifica se tem valores nos filtros
        if (!empty($data['codigo_cliente'])) {

            //pega as unidades do grupo economico
            $this->GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');
            $grupos_economicos = $this->GrupoEconomicoCliente->getCodigoClientesByCodigoMatriz($data['codigo_cliente']);

            // debug($grupos_economicos);exit;
            $codigo_cliente = explode(",",$grupos_economicos);

            $conditions['IntEsocialEventos.codigo_cliente'] = $codigo_cliente;
        }

        if (!empty($data['codigo_cliente_alocacao'])) {
            $conditions['FuncionarioSetorCargo.codigo_cliente_alocacao'] = $data['codigo_cliente_alocacao'];
        }

        if (!empty($data['codigo_cargo'])) {
            $conditions['Cargo.codigo'] = $data['codigo_cargo'];
        }

        if (!empty($data['codigo_setor'])) {
            $conditions['Setor.codigo'] = $data['codigo_setor'];
        }

        if (!empty($data['codigo_funcionario'])) {
            $conditions["Funcionario.codigo"] = $data['codigo_funcionario'];
        }

        if (!empty($data['nome_funcionario'])) {
            $conditions["Funcionario.nome LIKE"] = '%'. $data['nome_funcionario'] . '%';
        }

        if (!empty($data['cpf_funcionario'])) {
            $conditions["Funcionario.cpf"] = Comum::soNumero($data['cpf_funcionario']);
        }

        if (!empty($data['tipos_eventos'])) {
            $conditions['IntEsocialEventos.codigo_int_esocial_tipo_evento'] = $data['tipos_eventos'];
        }

        if (!empty($data['status'])) {
            $conditions['IntEsocialEventos.codigo_int_esocial_status'] = $data['status'];
        }

        if (!empty($data['codigo_registro_sistema'])) {
            $conditions['IntEsocialEventos.codigo_registro_sistema'] = $data['codigo_registro_sistema'];
        }
        
        //logica para as datas de filtros
        if(!empty($data["data_inicio"])) {
            $data_inicio = AppModel::dateToDbDate($data["data_inicio"].' 00:00:00');
            $data_fim = AppModel::dateToDbDate($data["data_fim"].' 23:59:59');
            $conditions [] = "(IntEsocialEventos.data_inclusao >= '". $data_inicio . "'";
        }//fim if

        if(!empty($data["data_fim"])) {
            $conditions [] = "IntEsocialEventos.data_inclusao <= '" . $data_fim . "')";
        }

        if(!empty($data["matricula"])) {
            $conditions['ClienteFuncionario.matricula like'] = '%' . $data['matricula'] . '%';
        }
        
        // die(debug($conditions));
        return $conditions;
        
    } //fim converteFiltroEmCondition

    /**
     * [getAll metodo para pegar os dados dos eventos que iram ser integrados]
     * @param  array   $conditions [description]
     * @param  boolean $pagination [description]
     * @return [type]              [description]
     */
    public function getAll(array $conditions = array(), $pagination = false, $sql_type = "sql", $export = null)
    {
	    //fields
	    $fields = array(
	        

	        "Cliente.razao_social",
	        "Cliente.nome_fantasia",
	        "Cliente.codigo_documento",
            "Cliente.codigo_documento_real",

	    	'IntEsocialEventos.codigo',
	    	'IntEsocialEventos.codigo_cliente',
	    	'IntEsocialEventos.codigo_registro_sistema',
	    	'IntEsocialEventos.data_integracao',
	    	'IntEsocialEventos.codigo_recibo',
	    	'IntEsocialEventos.retorno_esocial',

	    	'IntEsocialEventos.codigo_int_esocial_status',
	    	'IntEsocialEventos.codigo_int_esocial_tipo_evento',
            'IntEsocialEventos.codigo_integracao',
            'IntEsocialEventos.ambiente_esocial',
            'IntEsocialEventos.codigo_usuario_inclusao',
            'IntEsocialEventos.mensagem_retorno_integradora',
            'IntEsocialEventos.data_retorno_integradora',
            'IntEsocialEventos.dados_evento',
            'IntEsocialEventos.codigo_int_esocial_eventos_s3000',
            "(select codigo from int_esocial_eventos where codigo_int_esocial_eventos_s3000 = [IntEsocialEventos].codigo) AS codigo_s3000",

	    	'IntEsocialCertificado.nome_arquivo',
            'IntEsocialCertificado.ambiente_esocial',

	    	'IntEsocialStatus.descricao',
	    	'IntEsocialTipoEvento.descricao',
            'Funcionario.nome',
            'Funcionario.codigo',
            'Funcionario.cpf',
            'ClienteFuncionario.matricula',

        );
        //joins
        $joins = array(

        	array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'Cliente',
                'type' => 'INNER',
                'conditions' => 'Cliente.codigo = IntEsocialEventos.codigo_cliente',
            ),
            array(
                'table' => 'RHHealth.dbo.int_esocial_certificado',
                'alias' => 'IntEsocialCertificado',
                'type' => 'INNER',
                // 'conditions' => 'IntEsocialCertificado.codigo = IntEsocialEventos.codigo_int_esocial_certificado AND IntEsocialCertificado.codigo_cliente = IntEsocialEventos.codigo_cliente ',
                'conditions' => 'IntEsocialCertificado.codigo = IntEsocialEventos.codigo_int_esocial_certificado',
            ),
            array(
                'table' => 'RHHealth.dbo.int_esocial_tipo_evento',
                'alias' => 'IntEsocialTipoEvento',
                'type' => 'INNER',
                'conditions' => 'IntEsocialTipoEvento.codigo = IntEsocialEventos.codigo_int_esocial_tipo_evento',
            ),
            array(
                'table' => 'RHHealth.dbo.int_esocial_status',
                'alias' => 'IntEsocialStatus',
                'type' => 'INNER',
                'conditions' => 'IntEsocialStatus.codigo = IntEsocialEventos.codigo_int_esocial_status',
            ),
            array(
                'table' => 'RHHealth.dbo.funcionario_setores_cargos',
                'alias' => 'FuncionarioSetorCargo',
                'type' => 'INNER',
                'conditions' => 'FuncionarioSetorCargo.codigo = IntEsocialEventos.codigo_funcionario_setor_cargo',
            ),
            array(
                'table' => 'RHHealth.dbo.cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'INNER',
                'conditions' => 'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario',
            ),
            array(
                'table' => 'RHHealth.dbo.cliente',
                'alias' => 'ClienteAlocacao',
                'type' => 'INNER',
                'conditions' => 'ClienteAlocacao.codigo = FuncionarioSetorCargo.codigo_cliente_alocacao',
            ),  
            array(
                'table' => 'RHHealth.dbo.funcionarios',
                'alias' => 'Funcionario',
                'type' => 'INNER',
                'conditions' => 'Funcionario.codigo = ClienteFuncionario.codigo_funcionario',
            ),
            array(
                'table' => 'RHHealth.dbo.setores',
                'alias' => 'Setor',
                'type' => 'INNER',
                'conditions' => 'Setor.codigo = FuncionarioSetorCargo.codigo_setor',
            ),
            array(
                'table' => 'RHHealth.dbo.cargos',
                'alias' => 'Cargo',
                'type' => 'INNER',
                'conditions' => 'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo',
            ),
        );

        if($export){

            $joins[] = array(
                'table' => 'RHHealth.dbo.ocorrencias_int_esocial_eventos',
                'alias' => 'OcorrenciaIntEsocialEvento',
                'type' => 'LEFT',
                'conditions' => 'OcorrenciaIntEsocialEvento.codigo_int_esocial_evento = IntEsocialEventos.codigo',
            );

            $fields[] = 'OcorrenciaIntEsocialEvento.codigo';
            $fields[] = 'OcorrenciaIntEsocialEvento.codigo_ocorrencia';
            $fields[] = 'OcorrenciaIntEsocialEvento.descricao_ocorrencia';

        }

        if($pagination){
            $paginate = array(
                'fields' => $fields,
                'joins' => $joins,
                'conditions' => $conditions,
                'limit' => 50,
                'order' => "IntEsocialEventos.data_inclusao DESC"
            );
            return $paginate;
        }else{
            return $this->find($sql_type, array('joins' => $joins, 'fields' => $fields, 'conditions' => $conditions));
        }
    }//fim getAll


}//FINAL CLASS IntEsocialEventos