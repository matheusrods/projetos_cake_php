<?php

App::import('Component', 'CachedAcl');

class ClienteProdutoContrato extends AppModel {

	var $name = 'ClienteProdutoContrato';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHhealth';
	var $useTable = 'cliente_produto_contrato';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	var $validate = array(
		// 'data_vigencia' => array(
		// 	'rule' => 'dataMaiorOuIgualAHoje',
		// 	'allowEmpty' => false,
		// 	'message' => 'Informe uma data válida.',
		// ),
		'data_contrato' => array(
			'rule' => 'dataMenorOuIgualAHoje',
			'allowEmpty' => true,
			'message' => 'Informe uma data válida.'
			),
		'data_envio' => array(
			'rule' => 'dataMenorOuIgualAHoje',
			'allowEmpty' => true,
			'message' => 'Informe uma data válida.'
			),
		'numero' => array(
			'rule' => 'isUnique',
			'message' => 'Já existe um contrato com este número.'
			),
		);

	var $belongsTo = array(
		'ClienteProduto' => array(
			'className' => 'ClienteProduto',
			'foreignKey' => 'codigo_cliente_produto'
			),
		'Produto' => array(
			'className' => 'Produto',
			'foreignKey' => false,
			'conditions' => 'Produto.codigo = ClienteProduto.codigo_produto'
			),
		'Cliente' => array(
			'className' => 'Cliente',
			'foreignKey' => false,
			'conditions' => 'Cliente.codigo = ClienteProduto.codigo_cliente'
			)
		);


	function converteFiltroEmCondition($dados) {
		$conditions = array();
		if ((!empty($dados['data_inicial']) && $this->validaData($dados['data_inicial'])) && (!empty($dados['data_final']) && $this->validaData($dados['data_final']))) {
			$dados['data_inicial'] .= ' 00:00:00';
			$dados['data_final']   .= ' 23:59:59';
			$conditions = array('ClienteProdutoContrato.data_vigencia BETWEEN ? AND ?' => array(AppModel::dateToDbDate2($dados['data_inicial']), AppModel::dateToDbDate2($dados['data_final'])));
		}
		if (!empty($dados['codigo_produto']) && $dados['codigo_produto'] != 0)
			$conditions['ClienteProduto.codigo_produto'] = $dados['codigo_produto'];
		if (!empty($dados['codigo_cliente']))
			$conditions['codigo_cliente'] = $dados['codigo_cliente'];
		if (!empty($dados['data_contrato']))
			$conditions['ClienteProdutoContrato.data_contrato'] =  AppModel::dateToDbDate($dados['data_contrato']);
		if (!empty($dados['data_vigencia']))
			$conditions['ClienteProdutoContrato.data_vigencia'] = AppModel::dateToDbDate($dados['data_vigencia']);
		return $conditions;
	}

	
	function paginate($conditions, $fields, $order, $limit = 100, $page = 1, $recursive = -1, $extra = array()) {
		$this->unbindModel(array('belongsTo' => array('ClienteProdutoServico2')));
		$this->unbindModel(array('belongsTo' => array('ClienteProduto')));
		$this->unbindModel(array('belongsTo' => array('Cliente')));
		$this->unbindModel(array('belongsTo' => array('Produto')));
		
		$this->Produto = &ClassRegistry::init('Produto');
		$this->Cliente = &ClassRegistry::init('Cliente');
		$this->ClienteProduto = &ClassRegistry::init('ClienteProduto');
		$this->ClienteProdutoServico2 = &ClassRegistry::init('ClienteProdutoServico2');
		
		$fields = array(
			'Cliente.codigo',
			'Cliente.razao_social AS razao_social',
			'Produto.descricao AS descricao',
			'ClienteProduto.codigo',
			'ClienteProdutoContrato.numero',
			'ClienteProdutoContrato.data_vigencia',
			);

		$group = array(
			'Cliente.codigo',
			'Cliente.razao_social',
			'Produto.descricao',
			'ClienteProduto.codigo',
			'ClienteProdutoContrato.numero',
			'ClienteProdutoContrato.data_vigencia',
			);
		
		//$group = $fields;
		
		$joins = array(
			array(
				'table' => "{$this->ClienteProduto->databaseTable}.{$this->ClienteProduto->tableSchema}.{$this->ClienteProduto->useTable}",
				'alias' => 'ClienteProduto',
				'type' => 'LEFT',
				'conditions' => array('ClienteProdutoContrato.codigo_cliente_produto = ClienteProduto.codigo')
				),
			array(
				'table' => "{$this->ClienteProdutoServico2->databaseTable}.{$this->ClienteProdutoServico2->tableSchema}.{$this->ClienteProdutoServico2->useTable}",
				'alias' => 'ClienteProdutoServico2',
				'type' => 'RIGHT',
				'conditions' => array('ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto.codigo')
				),
			array(
				'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'LEFT',
				'conditions' => array('ClienteProduto.codigo_cliente = Cliente.codigo')
				),
			array(
				'table' => "{$this->Produto->databaseTable}.{$this->Produto->tableSchema}.{$this->Produto->useTable}",
				'alias' => 'Produto',
				'type' => 'LEFT',
				'conditions' => array('ClienteProduto.codigo_produto = Produto.codigo')
				),
			);

		$order = array('razao_social','descricao');			
		
		$conditions['ClienteProduto.codigo_motivo_bloqueio <> '] = 3;

		return $this->find('all', compact('fields', 'conditions', 'joins', 'group', 'limit', 'page', 'recursive', 'order'));
	}
	
