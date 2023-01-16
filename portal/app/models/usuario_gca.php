<?php 
class UsuarioGca extends AppModel {

    public $name = 'UsuarioGca';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'usuario_gca';	
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_usuario_gca'));

	/**
	 * CONSTANTES PARA AJUDAR NO RELATORIO DE BAIXA DE EXAMES
	 */ 
	const AGRP_UNIDADE = 1;
	const AGRP_SETOR = 2;
	const AGRP_CARGO = 3;
	const AGRP_RESULTADO = 4;

	public function lista_atendimento($codigo_usuario)
	{

		$fields = array(
			'UsuarioGca.codigo',
			'UsuarioGca.codigo_usuario',
			'UsuarioGca.codigo_usuario_grupo_covid',
			'UsuarioGca.cpf',
			'UsuarioGca.codigo_usuario_inclusao',
			'UsuarioGca.data_inclusao',
			'UsuarioGca.codigo_usuario_alteracao',
			'UsuarioGca.data_alteracao',
			'UsuarioGca.ativo',
			'UsuarioGca.volta_grupo',
			'UsuarioGca.solicita_exame',
			'UsuarioGca.dias_aguardar_resultado',
			'UsuarioGca.data_aguardar_resultado',
			'UsuarioGca.data_resultado_exame',
			'UsuarioGca.resultado_exame',
			'UsuarioGca.afastamento_positivado',
			'UsuarioGca.afastamento_sintomas',
			'UsuarioGca.data_fim_afastamento',
			'CONVERT(varchar(10), UsuarioGca.data_fim_afastamento,23) AS dt_fim_afastamento',
			'UsuarioGca.controle_data_afastamento',
			'UsuarioGca.data_fim_obito',
			'UsuarioGca.codigo_atestado',
			'UsuarioGca.observacao',
			'UsuarioGcaAnexos.anexo',
			'Usuario.nome'
		);

		$join = array(
			array(
                'table' => 'RHHealth.dbo.usuario',
                'alias' => 'Usuario',
                'type' => 'INNER',
                'conditions' => array('UsuarioGca.codigo_usuario_inclusao = Usuario.codigo')
            ),
			array(
                'table' => 'RHHealth.dbo.usuario_gca_anexos',
                'alias' => 'UsuarioGcaAnexos',
                'type' => 'LEFT',
                'conditions' => array('UsuarioGca.codigo = UsuarioGcaAnexos.codigo_usuario_gca')
            )
		);

		$dados = $this->find('all',array(
			'fields' => $fields,
			'joins' => $join,
			'conditions' => array('UsuarioGca.codigo_usuario' => $codigo_usuario)
		));

		return $dados;

	}

	/**
	 * Pega o tipo de agrupamento
	 */ 
	public function tiposAgrupamento() {
		return array(
			self::AGRP_RESULTADO => "por Resultado",
			self::AGRP_UNIDADE => "por Unidade",
			self::AGRP_SETOR => "por Setor",
			self::AGRP_CARGO => "por Cargo",
		);
	}

