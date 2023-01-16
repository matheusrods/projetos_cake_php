<?php
App::import('model', 'StatusPedidoExame');

class ImportacaoPedidosExamesRegistros extends AppModel
{
	var $name  			= 'ImportacaoPedidosExamesRegistros';
	var $tableSchema	= 'dbo';
	var $databaseTable	= 'RHHealth';
	var $useTable		= 'importacao_pedidos_exames_registros';
	var $primaryKey		= 'codigo';
	var $actsAs			= array('Secure');

	const CODIGO_FORNECEDOR_ANTIGO_PRESTADOR = 8090;

	/**
	 * Função responsável pela paginação dos registros para importação dos atestados
	 * @param array $coditions Condições de filtragem de dados da query da paginação
	 * @param array $fields Campos da query da paginação
	 * @param char $order Campos de ordenação da query da paginação
	 * @param number $limit Quantidade de registros exibidos por página
	 * @param number $page Informação página atual da paginação
	 * @param boolean|null $recursive
	 * @param array|array $extra 
	 * @return array Dados de retorno query de dados dos registros importados para validação
	 */
	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array())
	{
		$joins = null;
		if (isset($extra['joins']))
			$joins = $extra['joins'];
		if (isset($extra['group']))
			$group = $extra['group'];
		if (isset($extra['extra']['importacao']) && $extra['extra']['importacao']) {
			return $this->findImportacao('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
		}
		return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
	}

	/**
	 * Função responsável pela contagem dos registros para geração da paginação de registros importados
	 * @param array|null $coditions Condições de filtragem de dados da query da paginação
	 * @param number $recursive 
	 * @param array|array $extra 
	 * @return array retorna a quantidade de registros da query de dados da paginação
	 */
	public function paginateCount($conditions = null, $recursive = 0, $extra = array())
	{
		$joins = null;
		if (isset($extra['joins']))
			$joins = $extra['joins'];
		if (isset($extra['extra']['importacao']) && $extra['extra']['importacao']) {
			return $this->findImportacao('count', compact('conditions', 'recursive', 'joins'));
		}
		return $this->find('count', compact('conditions', 'recursive', 'joins'));
	}

	/**
	 * Método de Geração de Query para Busca de Dados para validação dos dados importados do arquivo para geração dos registros de pedidos de exame
	 * @param string $findType Tipo de Consulta que será feita para a listagem de Arquivos Importados
	 * @param array $options 
	 * @return array Dados de retorno query de dados dos registros importados para validação
	 */
	public function findImportacao($findType, $options)
	{
		$Cargo 					= &ClassRegistry::init('Cargo');
		$ClienteFuncionario 	= &ClassRegistry::init('ClienteFuncionario');
		$Fornecedor				= &ClassRegistry::init('Fornecedor');
		$Funcionario 			= &ClassRegistry::init('Funcionario');
		$FuncionarioSetorCargo 	= &ClassRegistry::init('FuncionarioSetorCargo');
		$Setor 					= &ClassRegistry::init('Setor');
		$StatusImportacao 		= &ClassRegistry::init('StatusImportacao');
		$ImportacaoPedidosExame	= &ClassRegistry::init('ImportacaoPedidosExame');
		$this->bindModel(array('hasOne' => array(
			'ImportacaoPedidosExame' => array(
				'foreignKey' => false, 'conditions' =>
				'ImportacaoPedidosExame.codigo = ImportacaoPedidosExamesRegistros.codigo_importacao_pedidos_exames'
			),
			'GrupoEconomico' => array('foreignKey' => false, 'conditions' => 'GrupoEconomico.codigo = ImportacaoPedidosExame.codigo_grupo_economico'),
			'ClienteUnidade' => array('className' => 'Cliente', 'foreignKey' => false, 'conditions' => array(
				'ClienteUnidade.codigo_empresa = ImportacaoPedidosExame.codigo_empresa',
				'ClienteUnidade.razao_social LIKE ImportacaoPedidosExamesRegistros.nome_empresa',
				'ClienteUnidade.nome_fantasia LIKE ImportacaoPedidosExamesRegistros.nome_unidade',
			)),

			'Setor' => array('className' => 'Setor', 'foreignKey' => false, 'conditions' => array('Setor.descricao LIKE ImportacaoPedidosExamesRegistros.nome_setor')),
			'Cargo' => array('className' => 'Cargo', 'foreignKey' => false, 'conditions' => array('Cargo.descricao LIKE ImportacaoPedidosExamesRegistros.nome_cargo')),

			'Funcionario' => array('className' => 'Funcionario', 'foreignKey' => false, 'conditions' => array(
				'Funcionario.cpf = ImportacaoPedidosExamesRegistros.cpf'
			)),
			'ClienteFuncionario' => array('className' => 'ClienteFuncionario', 'foreignKey' => false, 'conditions' => array(
				'ClienteFuncionario.codigo_empresa = ImportacaoPedidosExame.codigo_empresa',
				'ClienteFuncionario.codigo_funcionario = Funcionario.codigo'
			)),
			'FuncionarioSetorCargo' => array('type' => 'INNER', 'className' => 'FuncionarioSetorCargo', 'foreignKey' => false, 'conditions' => array(
				'FuncionarioSetorCargo.codigo_empresa = ImportacaoPedidosExame.codigo_empresa',
				'ClienteFuncionario.codigo = FuncionarioSetorCargo.codigo_cliente_funcionario',
				'FuncionarioSetorCargo.codigo_setor = Setor.codigo',
				'FuncionarioSetorCargo.codigo_cargo = Cargo.codigo'
			)),
			'Fornecedor' => array('className' => 'Fornecedor', 'foreignKey' => false, 'conditions' => array(
				'Fornecedor.codigo_empresa =  ImportacaoPedidosExame.codigo_empresa',
				'ImportacaoPedidosExamesRegistros.fornecedor LIKE Fornecedor.codigo'
			)),
			'StatusImportacao' => array('className' => 'StatusImportacao', 'foreignKey' => false, 'conditions' => array(
				'StatusImportacao.codigo = ImportacaoPedidosExamesRegistros.codigo_status_importacao'
			))
		)));

		$order = '';
		if ($findType != 'count') {
			$order = array('ImportacaoPedidosExamesRegistros.nome_empresa ASC, ImportacaoPedidosExamesRegistros.nome_unidade ASC, ImportacaoPedidosExamesRegistros.nome_setor ASC, ImportacaoPedidosExamesRegistros.nome_cargo ASC, ImportacaoPedidosExamesRegistros.cpf, ImportacaoPedidosExamesRegistros.data_realizacao ASC');
		}

		$fields = $this->findImportacaoBaseFields();
		$conditions = $options['conditions'];
		$query_base = $this->find('sql', compact('fields', 'conditions'));
		$dbo = $this->getDataSource();

		$cte = "WITH Base AS ($query_base)";

		$offset = (isset($options['page']) && $options['page'] > 1 ? (($options['page'] - 1) * $options['limit']) : null);
		$query = $dbo->buildStatement(array(
			'fields' => $this->findImportacaoFields(),
			'table' => "Base",
			'alias' => 'ImportacaoPedidosExamesRegistros',
			'joins' => array(
				array(
					'table' => $ClienteFuncionario->databaseTable . "." . $ClienteFuncionario->tableSchema . "." . $ClienteFuncionario->useTable,
					'alias' => 'ClienteFuncionario',
					'conditions' => array(
						'ClienteFuncionario.codigo_empresa = ImportacaoPedidosExamesRegistros.codigo_empresa',
						'ClienteFuncionario.codigo = ImportacaoPedidosExamesRegistros.codigo_cliente_funcionario'
					),
					'type' => 'LEFT'
				),
				array(
					'table' => $Funcionario->databaseTable . "." . $Funcionario->tableSchema . "." . $Funcionario->useTable,
					'alias' => 'Funcionario',
					'conditions' => 'Funcionario.cpf = ImportacaoPedidosExamesRegistros.cpf',
					'type' => 'LEFT'
				),
				array(
					'table' => $FuncionarioSetorCargo->databaseTable . "." . $FuncionarioSetorCargo->tableSchema . "." . $FuncionarioSetorCargo->useTable,
					'alias' => 'FuncionarioSetorCargo',
					'conditions' => 'FuncionarioSetorCargo.codigo = ImportacaoPedidosExamesRegistros.codigo_func_setor_cargo',
					'type' => 'LEFT'
				),
				array(
					'table' => $Setor->databaseTable . "." . $Setor->tableSchema . "." . $Setor->useTable,
					'alias' => 'Setor',
					'conditions' => array(
						'Setor.codigo_empresa = FuncionarioSetorCargo.codigo_empresa',
						'Setor.codigo = FuncionarioSetorCargo.codigo_setor'
					),
					'type' => 'LEFT'
				),
				array(
					'table' => $Cargo->databaseTable . "." . $Cargo->tableSchema . "." . $Cargo->useTable,
					'alias' => 'Cargo',
					'conditions' => array(
						'Cargo.codigo_empresa = FuncionarioSetorCargo.codigo_empresa',
						'Cargo.codigo = FuncionarioSetorCargo.codigo_cargo'
					),
					'type' => 'LEFT'
				),
				array(
					'table' => $Fornecedor->databaseTable . "." . $Fornecedor->tableSchema . "." . $Fornecedor->useTable,
					'alias' => 'Fornecedor',
					'conditions' => array(
						'Fornecedor.codigo_empresa = ImportacaoPedidosExamesRegistros.codigo_empresa',
						'ImportacaoPedidosExamesRegistros.fornecedor like Fornecedor.codigo'
					),
					'type' => 'LEFT'
				),
			),
			'limit' => (isset($options['limit']) ? $options['limit'] : null),
			'offset' => $offset,
			'conditions' => null,
			'order' => ($order),
			'group' => null,
		), $this);

		// print $cte.$query;exit;

		if ($findType == 'sql') {
			return array('cte' => $cte, 'query' => $query);
		} elseif ($findType == 'count') {
			$result = $this->query("{$cte} SELECT COUNT(codigo) AS qtd FROM ({$query}) AS base");
			return $result[0][0]['qtd'];
		}
		return $this->query($cte . $query);
	}

	/**
	 * Método de definição de quais campos serão utilizados para validação dos dados da importação de pedidos de exame médico.
	 * @return array Campos que serão utilizados na query de busca de dados para validação dos dados da importação de pedidos de exame médico.
	 */
	private function findImportacaoFields()
	{
		return array(
			'ImportacaoPedidosExamesRegistros.codigo',
			'ImportacaoPedidosExamesRegistros.codigo_empresa',
			'ImportacaoPedidosExamesRegistros.nome_empresa',
			'ImportacaoPedidosExamesRegistros.nome_unidade',
			'ImportacaoPedidosExamesRegistros.nome_setor',
			'ImportacaoPedidosExamesRegistros.nome_cargo',
			'ImportacaoPedidosExamesRegistros.cpf',
			'CONVERT(VARCHAR,ImportacaoPedidosExamesRegistros.data_solicitacao, 103) AS data_solicitacao',
			'ImportacaoPedidosExamesRegistros.tipo_item_pedido',
			'ImportacaoPedidosExamesRegistros.nome_exame',
			'ImportacaoPedidosExamesRegistros.tipo_exame',
			"CASE WHEN ImportacaoPedidosExamesRegistros.tipo_exame = 'ADMISSIONAL'	THEN 1 ELSE 0 END AS exame_admissional",
			"CASE WHEN ImportacaoPedidosExamesRegistros.tipo_exame = 'DEMISSIONAL'	THEN 1 ELSE 0 END AS exame_demissional",
			"CASE WHEN ImportacaoPedidosExamesRegistros.tipo_exame = 'RETORNO'		THEN 1 ELSE 0 END AS exame_retorno",
			"CASE WHEN ImportacaoPedidosExamesRegistros.tipo_exame = 'MUDANCA'		THEN 1 ELSE 0 END AS exame_mudanca",
			"CASE WHEN ImportacaoPedidosExamesRegistros.tipo_exame = 'QUALIDADE'	THEN 1 ELSE 0 END AS qualidade_vida",
			"CASE WHEN ImportacaoPedidosExamesRegistros.tipo_exame = 'PONTUAL'		THEN 1 ELSE 0 END AS pontual",
			"CASE WHEN ImportacaoPedidosExamesRegistros.tipo_exame = 'PERIODICO'	THEN 1 ELSE 0 END AS exame_periodico",
			"CASE WHEN ImportacaoPedidosExamesRegistros.tipo_exame = 'MONITORACAO'	THEN 1 ELSE 0 END AS exame_monitoracao",
			'CONVERT(VARCHAR,ImportacaoPedidosExamesRegistros.data_realizacao, 103) AS data_realizacao',
			'ImportacaoPedidosExamesRegistros.resultado_exame',
			"CASE WHEN ImportacaoPedidosExamesRegistros.resultado_exame = 'NORMAL' THEN 1 ELSE 2 END AS resultado",
			'ImportacaoPedidosExamesRegistros.resultado_observacao',
			'ImportacaoPedidosExamesRegistros.observacao',
			'Funcionario.nome AS nome_funcionario',
			'Setor.descricao AS setor_descricao',
			'Cargo.descricao AS cargo_descricao',
			'ImportacaoPedidosExamesRegistros.status_importacao AS status_importacao',
			"ImportacaoPedidosExamesRegistros.codigo_importacao_pedidos_exames AS codigo_importacao_pedidos_exames",
			"FuncionarioSetorCargo.codigo AS codigo_func_setor_cargo",
			"ClienteFuncionario.codigo AS codigo_cliente_funcionario",
			"ClienteFuncionario.codigo_funcionario AS codigo_funcionario",
			"ImportacaoPedidosExamesRegistros.fornecedor AS fornecedor",
			"Fornecedor.codigo AS codigo_fornecedor",
			"Fornecedor.nome AS nome_fornecedor",
			"FuncionarioSetorCargo.codigo_cliente_alocacao AS codigo_cliente_alocacao"
		);
	}

	/**
	 * Método de definição de quais campos serão utilizados para validação dos dados da importação de pedidos de exame médico.
	 * @return array Campos que serão utilizados na query de busca de dados para validação dos dados da importação de pedidos de exame médico.
	 */
	private function findImportacaoBaseFields()
	{
		return array(
			'ImportacaoPedidosExame.codigo_empresa AS codigo_empresa',
			'ImportacaoPedidosExamesRegistros.codigo AS codigo',
			'ImportacaoPedidosExamesRegistros.nome_empresa AS nome_empresa',
			'ImportacaoPedidosExamesRegistros.nome_unidade AS nome_unidade',
			'ImportacaoPedidosExamesRegistros.nome_setor AS nome_setor',
			'ImportacaoPedidosExamesRegistros.nome_cargo AS nome_cargo',
			'ImportacaoPedidosExamesRegistros.cpf AS cpf',
			'ImportacaoPedidosExamesRegistros.data_solicitacao AS data_solicitacao',
			'ImportacaoPedidosExamesRegistros.tipo_item_pedido AS tipo_item_pedido',
			'ImportacaoPedidosExamesRegistros.nome_exame AS nome_exame',
			'ImportacaoPedidosExamesRegistros.tipo_exame AS tipo_exame',
			'ImportacaoPedidosExamesRegistros.data_realizacao AS data_realizacao',
			'ImportacaoPedidosExamesRegistros.resultado_exame AS resultado_exame',
			'ImportacaoPedidosExamesRegistros.resultado_observacao AS resultado_observacao',
			'ImportacaoPedidosExamesRegistros.observacao AS observacao',
			'ClienteUnidade.codigo AS cliente_alocacao_codigo',
			'ClienteUnidade.razao_social AS cli_aloc_razao_social',
			'ClienteUnidade.nome_fantasia AS cli_aloc_nome_fantasia',
			"ClienteFuncionario.codigo AS codigo_cliente_funcionario",
			"FuncionarioSetorCargo.codigo AS codigo_func_setor_cargo",
			"Funcionario.nome AS nome_funcionario",
			"StatusImportacao.descricao AS status_importacao",
			"ImportacaoPedidosExamesRegistros.codigo_importacao_pedidos_exames AS codigo_importacao_pedidos_exames",
			"FuncionarioSetorCargo.codigo AS codigo_func_setor_cargo",
			"ClienteFuncionario.codigo AS codigo_cliente_funcionario",
			"ClienteFuncionario.codigo_funcionario AS codigo_funcionario",
			"ImportacaoPedidosExamesRegistros.fornecedor AS fornecedor",
			"Fornecedor.codigo AS codigo_fornecedor",
			"Fornecedor.nome AS nome_fornecedor",
			"FuncionarioSetorCargo.codigo_cliente_alocacao AS codigo_cliente_alocacao"
		);
	}

	/**
	 * Método de depara dos campos da planilha para os campos da query de dados de validação dos dados de importação de pedidos de exame médico
	 * @return array Depara de campos
	 */
	function depara()
	{
		return array(
			'nome_funcionario'			=> 'nome_funcionario',
			'nome_unidade'				=> 'nome_unidade',
			'nome_setor'				=> 'setor_descricao',
			'nome_cargo'				=> 'cargo_descricao',
			'cpf'						=> 'cpf',
			'data_solicitacao' 			=> 'data_solicitacao',
			'tipo_item_pedido' 			=> 'tipo_item_pedido',
			'nome_exame' 				=> 'nome_exame',
			'tipo_exame' 				=> 'tipo_exame',
			'fornecedor' 				=> 'fornecedor',
			'nome_fornecedor' 			=> 'nome_fornecedor',
			'data_realizacao' 			=> 'data_realizacao',
			'resultado_exame' 			=> 'resultado_exame',
			'resultado_observacao' 		=> 'resultado_observacao',
			'observacao'				=> 'observacao'
		);
	}

	/**
	 * Método de depara dos campos das validação para os títulos de exibição do cabeçalho da listagem de registros de importação de pedidos de exame médico.
	 * @return array Depara de campos de validação para os títulos das colunas da listagem de registros de importação de pedidos de exame médico.
	 */
	function titulos()
	{
		return array(
			'nome_funcionario'			=> 'Funcionário',
			'nome_unidade'				=> 'Nome da Unidade',
			'nome_setor'				=> 'Nome do Setor',
			'nome_cargo'				=> 'Nome do Cargo',
			'cpf'						=> 'CPF',
			'data_solicitacao' 			=> 'Data de Solicitação',
			'tipo_item_pedido' 			=> 'Item do Pedido',
			'nome_exame' 				=> 'Nome do Exame',
			'tipo_exame' 				=> 'Tipo de Exame',
			'fornecedor' 				=> 'Prestador de Serviço',
			'nome_fornecedor' 			=> 'Nome Prestador de Serviço',
			'data_realizacao' 			=> 'Data de Realização',
			'resultado_exame' 			=> 'Resultado',
			'resultado_observacao' 		=> 'Observação Resultado',
			'observacao'				=> 'Observação'
		);
	}

	/**
	 * Método de validação de todas as linhas do arquivo importadas para geração de pedidos de exame médico
	 * @param array $registros 
	 * @return array Array onde a chave é nome do arquivo e a mensagem de inconsistência gerada na validação
	 */
	function alertasRegistrosCadastros($registros)
	{
		$alertas = array();
		$validacoes = array();
		$retorno = array();
		foreach ($registros as $key => $registro) {
			$retorno = $this->alertasRegistroCadastros($registro);
			$alertas[$key] 		= $retorno['alertas'];
			$validacoes[$key]	= $retorno['validacoes'];
		}
		return array('alertas' => $alertas, 'validacoes' => $validacoes);;
	}

	/**
	 * Método de validação por linha, chamada no método alertasRegistrosCadastros
	 * Nesse método são verificados se as colunas da planilha estão preenchidos com informação para geração do registro de pedido de exame	
	 * @param array $registros 
	 * @return array Array onde a chave é nome do arquivo e a mensagem de inconsistência gerada na validação
	 */
	function alertasRegistroCadastros($registro)
	{
		$Documento			= &ClassRegistry::init('Documento');
		$Exame				= &ClassRegistry::init('Exame');
		$Fornecedor			= &ClassRegistry::init('Fornecedor');
		$TipoExame			= &ClassRegistry::init('TipoExame');

		$alertas 	= array_keys($registro[0]);
		$alertas	= array_flip($alertas);

		$campos = array();
		foreach ($registro[0] as $key => $value) $registro[0][$key] = trim($registro[0][$key]);

		if (empty($registro[0]['nome_funcionario']))	$campos['nome_funcionario'] = 'Funcionário não encontrado';
		if (empty($registro[0]['nome_unidade']))		$campos['nome_unidade'] = 'Unidade do Funcionário não encontrado';
		if (empty($registro[0]['nome_setor']))			$campos['nome_setor'] = 'Setor do Funcionário não encontrado';
		if (empty($registro[0]['nome_cargo']))			$campos['nome_cargo'] = 'Cargo do Funcionário não encontrado';

		if (empty($registro[0]['data_solicitacao'])) {
			$campos['data_solicitacao'] = 'Data de Solicitação do exame não informada';
		} elseif (!empty($registro[0]['data_realizacao'])) {

			$data_solicitacao = AppModel::dateToDbDate2($registro[0]['data_solicitacao']);
			$data_realizacao = AppModel::dateToDbDate2($registro[0]['data_realizacao']);

			if (date('Y-m-d', strtotime($data_solicitacao)) > date('Y-m-d', strtotime($data_realizacao))) {
				$campos['data_solicitacao']	= 'Data Realização de Exame deve ser (maior ou igual) a Data de Solicitação do Pedido de Exame';
				$campos['data_realizacao']	= 'Data Realização de Exame deve ser (maior ou igual) a Data de Solicitação do Pedido de Exame';
			}
		}

		if (empty($registro[0]['cpf'])) {
			$campos['cpf'] = 'CPF não informado';
		} elseif (!$Documento->isCPF($registro[0]['cpf'])) {
			$campos['cpf'] = 'CPF inválido';
		}

		if (empty($registro[0]['tipo_exame'])) {
			$campos['tipo_exame'] = 'Tipo do exame não informado';
		} elseif (!in_array(strtoupper($registro[0]['tipo_exame']), explode('|', 'ADMISSIONAL|DEMISSIONAL|MUDANCA|QUALIDADE|PERIODICO|RETORNO|MONITORACAO'))) {
			$campos['tipo_exame'] = 'Tipo do exame não encontrado na plataforma RHHealth.';
		}

		if (empty($registro[0]['nome_exame'])) {
			$campos['nome_exame'] = 'Nome do exame não informado';
		} else {
			$conditions = array(
				'UPPER(Exame.descricao)' => strtoupper($registro[0]['nome_exame'])
			);
			if (!$Exame->find('first', array('conditions' => $conditions, 'fields' => array('descricao')))) {
				$campos['nome_exame'] = 'Exame não encontrado na plataforma RHHealth.';
			}
		}

		if (!empty($registro[0]['fornecedor'])) {
			// $conditions = array(
			// 	'Fornecedor.codigo' => trim($registro[0]['fornecedor'])
			// );
			// if(!$Fornecedor->find('first',array('conditions' => $conditions, 'fields' => array('nome')))) {
			// 	$campos['fornecedor'] = 'Prestador de Serviço do exame médico não encontrado na plataforma RHHealth.';
			// }

			//query para pegar o fornecedor pois estava demorando muito com as funcoes do cake
			$sql = "SELECT nome FROM RHHealth.dbo.fornecedores WHERE codigo = " . trim($registro[0]['fornecedor']);
			$fornecedor = $this->query($sql);
			//verifica se o fornecedor existe
			if (empty($fornecedor)) {
				$campos['fornecedor'] = 'Prestador de Serviço do exame médico não encontrado na plataforma RHHealth.';
			}
		} else {
			$campos['fornecedor'] 	= 'Prestador de Serviço não informado';
		}

		if (empty($registro[0]['resultado_exame'])) {
			$campos['resultado_exame'] = 'Tipo do exame não informado';
		} elseif (!in_array(strtoupper(Comum::tirarAcentos($registro[0]['resultado_exame'])), explode('|', 'NORMAL|ALTERADO|SEM ALTERACAO APARENTE'))) {
			$campos['resultado_exame'] = 'Resultado do exame não encontrado na plataforma RHHealth.';
		}

		return array('alertas' => $alertas, 'validacoes' => $campos);
	}

	/**
	 * Método de Verificação de existência da informação do pedido de exame
	 * @param array $data Dados para busca, validação e geração de registro do Atestado e Atestado CID
	 * @return integer Código de identificação do registro de Atestado gerado
	 */
	function importarPedidoExame($data)
	{
		$pedido_exame 		= &ClassRegistry::init('PedidoExame');
		$item_pedido_exame	= &ClassRegistry::init('ItemPedidoExame');
		$item_pedido_baixa	= &ClassRegistry::init('ItemPedidoExameBaixa');
		$pedido_exame		= &ClassRegistry::init('PedidoExame');
		$pedido_lote		= &ClassRegistry::init('PedidoLote');
		$retorno_pedido		= array("codigo_pedido_exame" => 0, "invalidFields" => "");
		$pedido_lote_id		= 0;
		$item_pedido_id		= 0;

		$condicao_tipo_exame = '';
		if (isset($data['exame_admissional']) && $data['exame_admissional']) {
			$condicao_tipo_exame = 'exame_admissional';
		} else if (isset($data['exame_demissional']) && $data['exame_demissional']) {
			$condicao_tipo_exame = 'exame_demissional';
		} else if (isset($data['exame_periodico']) && $data['exame_periodico']) {
			$condicao_tipo_exame = 'exame_periodico';
		} else if (isset($data['pontual']) && $data['pontual']) {
			$condicao_tipo_exame = 'pontual';
		} else if (isset($data['exame_retorno']) && $data['exame_retorno']) {
			$condicao_tipo_exame = 'exame_retorno';
		} else if (isset($data['exame_mudanca']) && $data['exame_mudanca']) {
			$condicao_tipo_exame = 'exame_mudanca';
		} else if (isset($data['exame_monitoracao']) && $data['exame_monitoracao']) {
			$condicao_tipo_exame = 'exame_monitoracao';
		}

		$dados = array(
			'conditions' => array(
				'PedidoExame.data_solicitacao' => AppModel::dateToDbDate2($data['data_solicitacao']),
				$condicao_tipo_exame => 1,
				'PedidoExame.codigo_funcionario' => $data['codigo_funcionario'],
				'PedidoExame.codigo_func_setor_cargo' => $data['codigo_func_setor_cargo']
			)
		);

		if (!$pedido_exame_id = $pedido_exame->find('first', $dados)) {
			try {
				$data_lote = array('codigo_grupo_economico' => $data['codigo_grupo_economico']);
				if (!$pedido_lote->incluir($data_lote)) {
					$retorno_pedido['invalidFields'] = "Erro na inclusão do lote do pedido";
					$retorno_pedido['invalidFields'] .= $pedido_lote->validationErrors;
				} else {
					$pedido_lote_id = $pedido_lote->getLastInsertId();
				}
			} catch (Exception $e) {
				$retorno_pedido['invalidFields'] = $e->getMessage();
				$this->log($e->getMessage(), 'debug');
			}

			$data['codigo_status_pedidos_exames'] = StatusPedidoExame::TOTALMENTE_BAIXADO;
			if ($pedido_lote_id) {
				$data['codigo_pedidos_lote'] = $pedido_lote_id;
			}
			try {
				if ($pedido_exame->incluir($data)) {
					$retorno_pedido['codigo_pedido_exame'] = $pedido_exame->getLastInsertId();
					$pedido_exame_id = $retorno_pedido['codigo_pedido_exame'];
				}
				$retorno_pedido['invalidFields'] = $pedido_exame->validationErrors;
			} catch (Exception $e) {
				$retorno_pedido['invalidFields'] = $e->getMessage();
				$this->log($e->getMessage(), 'debug');
			}
		} else {
			$pedido_exame_id = $pedido_exame_id['PedidoExame']['codigo'];
			$retorno_pedido['codigo_pedido_exame'] = $pedido_exame_id;
			$conditions = array('conditions' => array(
				"ItemPedidoExame.codigo_pedidos_exames" => $pedido_exame_id,
				"ItemPedidoExame.codigo_exame" => $data['codigo_exame']
			));
			if ($item_pedido_exame->find('first', $conditions)) {
				$retorno_pedido['invalidFields'] = 'Pedido já existente para o funcionário e data de solicitação informados.';
			}
		}

		// $this->log(print_r($retorno_pedido,1),'debug');
		// $this->log(print_r($data,1),'debug');

		//verifica se tenho algum invalid fields
		if (empty($retorno_pedido['invalidFields'])) {

			if ($pedido_exame_id) {

				$data_item_pedido = array(
					'codigo_pedidos_exames' => $pedido_exame_id,
					'codigo_fornecedor'		=> $data['codigo_fornecedor'],
					'codigo_exame'			=> $data['codigo_exame'],
					'valor'					=> 0
				);

				//para colocar o resultado e o comparecimento
				if (isset($data['resultado']) && $data['resultado']) {
					$data_item_pedido['data_realizacao_exame'] = $data['data_realizacao'];
					$data_item_pedido['compareceu'] = 1;
				}

				try {
					if ($item_pedido_exame->incluir($data_item_pedido)) {
						$item_pedido_id = $item_pedido_exame->getLastInsertId();
					} else {
						$retorno_pedido['invalidFields'] = $item_pedido_exame->validationErrors;
					}
				} catch (Exception $e) {
					$retorno_pedido['invalidFields'] = $e->getMessage();
					$this->log($e->getMessage(), 'debug');
				}

				if (
					(isset($data['resultado']) && $data['resultado'] && $item_pedido_id) ||
					$data['codigo_fornecedor'] == self::CODIGO_FORNECEDOR_ANTIGO_PRESTADOR
				) {
					$data_item_baixa = array(
						'codigo_itens_pedidos_exames' => $item_pedido_id,
						'resultado'	=> !empty($data['resultado']) ? $data['resultado'] : '',
						'descricao'	=> !empty($data['resultado_observacao']) ? $data['resultado_observacao'] : '',
						'data_realizacao_exame' => $data['data_realizacao'],
						'pedido_importado' => 1
					);

					try {
						if ($item_pedido_baixa->incluir($data_item_baixa)) {
							$item_pedido_baixa->getLastInsertId();
						} else {
							$retorno_pedido['invalidFields'] = $item_pedido_baixa->validationErrors;
						}
					} catch (Exception $e) {
						$retorno_pedido['invalidFields'] = $e->getMessage();
						$this->log($e->getMessage(), 'debug');
					}
				}
			}
		} //fim invalidfields

		return $retorno_pedido;
	}
}