	function paginateCount($conditions = null, $recursive = -1, $extra = array()) {
		$this->unbindModel(array('belongsTo' => array('ClienteProdutoServico2')));
		$this->unbindModel(array('belongsTo' => array('ClienteProduto')));
		$this->unbindModel(array('belongsTo' => array('Cliente')));
		$this->unbindModel(array('belongsTo' => array('Produto')));
		
		$this->ClienteProdutoServico2 = &ClassRegistry::init('ClienteProdutoServico2');
		$this->ClienteProduto = &ClassRegistry::init('ClienteProduto');
		$this->Produto = &ClassRegistry::init('Produto');
		$this->Cliente = &ClassRegistry::init('Cliente');

		$fields = array(
			'Cliente.codigo',
			'Cliente.razao_social',
			'Produto.descricao',
			'ClienteProduto.codigo',
			'ClienteProdutoContrato.numero',
			'ClienteProdutoContrato.data_vigencia',
			);
		
		$group = $fields;
		
		$joins = array(
			array(
				'table' => "{$this->ClienteProduto->databaseTable}.{$this->ClienteProduto->tableSchema}.{$this->ClienteProduto->useTable}",
				'alias' => 'ClienteProduto',
				'type' => 'LEFT',
				'conditions' => array('ClienteProdutoContrato.codigo_cliente_produto = ClienteProduto.codigo')
				),
			array(
				'table' => "{$this->ClienteProdutoServico2->databaseTable}.{$this->ClienteProdutoServico2->tableSchema}.{$this->ClienteProdutoServico2->useTable}",
				'alias' => 'ClienteProdutoServico2',
				'type' => 'RIGHT',
				'conditions' => array('ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto.codigo')
				),
			array(
				'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'LEFT',
				'conditions' => array('ClienteProduto.codigo_cliente = Cliente.codigo')
				),
			array(
				'table' => "{$this->Produto->databaseTable}.{$this->Produto->tableSchema}.{$this->Produto->useTable}",
				'alias' => 'Produto',
				'type' => 'LEFT',
				'conditions' => array('ClienteProduto.codigo_produto = Produto.codigo')
				),
			);
		$conditions['ClienteProduto.codigo_motivo_bloqueio <> '] = 3;
		$query 	= $this->find('sql', compact('conditions', 'recursive', 'joins','group','fields'));
		$query 	= "SELECT count(1) AS total FROM ({$query}) AS contratos";
		$result	= $this->query($query);
		return $result[0][0]['total'];
	}
	
	public function buscarPorCodigoClienteEProduto($codigo_cliente, $codigo_produto) {
		return $this->find('first', array(
			'conditions' => array(
				'ClienteProduto.codigo_cliente' => $codigo_cliente,
				'ClienteProduto.codigo_produto' => $codigo_produto
				),
			));
	}

	function parentNode() {
		return null;
	}

	function dataMenorOuIgualAHoje($check, $validate) {
		$check = array_values($check);
		$check = $this->dateToDbDate($check[0]);

		if (!isset($check))
			return false;
		return Date('Ymd', strtotime($check)) <= Date('Ymd');
	}

	function dataMaiorOuIgualAHoje($check, $validate) {
		$check = array_values($check);
		$check = $this->dateToDbDate($check[0]);

		if (!isset($check))
			return false;
		return Date('Ymd', strtotime($check)) >= Date('Ymd');
	}

	function listar($conditions = null, $limit = null) {
		return $this->find('all', array('conditions' => $conditions, 'limit' => $limit));
	}