	/**
	 * Metodo para converter os parametros passados em condições para o banco de dados
	 */ 
	public function converteFiltrosEmConditions($data) {

		$conditions = array();

		if (isset($data['codigo_cliente']) && !empty($data['codigo_cliente'])) {

			$GrupoEconomicoCliente = ClassRegistry::init('GrupoEconomicoCliente');
			//pega o grupo economico
			$gec = $GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $data['codigo_cliente'])));
			//seta a conditions
			$conditions['GrupoEconomicoCliente.codigo_grupo_economico'] = $gec['GrupoEconomicoCliente']['codigo_grupo_economico'];

			// $conditions['OR'] = array(
			// 							'GrupoEconomico.codigo_cliente' => $data['codigo_cliente'],
			// 							'GrupoEconomicoCliente.codigo_cliente' => $data['codigo_cliente']
			// 						);

		}
	
		if (isset($data['codigo_cliente_alocacao']) && !empty($data['codigo_cliente_alocacao'])) {
			$conditions['FuncionarioSetorCargo.codigo_cliente_alocacao'] = $data['codigo_cliente_alocacao'] == -1 ? null : $data['codigo_cliente_alocacao'];
		}
		
		if (isset($data['codigo_funcionario']) && !empty($data['codigo_funcionario'])) {
			$conditions['ClienteFuncionario.codigo_funcionario'] = $data['codigo_funcionario'] == -1 ? null : $data['codigo_funcionario'];
		}
	
		if (isset($data['codigo_setor']) && !empty($data['codigo_setor'])) {
			$conditions['FuncionarioSetorCargo.codigo_setor'] = $data['codigo_setor'] == -1 ? null : $data['codigo_setor'];
		}
	
		if (isset($data['codigo_cargo']) && !empty($data['codigo_cargo'])) {
			$conditions['FuncionarioSetorCargo.codigo_cargo'] = $data['codigo_cargo'] == -1 ? null : $data['codigo_cargo'];
		}

		if($data['tipo_periodo'] == '1') { //positivo
			$conditions['UsuarioGca.resultado_exame'] = 1;
		} elseif($data['tipo_periodo'] == '2') { //negativo
			$conditions['UsuarioGca.resultado_exame'] = 2;
		}

		if (isset($data['data_inicio']) && !empty($data['data_inicio'])) {
			$conditions['UsuarioGca.data_resultado_exame >='] = AppModel::dateToDbDate($data['data_inicio']);
		}

		if (isset($data['data_fim']) && !empty($data['data_fim'])) {
			$conditions['UsuarioGca.data_resultado_exame <='] = AppModel::dateToDbDate($data['data_fim']);
		}

		//filtro de cidade unidade
		if(isset($data['codigo_cidade_unidade']) && !empty($data['codigo_cidade_unidade'])) {
			$conditions['ClienteEndereco.cidade'] = $data['codigo_cidade_unidade'];
		}//fim filtro cidade unidade

		//filtro de estado unidade
		if(isset($data['codigo_estado_unidade']) && !empty($data['codigo_estado_unidade'])) {
			$conditions['ClienteEndereco.estado_abreviacao'] = $data['codigo_estado_unidade'];
		}//fim filtro estado unidade		

		//filtro de cidade fornecedor
		if(isset($data['codigo_cidade_fornecedor']) && !empty($data['codigo_cidade_fornecedor'])) {
			$conditions['FornecedorEndereco.cidade'] = $data['codigo_cidade_fornecedor'];
		}//fim filtro cidade unidade

		//filtro de estado unidade
		if(isset($data['codigo_estado_fornecedor']) && !empty($data['codigo_estado_fornecedor'])) {
			$conditions['FornecedorEndereco.estado_descricao'] = $data['codigo_estado_fornecedor'];
		}//fim filtro estado unidade		

		//filtro de matricula
		if(isset($data['matricula']) && !empty($data['matricula'])) {
			$conditions['ClienteFuncionario.matricula'] = $data['matricula'];
		}//fim filtro estado unidade


		return $conditions;

	}//fim converteFiltrosEmConditions

	/**
	 * Metodo para trazer o resultado para a montagem do relatorio sitético com gráfico
	 * 
	 */ 
	public function resultado_exame_sintetico($agrupamento, $conditions) 
	{
		//metodo que prepara a query
		$query_analitica = $this->resultado_exame_analitico('sql', compact('conditions'));
		// debug($query_analitica);exit;
		
		//verifica qual agrupamento
		switch ($agrupamento) {
			case self::AGRP_UNIDADE: //agrupar por unidade
				$fields = array(
					'unidade_codigo AS codigo',
					'unidade_nome_fantasia AS descricao',
					'COUNT(resultado_exame) AS quantidade',
				);
				$group = array(
					'unidade_codigo',
					'unidade_nome_fantasia',
				);
				break;
			case self::AGRP_SETOR: //agrupado por setor
				$fields = array(
					'setor_codigo AS codigo',
					'setor_descricao AS descricao',
					'COUNT(resultado_exame) AS quantidade',
				);
				$group = array(
					'setor_codigo',
					'setor_descricao',
				);
				break;
			case self::AGRP_CARGO: //agrupado pelo cargo
				$fields = array(
					'cargo_codigo AS codigo',
					'cargo_descricao AS descricao',
					'COUNT(resultado_exame) AS quantidade',
				);
				$group = array(
					'cargo_codigo',
					'cargo_descricao',
				);
				break;
			case self::AGRP_RESULTADO: //agrupado pelo resultado do exame
				$fields = array(
					'resultado_exame_codigo AS codigo',
					'resultado_exame AS descricao',
					'COUNT(resultado_exame) AS quantidade',
				);
				$group = array(
					'resultado_exame_codigo',
					'resultado_exame',
				);
				break;
		}
		$order = array('COUNT(resultado_exame) DESC');
		$dbo = $this->getDataSource();
		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$query_analitica})",
				'alias' => 'analitico',
				'schema' => null,
				'alias' => 'sintetico',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => null,
				'order' => $order,
				'group' => $group
				), $this
			);

		// print $query;exit;

		return $this->query($query);

	}//fim sintetico

	/**
	 * metodo para pegar os dados no detalhe da consulta e montar o grid de relatorio analitico da resultados de exames do covid
	 */ 
	public function resultado_exame_analitico($type = null, $options) {

		//realiza os relacionamentos da para montar a query		
		$joins = array(
				array(
					'table' => 'RHHealth.dbo.funcionarios',
					'alias' => 'Funcionario',
					'type' => 'INNER',
					'conditions' => array('UsuarioGca.cpf = Funcionario.cpf')
					),
				array(
					'table' => 'RHHealth.dbo.cliente_funcionario',
					'alias' => 'ClienteFuncionario',
					'type' => 'INNER',
					'conditions' => array('Funcionario.codigo = ClienteFuncionario.codigo_funcionario AND ClienteFuncionario.ativo <> 0')
					),
				array(
					'table' => 'RHHealth.dbo.funcionario_setores_cargos',
					'alias' => 'FuncionarioSetorCargo',
					'type' => 'INNER',
					'conditions' => array('FuncionarioSetorCargo.codigo_cliente_funcionario = ClienteFuncionario.codigo AND FuncionarioSetorCargo.data_fim IS NULL')
					),
				array(
					'table' => 'RHHealth.dbo.setores',
					'alias' => 'Setor',
					'type' => 'INNER',
					'conditions' => array('FuncionarioSetorCargo.codigo_setor = Setor.codigo')
					),
				array(
					'table' => 'RHHealth.dbo.cargos',
					'alias' => 'Cargo',
					'type' => 'INNER',
					'conditions' => array('FuncionarioSetorCargo.codigo_cargo = Cargo.codigo')
					),
				array(
					'table' => 'RHHealth.dbo.cliente',
					'alias' => 'Unidade',
					'type' => 'LEFT',
					'conditions' => array('FuncionarioSetorCargo.codigo_cliente_alocacao = Unidade.codigo')
					),
				array(
					'table' => 'RHHealth.dbo.cliente_endereco',
					'alias' => 'ClienteEndereco',
					'type' => 'INNER',
					'conditions' => array('ClienteEndereco.codigo = (SELECT TOP 1 codigo FROM RHHealth.dbo.cliente_endereco WHERE codigo_cliente = Unidade.codigo)' )
					),
				array(
					'table' => 'RHHealth.dbo.grupos_economicos_clientes',
					'alias' => 'GrupoEconomicoCliente',
					'type' => 'INNER',
					'conditions' => array('GrupoEconomicoCliente.codigo_cliente = FuncionarioSetorCargo.codigo_cliente_alocacao')
					),
				array(
					'table' => 'RHHealth.dbo.grupos_economicos',
					'alias' => 'GrupoEconomico',
					'type' => 'INNER',
					'conditions' => array('GrupoEconomicoCliente.codigo_grupo_economico = GrupoEconomico.codigo')
					),
				);

		//campos
		$fields = array(
			'Unidade.codigo AS unidade_codigo',
			'Unidade.nome_fantasia AS unidade_nome_fantasia',
			'Unidade.razao_social AS unidade_razao_social',
			'Funcionario.nome AS funcionario',
			'Funcionario.cpf AS cpf',
			'Setor.codigo AS setor_codigo',
			'Setor.descricao AS setor_descricao',
			'Cargo.codigo AS cargo_codigo',
			'Cargo.descricao AS cargo_descricao',
			'ClienteFuncionario.matricula AS matricula',
			'UsuarioGca.resultado_exame AS resultado_exame_codigo',
			"(CASE WHEN UsuarioGca.resultado_exame = 1 THEN 'Positivo' ELSE 'Negativo' END) AS resultado_exame",
			'CONVERT(VARCHAR, UsuarioGca.data_resultado_exame, 120) AS data_resultado',
		);

		$group = array(
			'Unidade.codigo',
	       	'Unidade.nome_fantasia',
			'Unidade.razao_social',
			'Funcionario.nome',
			'Funcionario.cpf',
			'Setor.codigo',
			'Setor.descricao',
			'Cargo.codigo',
			'Cargo.descricao',
			'ClienteFuncionario.matricula',
			'UsuarioGca.resultado_exame',
			'UsuarioGca.data_resultado_exame'
		);
		
		if(!empty($type) ){
			//pega as conditions que foi montada
			$conditions = $options['conditions'];
		}

		if(empty($type) ){
			$dados = array(
            	'conditions' => $options,
            	'joins' => $joins,
            	'fields' => $fields,
            	'group' => $group
        	);
		}        

        if(!empty($type) ){
        	//retorna o resultado que foi solicitado all, sql, first
			return $this->find($type, compact('fields', 'joins', 'conditions','group'));
        }
        else{
			return $dados;
        }
	} //fim analitico


}