<?php
class ConsultaPedidosExamesController extends AppController
{
	public $name = 'ConsultaPedidosExames';
	public $helpers = array('BForm', 'Html', 'Ajax', 'Highcharts');

	var $uses = array(
		'PedidoExame',
		'Exame',
		'Setor',
		'Cargo',
		'Cliente',
		'Funcionario',
		'ClienteFuncionario',
		'GrupoEconomico',
		'GrupoEconomicoCliente',
		'ItemPedidoExame',
		'FuncionarioSetorCargo',
		'ItemPedidoExameBaixa',
		'ClienteEndereco',
		'FornecedorEndereco'
	);

	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->BAuth->allow(array('get_combo_cidade_unidade', 'get_combo_estado_unidade', 'get_combo_cidade_fornecedor', 'get_combo_estado_fornecedor', 'resultado_de_exames', 'resultado_de_exames_listagem', 'resultado_exames', 'resultado_exames_listagem', 'baixa_exames_analitico_listagem'));
	}

	/**
	 * Metodo para buscar os dados agrupados sintetico e montar o relatorio
	 */
	public function baixa_exames_sintetico()
	{
		//titulo da pagina
		$this->pageTitle = 'Exames Baixados Sintético';

		//inserido na filtros controller
		$filtros = $this->Filtros->controla_sessao($this->data, 'PedidoExame');

		if (!isset($filtros['codigo_cliente'])) {
			$filtros['codigo_cliente'] = null;
		}

		if (!isset($filtros['tipo_periodo'])) {
			$filtros['tipo_periodo'] = 'E';
		}

		if (!isset($filtros['agrupamento'])) {
			$filtros['agrupamento'] = 1;
		}

		if (empty($filtros['data_inicio'])) {
			$filtros['data_inicio'] = '01/' . date('m/Y');
			$filtros['data_fim'] = date('d/m/Y');
		}

		//pega o usuario que esta logdao
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		//pega os filtros setados que estao em sessao
		$this->data['PedidoExame'] = $filtros;

		$tipos_agrupamento = $this->PedidoExame->tiposAgrupamento();

		$this->set(compact('tipos_agrupamento'));
		$this->carrega_combos_grupo_economico('PedidoExame');
		$this->carrega_combo_periodo();
	} //fim baixa_exames_sintetico

	/**
	 * Metodo para apresentar a listagem dos dados
	 */
	public function baixa_exames_sintetico_listagem()
	{

		//pega os filtros da sessão
		$filtros = $this->Filtros->controla_sessao($this->data, 'PedidoExame');
		//verifica o usuario logado
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		//pega os filtros da sessao e seta em um array
		$this->data['PedidoExame'] = $filtros;
		//varialve auxiliar
		$dados = array();
		$agrupamento = $filtros['agrupamento'];

		if (!empty($filtros['codigo_cliente'])) {
			$conditions = $this->PedidoExame->converteFiltrosEmConditions($filtros);
			$dados = $this->PedidoExame->baixa_exames_sintetico($agrupamento, $conditions);
		}

		//seta para pegar os dados na view
		$this->set(compact('dados', 'agrupamento'));
	} // fim baixa_exames_sintetico_listagem() 

	/**
	 * Metodo para montar os arrays de carregamento dos combos
	 */
	public function carrega_combos_grupo_economico($model)
	{
		//instancia as models
		$this->loadModel('Cargo');
		$this->loadModel('Setor');
		//pega as unidades
		$unidades = $this->GrupoEconomicoCliente->lista($this->data[$model]['codigo_cliente']);
		//pega os setores
		$setores = $this->Setor->lista($this->data[$model]['codigo_cliente']);
		//pega os cargos
		$cargos = $this->Cargo->lista($this->data[$model]['codigo_cliente']);


		//filtros cidades unidades
		//monta a cidade unidade
		$cidade_unidade = array();
		$codigo_estado_unidade = null;
		if (isset($this->data[$model]['codigo_estado_unidade'])) {
			$codigo_estado_unidade = $this->data[$model]['codigo_estado_unidade'];
		}

		$cid_unidade = $this->ClienteEndereco->get_combo_cidade($this->data[$model]['codigo_cliente'], $codigo_estado_unidade);
		foreach ($cid_unidade as $cu) {
			$cidade_unidade[$cu['codigo']] = $cu['descricao'];
		}

		$estado_unidade = array();
		$est_unidade = $this->ClienteEndereco->get_combo_estado($this->data[$model]['codigo_cliente']);
		foreach ($est_unidade as $eu) {
			$estado_unidade[$eu['codigo']] = $eu['descricao'];
		}

		$cidade_credenciado = array();
		$codigo_estado_fornecedor = null;
		if (isset($this->data[$model]['codigo_estado_fornecedor'])) {
			$codigo_estado_fornecedor = $this->data[$model]['codigo_estado_fornecedor'];
		}
		$cid_credenciado = $this->FornecedorEndereco->get_combo_cidade($this->data[$model]['codigo_cliente'], $codigo_estado_fornecedor);
		foreach ($cid_credenciado as $cc) {
			$cidade_credenciado[$cc['codigo']] = $cc['descricao'];
		}

		$estado_credenciado = array();
		$est_credenciado = $this->FornecedorEndereco->get_combo_estado($this->data[$model]['codigo_cliente']);
		foreach ($est_credenciado as $ec) {
			$estado_credenciado[$ec['codigo']] = $ec['descricao'];
		}

		//seta os valores para recuperar na view
		$this->set(compact('unidades', 'setores', 'cargos', 'cidade_unidade', 'estado_unidade', 'cidade_credenciado', 'estado_credenciado'));
	} //fim carrega_combos_grupo_economico

	/**
	 * metodo para pegar os tipos de pesquisa que ira existir
	 */
	public function carrega_combo_periodo()
	{
		//tipos de periodo
		$tipos_periodo = array(
			'E' => 'Emissão',
			'R' => 'Resultado',
			'B' => 'Baixa'
		);

		$this->set(compact('tipos_periodo'));
	} //fim carrega_combo_periodo

	/**
	 * Metod para trazer os resultados no detalhe do analitico
	 */
	public function baixa_exames_analitico()
	{
		$this->pageTitle = 'Exames Baixados Analítico';
		$this->layout = 'new_window';

		$filtros = $this->Filtros->controla_sessao($this->data, 'PedidoExame');

		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$this->data['PedidoExame'] = $filtros;
		$this->carrega_combos_grupo_economico('PedidoExame');
		$this->carrega_combo_periodo();
		$this->carrega_combo_tipo_exame();
	} //fim baixa_exames_analitico

	/**
	 * Metodo com os detalhes analiticos de listagem
	 */
	public function baixa_exames_analitico_listagem($export = false)
	{
		// debug('opa');exit;

		$filtros = $this->Filtros->controla_sessao($this->data, 'PedidoExame');

		// debug($filtros);

		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$dados = array();
		if (!empty($filtros['codigo_cliente'])) {
			$conditions = $this->PedidoExame->converteFiltrosEmConditions($filtros);
			if ($export) {
				//monta a query
				$query_export = $this->PedidoExame->baixa_exames_analitico(null, $conditions);
				//faz a consulta pra exportar
				$query = $this->PedidoExame->find('sql', array('fields' => $query_export['fields'], 'conditions' => $query_export['conditions'], 'joins' => $query_export['joins']));
				//passa o resultado pra query pra monstar o csv
				$this->exportExamesBaixados($query);
			}

			// $dados = $this->PedidoExame->baixa_exames_analitico('all', compact('conditions'));
			//monsta a query na model, por que estava dando problema de memoria, pra resolver é necessario termos que colocar a paginação na tela
			$dado = $this->PedidoExame->baixa_exames_analitico(null, $conditions);
			//paginate
			$this->paginate['PedidoExame'] = array(
				'recursive' => -1,
				'fields' => $dado['fields'],
				'joins' => $dado['joins'],
				'conditions' => $dado['conditions'],
				'limit' => 50
			);
			//printa o paginate pra ctp
			$dados = $this->paginate('PedidoExame');
		}

		// pr($dados);exit;

		$this->set(compact('dados'));
	} //fim baixa_exames_analitico_listagem

	/**
	 * Metodo para exportar os dados da consulta analitica
	 */
	public function exportExamesBaixados($query)
	{

		$dbo = $this->PedidoExame->getDataSource();
		$dbo->results   = $dbo->rawQuery($query);
		ob_clean();
		header('Content-Encoding: UTF-8');
		header("Content-Type: application/force-download;charset=utf-8");
		header('Content-Disposition: attachment; filename="exames_baixados' . date('YmdHis') . '.csv"');
		echo utf8_decode('"Pedido";"Cliente Matrícula";"Unidade Alocação";"Cidade Unidade";"Estado Unidade";"Funcionário";"Setor";"Cargo";"CPF";"Matrícula";"Exame";"Tipo de exame ocupacional";"Credenciado";"Cidade Credenciado";"Estado Credenciado";"Respondido Lyn";"Data Emissão Pedido";"Data da Realização do Exame";"Data da Baixa do Pedido";"Fornecedor Particular"') . "\n";

		while ($value = $dbo->fetchRow()) {
			$linha  = $value[0]['codigo'] . ';';
			$linha .= $value[0]['cliente'] . ';';
			$linha .= $value[0]['unidade_nome_fantasia'] . ';';
			$linha .= $value[0]['cliente_cidade'] . ';';
			$linha .= $value[0]['cliente_estado'] . ';';
			$linha .= $value[0]['funcionario'] . ';';
			$linha .= $value[0]['setor_descricao'] . ';';
			$linha .= $value[0]['cargo_descricao'] . ';';
			$linha .= $value[0]['cpf'] . ';';
			$linha .= $value[0]['matricula'] . ';';
			$linha .= $value[0]['exame_descricao'] . ';';
			$linha .= $value[0]['tipo_exame'] . ';';
			$linha .= $value[0]['credenciado'] . ';';
			$linha .= $value[0]['fornecedor_cidade'] . ';';
			$linha .= $value[0]['fornecedor_estado'] . ';';
			$linha .= $value[0]['respondido_lyn'] . ';';
			$linha .= AppModel::dbDateToDate($value[0]['data_emissao']) . ';';
			$linha .= AppModel::dbDateToDate($value[0]['data_resultado']) . ';';
			$linha .= AppModel::dbDateToDate($value[0]['data_baixa']) . ';';
			$linha .= $value[0]['fornecedor_particular'] . ';';
			echo utf8_decode($linha) . "\n";
		}
		die();
	} //fim exportExamesBaixados

	/**
	 * Metodo para exportar os dados da consulta resultado de exames
	 */
	public function exportResultadoExames($query)
	{

		$dbo = $this->PedidoExame->getDataSource();
		$dbo->results   = $dbo->rawQuery($query);
		ob_clean();
		header('Content-Encoding: UTF-8');
		header("Content-Type: application/force-download;charset=utf-8");
		header('Content-Disposition: attachment; filename="resultado_exames' . date('YmdHis') . '.csv"');
		echo utf8_decode('"Pedido";"Cliente Matrícula";"Unidade Alocação";"Cidade Unidade";"Estado Unidade";"Funcionário";"Setor";"Cargo";"CPF";"Matrícula";"Exame";"Tipo de exame ocupacional";"Credenciado";"Cidade Credenciado";"Estado Credenciado";"Respondido Lyn";"Data Emissão Pedido";"Data da Realização do Exame";"Data da Baixa do Pedido";"Fornecedor Particular";"Resultado do Exame"') . "\n";

		while ($value = $dbo->fetchRow()) {
			$linha  = $value[0]['codigo'] . ';';
			$linha .= $value[0]['cliente'] . ';';
			$linha .= $value[0]['unidade_nome_fantasia'] . ';';
			$linha .= $value[0]['cliente_cidade'] . ';';
			$linha .= $value[0]['cliente_estado'] . ';';
			$linha .= $value[0]['funcionario'] . ';';
			$linha .= $value[0]['setor_descricao'] . ';';
			$linha .= $value[0]['cargo_descricao'] . ';';
			$linha .= $value[0]['cpf'] . ';';
			$linha .= $value[0]['matricula'] . ';';
			$linha .= $value[0]['exame_descricao'] . ';';
			$linha .= $value[0]['tipo_exame'] . ';';
			$linha .= $value[0]['credenciado'] . ';';
			$linha .= $value[0]['fornecedor_cidade'] . ';';
			$linha .= $value[0]['fornecedor_estado'] . ';';
			$linha .= $value[0]['respondido_lyn'] . ';';
			$linha .= AppModel::dbDateToDate($value[0]['data_emissao']) . ';';
			$linha .= AppModel::dbDateToDate($value[0]['data_resultado']) . ';';
			$linha .= AppModel::dbDateToDate($value[0]['data_baixa']) . ';';
			$linha .= $value[0]['fornecedor_particular'] . ';';
			$linha .= $value[0]['tipo_resultado'] . ';';
			echo utf8_decode($linha) . "\n";
		}
		die();
	} //fim exportExamesBaixados
	/**
	 * metodo para pegar os tipos de pedido de exame
	 */
	public function carrega_combo_tipo_exame()
	{
		$tipos_exames = array(
			1 => 'Exame admissional',
			2 => 'Exame periódico',
			3 => 'Exame demissional',
			4 => 'Retorno ao trabalho',
			5 => 'Mudança de riscos ocupacionais',
			6 => 'Monitoração pontual',
			7 => 'Pontual'
		);

		$this->set(compact('tipos_exames'));
	} //fim carrega_combo_tipo_exame


	/**
	 * [get_combo_cidade_unidade description]
	 * 
	 * metodo pego pelo ajax para popular o combo de cidade
	 * 
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	public function get_combo_cidade_unidade($codigo_cliente, $uf = null)
	{

		//pega as cidades e estados do codigo unidade pesquisado
		$result = $this->ClienteEndereco->get_combo_cidade($codigo_cliente, $uf);

		echo json_encode($result);
		die();
	} //fim get_combo_cidade_unidade

	/**
	 * [get_combo_cidade_unidade description]
	 * 
	 * metodo pego pelo ajax para popular o combo de cidade
	 * 
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	public function get_combo_estado_unidade($codigo_cliente)
	{

		//pega as cidades e estados do codigo unidade pesquisado
		$result = $this->ClienteEndereco->get_combo_estado($codigo_cliente);
		echo json_encode($result);
		die();
	} //fim get_combo_cidade_unidade


	/**
	 * [get_combo_cidade_unidade description]
	 * 
	 * metodo pego pelo ajax para popular o combo de cidade
	 * 
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	public function get_combo_cidade_fornecedor($codigo_cliente, $uf = null)
	{

		//pega as cidades e estados do codigo unidade pesquisado
		$result = $this->FornecedorEndereco->get_combo_cidade($codigo_cliente, $uf);
		echo json_encode($result);
		die();
	} //fim get_combo_cidade_unidade

	/**
	 * [get_combo_cidade_unidade description]
	 * 
	 * metodo pego pelo ajax para popular o combo de cidade
	 * 
	 * @param  [type] $codigo_cliente [description]
	 * @return [type]                 [description]
	 */
	public function get_combo_estado_fornecedor($codigo_cliente)
	{

		//pega as estados do codigo unidade pesquisado
		$result = $this->FornecedorEndereco->get_combo_estado($codigo_cliente);
		echo json_encode($result);
		die();
	} //fim get_combo_cidade_unidade

	/**
	 * Metodo para buscar os dados agrupados sintetico e montar o relatorio
	 */
	public function resultado_de_exames()
	{
		//titulo da pagina
		$this->pageTitle = 'Resultados de exames';

		//inserido na filtros controller
		$filtros = $this->Filtros->controla_sessao($this->data, 'PedidoExame');

		if (!isset($filtros['codigo_cliente'])) {
			$filtros['codigo_cliente'] = null;
		}

		if (!isset($filtros['tipo_periodo'])) {
			$filtros['tipo_periodo'] = 'E';
		}

		if (!isset($filtros['agrupamento'])) {
			$filtros['agrupamento'] = 1;
		}

		if (empty($filtros['data_inicio'])) {
			$filtros['data_inicio'] = '01/' . date('m/Y');
			$filtros['data_fim'] = date('d/m/Y');
		}

		//pega o usuario que esta logdao
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		//pega os filtros setados que estao em sessao
		$this->data['PedidoExame'] = $filtros;

		$tipos_agrupamento = $this->PedidoExame->tiposAgrupamentoResultadoExames();

		$this->set(compact('tipos_agrupamento'));
		$this->carrega_combos_grupo_economico('PedidoExame');
		$this->carrega_combo_periodo();
	} //fim resultado_de_exames

	/**
	 * Metodo para apresentar a listagem dos dados
	 */
	public function resultado_de_exames_listagem()
	{

		//pega os filtros da sessão
		$filtros = $this->Filtros->controla_sessao($this->data, 'PedidoExame');
		//verifica o usuario logado
		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}
		//pega os filtros da sessao e seta em um array
		$this->data['PedidoExame'] = $filtros;
		//varialve auxiliar
		$dados = array();
		$agrupamento = $filtros['agrupamento'];

		if (!empty($filtros['codigo_cliente'])) {
			$conditions = $this->PedidoExame->converteFiltrosEmConditions($filtros);
			$dados = $this->PedidoExame->baixa_exames_sintetico2($agrupamento, $conditions);
		}

		$filtro_resultado = false;
		if (isset($filtros['agrupamento']) && !empty($filtros['agrupamento']) && $filtros['agrupamento'] == 5) {
			$filtro_resultado = true;
		}

		//seta para pegar os dados na view
		$this->set(compact('dados', 'agrupamento', 'filtro_resultado'));
	} // fim baixa_exames_sintetico_listagem() 


	/**
	 * Metod para trazer os resultados no detalhe do analitico
	 */
	public function resultado_exames()
	{
		$this->pageTitle = 'Resultados de Exames';
		$this->layout = 'new_window';

		$filtros = $this->Filtros->controla_sessao($this->data, 'PedidoExame');

		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$this->data['PedidoExame'] = $filtros;
		$this->carrega_combos_grupo_economico('PedidoExame');
		$this->carrega_combo_periodo();
		$this->carrega_combo_tipo_exame();
	} //fim baixa_exames_analitico

	/**
	 * Metodo com os detalhes analiticos de listagem
	 */
	public function resultado_exames_listagem($export = false)
	{
		// debug('opa');exit;

		$filtros = $this->Filtros->controla_sessao($this->data, 'PedidoExame');

		// debug($filtros);

		if (!empty($this->authUsuario['Usuario']['codigo_cliente'])) {
			$filtros['codigo_cliente'] = $this->authUsuario['Usuario']['codigo_cliente'];
		}

		$dados = array();
		if (!empty($filtros['codigo_cliente'])) {
			$conditions = $this->PedidoExame->converteFiltrosEmConditions($filtros);
			if ($export) {
				//monta a query
				$query_export = $this->PedidoExame->baixa_exames_analitico(null, $conditions);
				//faz a consulta pra exportar
				$query = $this->PedidoExame->find('sql', array('fields' => $query_export['fields'], 'conditions' => $query_export['conditions'], 'joins' => $query_export['joins']));
				//passa o resultado pra query pra monstar o csv
				$this->exportResultadoExames($query);
			}

			// $dados = $this->PedidoExame->baixa_exames_analitico('all', compact('conditions'));
			//monsta a query na model, por que estava dando problema de memoria, pra resolver é necessario termos que colocar a paginação na tela
			$dado = $this->PedidoExame->baixa_exames_analitico(null, $conditions);
			//paginate
			$this->paginate['PedidoExame'] = array(
				'recursive' => -1,
				'fields' => $dado['fields'],
				'joins' => $dado['joins'],
				'conditions' => $dado['conditions'],
				'limit' => 50
			);
			//printa o paginate pra ctp
			$dados = $this->paginate('PedidoExame');
		}

		// pr($dados);exit;

		$this->set(compact('dados'));
	} //fim baixa_resultado_exames_listagem

} //fim consultapedidoexamescontroller