	public function gerarNumeroContrato($codigo_cliente_produto) {
		$cliente_produto = $this->ClienteProduto->findByCodigo($codigo_cliente_produto);

		$cliente_produto_contrato = $this->find('count', array('conditions' => array('ClienteProdutoContrato.codigo_cliente_produto' => $codigo_cliente_produto)));
		$cliente_produto_contrato = $cliente_produto_contrato + 1;

		$numero_contrato = date('Ymd');
		$numero_contrato .= str_pad($cliente_produto['ClienteProduto']['codigo_cliente'], 5, 0, STR_PAD_LEFT);
		$numero_contrato .= str_pad($cliente_produto['ClienteProduto']['codigo_produto'], 2, 0, STR_PAD_LEFT);
		$numero_contrato .= str_pad($cliente_produto_contrato, 2, 0, STR_PAD_LEFT);

		return $numero_contrato;
	}
	
	public function atualizarDataVigencia($codigo_cliente_produto){
		$contratos = $this->find('all', array('conditions' => array('codigo_cliente_produto' => $codigo_cliente_produto), 'recursive' => -1));
		foreach($contratos as &$contrato) {
			$contrato['ClienteProdutoContrato']['data_vigencia'] = date('d/m/Y H:i:s', strtotime('+1 year', strtotime(preg_replace("/(\d{2})\/(\d{2})\/(\d{2,4})(\w*)/", "$3$2$1$4", $contrato['ClienteProdutoContrato']['data_vigencia']))));
			if (!parent::atualizar($contrato))
				return false;
		}
		return true;
	}

	public function atualizarRegistro($data){
		if (isset($data['ClienteProdutoContrato']['codigo_cliente_produto']) && !empty($data['ClienteProdutoContrato']['codigo_cliente_produto']) && isset($data['ClienteProdutoContrato']['arquivo_contrato']) && !empty($data['ClienteProdutoContrato']['arquivo_contrato'])) {
			$this->ClienteProduto->read(null, $data['ClienteProdutoContrato']['codigo_cliente_produto']);
			$this->ClienteProduto->saveField('possui_contrato', true);
		}
		return parent::atualizar($data);
	}

	public function atualizarContratos($filtros) {
		$this->ClienteProdutoServico2Log=& ClassRegistry::init('ClienteProdutoServico2Log');
		$this->ClienteProdutoServico2	=& ClassRegistry::init('ClienteProdutoServico2');
		$this->ClienteProdutoContrato	=& ClassRegistry::init('ClienteProdutoContrato');

		$conditions = $this->converteFiltroEmCondition($filtros);
		$filtros['data_inicial'] = $this->dateTimeToDbDateTime2($filtros['data_inicial']).' 00:00:00';
		$filtros['data_final'] 	 = $this->dateTimeToDbDateTime2($filtros['data_final']).' 23:59:59';

		try {
			ini_set('max_execution_time', 0);
			set_time_limit(0);
			$this->query('BEGIN TRANSACTION');

			if (!$this->ClienteProdutoServico2Log->incluirLogsParaContrato($conditions)) throw new Exception("Erro ao incluir logs");
			if (!$this->ClienteProdutoServico2->atualizarValores($filtros)) throw new Exception("Erro ao atualizar valores dos servicos");
			if (!$this->atualizarDatasVigencia($filtros)) throw new Exception("Erro ao atualizar data de vigencia dos contratos");

			$this->commit();
			return true;
		} catch (Exception $ex) {
			$this->rollback();
			return false;
		}
	}
	
