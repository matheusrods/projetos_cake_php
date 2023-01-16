<?php
class RiscoExameAplicados extends AppModel {

	var $name = 'RiscoExameAplicados';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'grupos_economicos_clientes';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	
	// public $belongsTo = array(
	// 	'ClienteFuncionario' => array(
	// 		'className'    => 'ClienteFuncionario',
	// 		'foreignKey'    => 'codigo_cliente_funcionario'
	// 		),
	// 	'MultiEmpresa' => array(
	// 		'className'    => 'MultiEmpresa',
	// 		'foreignKey'    => 'codigo_empresa'
	// 		),
	// 	);

	// var $validate = array(
	// 	'codigo_cliente' => array(
	// 		'rule' => 'notEmpty',
	// 		'message' => 'Informe o Cliente',
	// 		'required' => true
	// 	)
	// );
	const SIM = 1;
	const NAO = 0;

	const TIPO_PCMSO = 1;
	const TIPO_PPRA = 2;

	/**
	 * [converteFiltroEmCondition description]
	 * 
	 * metodo para fazer os filtros do where na query
	 * 
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function converteFiltroEmCondition($data) 
	{
        $conditions = array();

		if (!empty( $data['codigo_cliente'])) {
			$conditions['GrupoEconomico.codigo_cliente'] = $data['codigo_cliente'];
        }

        if (!empty( $data['codigo_cliente_alocacao'])) {
			$conditions['GruposEconomicosClientes.codigo_cliente'] = $data['codigo_cliente_alocacao'];
        }

		if(!empty($data['codigo_setor'])){
			$conditions['Setor.codigo'] = $data['codigo_setor'];
		}

		if(!empty($data['codigo_cargo'])){
			$conditions['Cargo.codigo'] = $data['codigo_cargo'];
		}

		if(!empty($data['codigo_tipo'])){
			$conditions['RiscoExameAplicados.codigo_tipo'] = $data['codigo_tipo'];
		}

		if(!empty($data['tomadores'])){			
			$conditions['Cliente.e_tomador'] = $data['tomadores'];
		}

		if(!empty($data['codigo_funcionario'])){
			$conditions['Funcionario.codigo'] = $data['codigo_funcionario'];
		}

		if(!empty($data['nome_funcionario'])){
			$conditions['Funcionario.nome LIKE'] = $data['nome_funcionario'];
		}

		//retorn os dados para a metodo que chamou
        return $conditions;

    }//fim convertFiltrosConditions
	

	function carregarCombos($codigo_cliente = null) 
	{
		$retorno = array();

		//instancia as models
		$this->Cliente = & ClassRegistry::init('Cliente');
		$this->Cargo = & ClassRegistry::init('Cargo');
		$this->Setor = & ClassRegistry::init('Setor');

		$retorno['unidades'] = $this->Cliente->lista_por_cliente($codigo_cliente);
		$retorno['cargos'] = $this->Cargo->lista_por_cliente($codigo_cliente);
		$retorno['setores'] = $this->Setor->lista_por_cliente($codigo_cliente);
		$retorno['tipos'] = $this->carregarTipos();
		$retorno['tomadores'] = $this->carregarTomadores();

		return $retorno;
	}
	

	public function carregarTipos() 
	{	
		return array(
			self::TIPO_PCMSO => "PCMSO",
			self::TIPO_PPRA => "PPRA"
		);
	}


	public function carregarTomadores() 
	{	
		return array(
			'' => 'Todos',
			self::SIM => "Sim",
			self::NAO => "Não"			
		);
	}

	/**
	 * [queryPCMSO description]
	 * 
	 * query para pegar os dados da aplicacao de exames
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function queryPCMSO($conditions,$limit=true)
	{
		// debug($conditions);
		
		//monta os joins da query
        $joins = array(
            array(
                'table' => 'RHHealth.dbo.aplicacao_exames',
                'alias' => 'AplicacaoExame',
                'type' => 'INNER',
                'conditions' => array('Cliente.codigo = AplicacaoExame.codigo_cliente')
            ),                
            array(
                'table' => 'RHHealth.dbo.grupos_economicos_clientes',
                'alias' => 'GruposEconomicosClientes',
                'type' => 'INNER',
                'conditions' => array('GruposEconomicosClientes.codigo_cliente = AplicacaoExame.codigo_cliente_alocacao')
            ),
            array(
                'table' => 'RHHealth.dbo.grupos_economicos',
                'alias' => 'GrupoEconomico',
                'type' => 'INNER',
                'conditions' => array('GruposEconomicosClientes.codigo_grupo_economico = GrupoEconomico.codigo ')
            ),
            array(
                'table' => 'RHHealth.dbo.setores',
                'alias' => 'Setor',
                'type' => 'INNER',
                'conditions' => array('Setor.codigo = AplicacaoExame.codigo_setor')
            ),
            array(
                'table' => 'RHHealth.dbo.cargos',
                'alias' => 'Cargo',
                'type' => 'INNER',
                'conditions' => array('Cargo.codigo = AplicacaoExame.codigo_cargo')
            ),
            array(
                'table' => 'RHHealth.dbo.exames',
                'alias' => 'Exames',
                'type' => 'INNER',
                'conditions' => array('Exames.codigo = AplicacaoExame.codigo_exame')
            ),
            array(
                'table' => 'RHHealth.dbo.funcionarios',
                'alias' => 'Funcionario',
                'type' => 'LEFT',
                'conditions' => array('Funcionario.codigo = AplicacaoExame.codigo_funcionario')
            ),
            array(
                'table' => 'RHHealth.dbo.cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'LEFT',
                'conditions' => array('Funcionario.codigo = ClienteFuncionario.codigo_funcionario')
            ),
            array(
                'table' => 'RHHealth.dbo.funcionario_setores_cargos',
                'alias' => 'FuncionarioSetoresCargos',
                'type' => 'LEFT',
                'conditions' => array('FuncionarioSetoresCargos.codigo_cliente_funcionario = ClienteFuncionario.codigo AND FuncionarioSetoresCargos.codigo_setor = AplicacaoExame.codigo_setor AND FuncionarioSetoresCargos.codigo_cargo = AplicacaoExame.codigo_cargo AND FuncionarioSetoresCargos.codigo_cliente_alocacao = AplicacaoExame.codigo_cliente_alocacao')
            ),
            array(
                'table' => 'RHHealth.dbo.clientes_setores_cargos',
                'alias' => 'ClienteSetorCargo',
                'type' => 'INNER',
                'conditions' => array('ClienteSetorCargo.codigo_setor = Setor.codigo AND ClienteSetorCargo.codigo_cargo = Cargo.codigo AND AplicacaoExame.codigo_cliente_alocacao = ClienteSetorCargo.codigo_cliente_alocacao AND (ClienteSetorCargo.ativo = 1 OR ClienteSetorCargo.ativo IS NULL)')//ajuste para o chamado CDCT-428, trazer somente hierarquias ativas
            ),
        );

        //monta os dados do select
        $fields = array(
			'Cliente.codigo as codigo_unidade', 
			'Cliente.nome_fantasia as unidade_nome_fantasia', 
            'Setor.codigo as codigo_setor',
            'Setor.descricao as setor_descricao',
            'Cargo.codigo as codigo_cargo',
            'Cargo.descricao as cargo_descricao',
            'Funcionario.nome as funcionario_nome',
            'Funcionario.cpf as funcionario_cpf',
            'ClienteFuncionario.matricula as funcionario_matricula',
            'Exames.descricao as exame_descricao',
            'AplicacaoExame.exame_admissional as exame_admissional', 
            'AplicacaoExame.exame_periodico as exame_periodico',
            'AplicacaoExame.exame_demissional as exame_demissional', 
            'AplicacaoExame.exame_retorno as exame_retorno', 
            'AplicacaoExame.exame_mudanca as exame_mudanca', 
            'AplicacaoExame.exame_monitoracao as exame_monitoracao',
            'AplicacaoExame.periodo_meses as periodo_meses',
            'AplicacaoExame.periodo_idade as periodo_idade',
            'AplicacaoExame.qtd_periodo_idade as qtd_periodo_idade',
            'AplicacaoExame.periodo_idade_2 as periodo_idade_2',
            'AplicacaoExame.qtd_periodo_idade_2 as qtd_periodo_idade_2',
            'AplicacaoExame.periodo_idade_3 as periodo_idade_3',
            'AplicacaoExame.qtd_periodo_idade_3 as qtd_periodo_idade_3',
            'AplicacaoExame.periodo_idade_4 as periodo_idade_4',
            'AplicacaoExame.qtd_periodo_idade_4 as qtd_periodo_idade_4'         
        );
        $order = 'Cliente.codigo';

        $dados = array(
                    'conditions' => $conditions,
                    'joins' => $joins,
                    'fields' => $fields,
                    'limit' => 50,
                    'order' => $order
                );

        if(!$limit) {
        	unset($dados['limit']);
        }

			
		return $dados;
	}//fim querypcmso


	/**
	 * [queryPCMSO description]
	 * 
	 * query para pegar os dados do grupo de exposição
	 * 
	 * @param  [type] $conditions [description]
	 * @return [type]             [description]
	 */
	public function queryPPRA($conditions, $limit = true)
	{
		//monta os joins da query
        $joins = array(
            array(
                'table' => 'RHHealth.dbo.grupos_economicos_clientes',
                'alias' => 'GruposEconomicosClientes',
                'type' => 'INNER',
                'conditions' => array('GruposEconomicosClientes.codigo_cliente = Cliente.codigo')
            ),
            array(
                'table' => 'RHHealth.dbo.grupos_economicos',
                'alias' => 'GrupoEconomico',
                'type' => 'INNER',
                'conditions' => array('GruposEconomicosClientes.codigo_grupo_economico = GrupoEconomico.codigo ')
            ),
            array(
                'table' => 'RHHealth.dbo.clientes_setores',
                'alias' => 'ClienteSetor',
                'type' => 'INNER',
                'conditions' => array('Cliente.codigo = ClienteSetor.codigo_cliente_alocacao')
            ),                
            array(
                'table' => 'RHHealth.dbo.setores',
                'alias' => 'Setor',
                'type' => 'INNER',
                'conditions' => array('Setor.codigo = ClienteSetor.codigo_setor')
            ),
             array(
                'table' => 'RHHealth.dbo.grupo_exposicao',
                'alias' => 'GrupoExposicao',
                'type' => 'INNER',
                'conditions' => array('GrupoExposicao.codigo_cliente_setor = ClienteSetor.codigo')
            ),
            array(
                'table' => 'RHHealth.dbo.cargos',
                'alias' => 'Cargo',
                'type' => 'INNER',
                'conditions' => array('Cargo.codigo = GrupoExposicao.codigo_cargo')
            ),
            array(
                'table' => 'RHHealth.dbo.grupos_exposicao_risco',
                'alias' => 'GrupoExposicaoRisco',
                'type' => 'INNER',
                'conditions' => array('GrupoExposicao.codigo = GrupoExposicaoRisco.codigo_grupo_exposicao')
            ),
            array(
                'table' => 'RHHealth.dbo.riscos',
                'alias' => 'Risco',
                'type' => 'INNER',
                'conditions' => array('Risco.codigo = GrupoExposicaoRisco.codigo_risco')
            ),
            array(
                'table' => 'RHHealth.dbo.funcionarios',
                'alias' => 'Funcionario',
                'type' => 'LEFT',
                'conditions' => array('Funcionario.codigo = GrupoExposicao.codigo_funcionario')
			),
			array(
                'table' => 'RHHealth.dbo.cliente_funcionario',
                'alias' => 'ClienteFuncionario',
                'type' => 'LEFT',
                'conditions' => array('Funcionario.codigo = ClienteFuncionario.codigo_funcionario')
            ),
            array(
                'table' => 'RHHealth.dbo.funcionario_setores_cargos',
                'alias' => 'FuncionarioSetoresCargos',
                'type' => 'LEFT',
                'conditions' => array('FuncionarioSetoresCargos.codigo_setor = ClienteSetor.codigo_setor AND FuncionarioSetoresCargos.codigo_cargo = GrupoExposicao.codigo_cargo AND FuncionarioSetoresCargos.codigo_cliente_alocacao = ClienteSetor.codigo_cliente_alocacao AND FuncionarioSetoresCargos.codigo_cliente_funcionario = ClienteFuncionario.codigo')
            ),
            array(
                'table' => 'RHHealth.dbo.clientes_setores_cargos',
                'alias' => 'ClienteSetorCargo',
                'type' => 'INNER',
                'conditions' => array('ClienteSetorCargo.codigo_setor = Setor.codigo AND ClienteSetorCargo.codigo_cargo = Cargo.codigo AND Cliente.codigo = ClienteSetorCargo.codigo_cliente_alocacao AND (ClienteSetorCargo.ativo = 1 OR ClienteSetorCargo.ativo IS NULL)')//ajuste para o chamado CDCT-428, trazer somente hierarquias ativas
            ),
        );

        //monta os dados do select
        $fields = array(
			'Cliente.codigo as codigo_unidade', 
            'Cliente.nome_fantasia as unidade_nome_fantasia', 
            'Setor.codigo as codigo_setor',
            'Setor.descricao as setor_descricao',
            'Cargo.codigo as codigo_cargo',
            'Cargo.descricao as cargo_descricao',
            'Funcionario.nome as funcionario_nome',
            'Funcionario.cpf as funcionario_cpf',
            'ClienteFuncionario.matricula as funcionario_matricula',
            'GrupoExposicao.codigo as codigo_grupo_exposicao',
            'Risco.nome_agente as risco_descricao',
            '(select classificacao_descricao from grupo_exposicao_tipo_grupo_exposicao_classificacao where codigo_grupo_exposicao = GrupoExposicao.codigo and codigo_tipo_grupo_exposicao_classificacao = 1 ) as insalubridade',
            "(select 'Sim' from grupo_exposicao_tipo_grupo_exposicao_classificacao where codigo_grupo_exposicao = GrupoExposicao.codigo and codigo_tipo_grupo_exposicao_classificacao = 2 ) as periculosidade",
            "(select 'Sim' from grupo_exposicao_tipo_grupo_exposicao_classificacao where codigo_grupo_exposicao = GrupoExposicao.codigo and codigo_tipo_grupo_exposicao_classificacao = 3 )  as aposentadoria"
        );
        $order = 'Cliente.codigo';

        $dados = array(
                    'conditions' => $conditions,
                    'joins' => $joins,
                    'fields' => $fields,
                    'limit' => 50,
                    'order' => $order
                );

        if(!$limit) {
        	unset($dados['limit']);
        }
        
		return $dados;
	}//fim query ppra


   

}