	function atualizarDatasVigencia($filtros) {
		$this->ClienteProduto		  =& ClassRegistry::init('ClienteProduto');
		$this->ClienteProdutoServico2  =& ClassRegistry::init('ClienteProdutoServico2');
		
		$query = "UPDATE
		{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
		SET
		data_vigencia = dateadd(YEAR, 1, data_vigencia)
		FROM
		{$this->databaseTable}.{$this->tableSchema}.{$this->useTable} AS ClienteProdutoContrato
		INNER JOIN {$this->ClienteProduto->databaseTable}.{$this->ClienteProduto->tableSchema}.{$this->ClienteProduto->useTable} AS ClienteProduto
		ON ClienteProdutoContrato.codigo_cliente_produto = ClienteProduto.codigo 
		INNER JOIN {$this->ClienteProdutoServico2->databaseTable}.{$this->ClienteProdutoServico2->tableSchema}.{$this->ClienteProdutoServico2->useTable} AS ClienteProdutoServico2
		ON ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto.codigo
		WHERE
		ClienteProdutoContrato.data_vigencia BETWEEN '{$filtros['data_inicial']}' AND '{$filtros['data_final']}' AND
		ClienteProduto.codigo_motivo_bloqueio <> 3";
		
		if (isset($filtros['codigo_produto']) && $filtros['codigo_produto'])
			$query = $query." AND ClienteProduto.codigo_produto = {$filtros['codigo_produto']}";

		return ($this->query($query) !== false);
	}
	
	function obterDadosParaContrato($dados = null) {
		if(!$dados) return false;
		$codigo_cliente_produto = $dados['ClienteProdutoContrato']['codigo_cliente_produto'];
		$codigo_cliente = $this->ClienteProduto->find('first', array('conditions' => array('ClienteProduto.codigo' => $codigo_cliente_produto), 'fields' => array('ClienteProduto.codigo_cliente')));
		$cliente = $this->Cliente->find('first', array('conditions' => array('Cliente.codigo' => $codigo_cliente['ClienteProduto']['codigo_cliente'])));
		return (!is_array($cliente)) ? false: array_merge($dados, $cliente);
	}
	
	function obterContratosParaAtualizacao($dados = null) {
		if(!$dados) return false;
		
		$this->unbindModel(array('belongsTo' => array('ClienteProdutoServico2')));
		$this->unbindModel(array('belongsTo' => array('ClienteProduto')));
		$this->unbindModel(array('belongsTo' => array('Cliente')));
		$this->unbindModel(array('belongsTo' => array('Produto')));
		
		$ClienteProdutoServico2 = &ClassRegistry::init('ClienteProdutoServico2');
		$ClienteProduto = &ClassRegistry::init('ClienteProduto');
		$Produto = &ClassRegistry::init('Produto');
		$Servico = &ClassRegistry::init('Servico');
		$Cliente = &ClassRegistry::init('Cliente');
		$ProfissionalTipo = &ClassRegistry::init('ProfissionalTipo');
		
		$fields = array(
			'Cliente.codigo',
			'Cliente.razao_social',
			'Produto.descricao',
			'ClienteProdutoContrato.numero',
			'ClienteProdutoContrato.data_contrato',
			'ClienteProdutoContrato.data_vigencia',
			'ClienteProdutoServico2.valor',
			'ClienteProdutoServico2.codigo',
			'ProfissionalTipo.descricao',
			'Servico.descricao'
			);
		
		$joins = array(
			array(
				'table' => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
				'alias' => 'ClienteProduto',
				'type' => 'LEFT',
				'conditions' => array('ClienteProdutoContrato.codigo_cliente_produto = ClienteProduto.codigo')
				),
			array(
				'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'LEFT',
				'conditions' => array('ClienteProduto.codigo_cliente = Cliente.codigo')
				),
			array(
				'table' => "{$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}",
				'alias' => 'ClienteProdutoServico2',
				'type' => 'LEFT',
				'conditions' => array('ClienteProduto.codigo = ClienteProdutoServico2.codigo_cliente_produto')
				),
			array(
				'table' => "{$Produto->databaseTable}.{$Produto->tableSchema}.{$Produto->useTable}",
				'alias' => 'Produto',
				'type' => 'LEFT',
				'conditions' => array('ClienteProduto.codigo_produto = Produto.codigo')
				),
			array(
				'table' => "{$Servico->databaseTable}.{$Servico->tableSchema}.{$Servico->useTable}",
				'alias' => 'Servico',
				'type' => 'LEFT',
				'conditions' => array('ClienteProdutoServico2.codigo_servico = Servico.codigo')
				),
			array(
				'table' => "{$ProfissionalTipo->databaseTable}.{$ProfissionalTipo->tableSchema}.{$ProfissionalTipo->useTable}",
				'alias' => 'ProfissionalTipo',
				'type' => 'LEFT',
				'conditions' => array('ClienteProdutoServico2.codigo_profissional_tipo = ProfissionalTipo.codigo')
				)
			);
		
		$dados['ClienteProdutoContrato']['data_inicial'] .= ' 00:00:00';
		$dados['ClienteProdutoContrato']['data_final']   .= ' 23:59:59';
		$conditions = array('ClienteProdutoContrato.data_vigencia BETWEEN ? AND ?' => array(AppModel::dateToDbDate2($dados['ClienteProdutoContrato']['data_inicial']), AppModel::dateToDbDate2($dados['ClienteProdutoContrato']['data_final'])));
		if ($dados['ClienteProdutoContrato']['codigo_produto'] != 0)
			$conditions['ClienteProduto.codigo_produto'] = $dados['ClienteProdutoContrato']['codigo_produto'];
		return $this->find('all', compact('fields', 'conditions', 'joins', 'group'));
	}

	private function validaData($data)
	{
		$data = explode('/', $data);
		return checkdate($data[1], $data[0], $data[2]);
	}
}