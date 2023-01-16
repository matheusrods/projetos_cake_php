<?php
App::import('Model', 'Servico');
class ClientEmpresa extends AppModel {

	var $name = 'ClientEmpresa';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'client_empresas';
	var $displayField = 'Raz_Social';
	var $primaryKey = 'Codigo';
	var $actsAs = array('Secure');
	var $validate = array(                        
        'CNPJCPF' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'CNPJCPF em branco'
            ),
            'documentoValido' => array(
                'rule' => 'documentoValido',
                'message' => 'CNPJ/CPF inválido'
            ),
        ),
    );

	const TIPO_EMPRESA_EMBARCADOR = 1;
	const TIPO_EMPRESA_SEGURADORA = 2;
	const TIPO_EMPRESA_CORRETORA = 3;
	const TIPO_EMPRESA_TRANSPORTADORA = 4;

	const SENTIDO_BUONNY_MONITORA = 1;
	const SENTIDO_MONITORA_BUONNY = 2;
	
	function retornaCodigoOperador($codigo_cliente, $CodEquipamento) {
		$dados = $this->find('first', array('fields' => 'tipo_operacao', 'conditions' => array('ClientEmpresa.Codigo' => $codigo_cliente)));
		$tipo_operacao = $dados['ClientEmpresa']['tipo_operacao'];
		switch($tipo_operacao) {
			case 1:
				return '001018';
			case 24:
				return '001513';
			case 53:
				return '001581';
			case 46:
				return '001546';
			default:
				switch($CodEquipamento) {
					case '000004':
						return '001373';
					case '000001':
						return '001583';
					case '000010':
						return '001383';
					case '000018':
						return '001382';
					case '000012':
						return '001586';
					default:
						return '001584';
				}
		}
	}

	function validaCNPJ(){
		$Documento = ClassRegistry::init('Documento');		
		return $Documento->isCNPJ($this->data['ClientEmpresa']['CNPJCPF']);
	}

	function documentoValido() {
		$model_documento = & ClassRegistry::init('Documento');
		$codigo_documento = $this->data[$this->name]['CNPJCPF'];
		if($model_documento->isCPF($codigo_documento) == false && $model_documento->isCNPJ($codigo_documento) == false)
			return false;
		else
			return true;
	}

	function produtosMonitoraNaveg() {
		$produtos = $this->find('all', array('fields' => 'produto_naveg', 'group' => 'produto_naveg'));
		return Set::extract($produtos, '/ClientEmpresa/produto_naveg');
	}

	function porCodigoCliente($codigo_cliente, $tipo = 'all', $tipo_empresa = null, $fields = null) {
		if ($tipo === 'all')
			$fields = array('codigo', 'codigoinformacoes', 'raz_social', 'telefone', 'cnpjcpf','produto_naveg','tipo_operacao','integracao_faturamento','codigo_cliente',);

		$conditions = array($this->name.'.codigo_cliente' => $codigo_cliente);
		if(!is_null($tipo_empresa))
			$conditions['TipoEmpresa'] = $tipo_empresa;
		return $this->find($tipo, array('fields' => $fields, 'conditions' => $conditions));
	}

	function porCodigo($codigo_cliente, $tipo = 'first', $fields = null) {
		if (is_null($fields))
			$fields = array('codigo', 'codigoinformacoes', 'raz_social', 'telefone', 'cnpjcpf','produto_naveg','tipo_operacao','integracao_faturamento','codigo_cliente','codigopgr');

		$conditions = array($this->name.'.codigo' => $codigo_cliente);

		return $this->find($tipo, compact('conditions','fields'));
	}

	function porCodigoInformacoes($codigo_cliente, $tipo = 'all') {
		$conditions = array($this->name.'.CodigoInformacoes' => $codigo_cliente);
		return $this->find($tipo, compact('conditions'));
	}

	function tipoEmpresa($codigo) {
		$result = $this->read('TipoEmpresa', $codigo);
		if ($result)
			return $result[key($result)]['TipoEmpresa'];
		return false;
	}

	function porCnpj($cnpj, $somente_ativos = false, $matriz=false){
		if(empty($cnpj))
			return false;
		$conditions = array('codigo_documento' => Comum::soNumero($cnpj));
		if ($somente_ativos) {
			$conditions['Status'] = 'S';
		}
		$fields = array('codigo', "codigo+' - '+raz_social AS raz_social");
		if( $matriz === TRUE ){
			$order = array($this->primaryKey);
			$limit = 1;
			return $results = $this->find('all', compact('conditions', 'fields', 'order', 'limit'));
		}
		$results = $this->find('all', compact('conditions', 'fields'));
		$return = array();
		foreach ($results as $result) {
			$return[$result['ClientEmpresa']['codigo']] = $result['0']['raz_social'];
		}
		return $return;
	}

	function porBaseCnpj($baseCnpj, $tipo_empresa = null){
		if(empty($baseCnpj))
			return false;
		$baseCnpj = Comum::soNumero($baseCnpj);
		$baseCnpj = preg_replace("/(\d{2})(\d{3})(\d{3})(\w*)/", "$1.$2.$3", $baseCnpj);		

		$conditions = array('cnpjcpf like' => $baseCnpj.'%');
		if ($tipo_empresa != null)
			$conditions['tipoempresa'] = $tipo_empresa;

		$order = array($this->primaryKey);
		return $this->find('list', array('conditions' => $conditions,'order' => $order));
	}

	function porBaseCnpjPersonalisada($baseCnpj, $fields = null){
		$lista = $this->porBaseCnpj($baseCnpj);
		$lista = array_keys($lista);
		if($lista){
			$conditions = array('ClientEmpresa.codigo' => $lista);
			$order = array($this->primaryKey);
			return $this->find('list', compact('conditions','fields','order'));
		}

		return false;
	}

	// Cuidado ao listar o campo do tipo DATA, DATATIME e afins, é necessário fazer tratamento na chamada dos mesmos.
	function carregarPorCnpjCpf($cnpjCpf, $tipo_find = 'list', $fields = null,$tipo_operacao = null) {
	  $cnpjCpf = Comum::soNumero($cnpjCpf);
	  if ((int)$cnpjCpf > 0) {
		  $conditions 	= array('codigo_documento' => $cnpjCpf);
		  if($tipo_operacao){
		  	$conditions['tipo_operacao'] = $tipo_operacao;
		  }
		  $order = array($this->primaryKey);
		  return $this->find($tipo_find, compact('conditions','fields','order'));
	  }
	  return array();
	}

	function porCodigoDbBuonny($codigo,$type = 'list'){
		$Cliente 		= classRegistry::Init('Cliente');
		$cliente 		= $Cliente->find('first',array('conditions' => array('Cliente.codigo' => $codigo)));

		if($cliente){
			$client_empresa = $this->carregarPorCnpjCpf($cliente['Cliente']['codigo_documento'],$type);
			return (($client_empresa)?$client_empresa:array());
		} else {
			return array();
		}
	}

	function pegaCidade($codigo_empresa){
		$this->bindModel(array('hasOne' => array(
			'MClientEmpresaEndereco' => array('foreignKey' => 'cli_codigo'),
		)));
		$cidade = $this->find('first', array('fields' => array('MClientEmpresaEndereco.end_municipio as municipio', 'MClientEmpresaEndereco.end_estado as estado'), 'conditions' => array($this->name.'.Codigo' => $codigo_empresa)));
		if ($cidade) {
			return $cidade['0'];
		}
		return null;
	}

	function carregar($codigo) {
		return $this->find('first', array('fields' => array('Codigo', 'Raz_Social', 'CNPJCPF', 'TipoEmpresa', 'tipo_operacao','CodigoInformacoes','codigo_documento'), 'conditions' => array('Codigo' => str_pad(trim($codigo), 6, '0', STR_PAD_LEFT))));
	}

	function converteCodigoClienteBuonnyMonitora($codigo, $sentido) {
		if ($sentido == self::SENTIDO_MONITORA_BUONNY) {
			$cliente = $this->find('all', array('fields' => 'codigo_cliente', 'conditions' => array('codigo' => $codigo)));
			return Set::extract('/ClientEmpresa/codigo_cliente', $cliente);
		} elseif ($sentido == self::SENTIDO_BUONNY_MONITORA) {
			$cliente = $this->find('all', array('fields' => 'codigo', 'conditions' => array('codigo_cliente' => $codigo)));
			if ($cliente)
				return Set::extract('/ClientEmpresa/codigo', $cliente);
			else
				return array('000000');
		}
	}

	function listarClientesMonitoraForaPortal() {

		$Cliente =& ClassRegistry::init('Cliente');

		$dbo = $this->getDataSource();
		$clientes_portal = $dbo->buildStatement(
			array(
				'fields' => array('codigo_documento'),
				'table' => $Cliente->databaseTable.'.'.$Cliente->tableSchema.'.'.$Cliente->useTable,
				'alias' => '[Cliente] WITH (NOLOCK)',
				'limit' => null,
				'offset' => null,
				'conditions' => null,
				'order' => null,
				'group' => null,
				), $this
		);
		
		$joins = array(
			array(
				'table' => "({$clientes_portal})",
				'alias' => 'ClientesPortal',
				'type' => 'LEFT',
				'conditions' => array("ClientEmpresa.codigo_documento = ClientesPortal.codigo_documento"),
			),
		);

		$query_clientes_monitora = $dbo->buildStatement(
			array(
				'fields' => array('CNPJCPF','Raz_Social','ISCEstadual','ISCMunicipal','Faturamento','TipoEmpresa','Regiao','FUN_CORRETORA','FUN_SEGURADORA'),
				'table' => $this->databaseTable.'.'.$this->tableSchema.'.'.$this->useTable,
				'alias' => '[ClientEmpresa] WITH (NOLOCK)',
				'limit' => null,
				'joins' => $joins,
				'offset' => null,
				'conditions' => array('ClientesPortal.codigo_documento IS NULL','ClientEmpresa.CNPJCPF IS NOT NULL',"ClientEmpresa.CNPJCPF <> '0'","BloqFinanc <> 'S'", "Status = 'S'","TipoEmpresa IN ('1','4')"),
				'order' => null,
				'group' => null,
				), $this
		);

		$clientes_monitora = $this->query($query_clientes_monitora);

		return($clientes_monitora);

	}

//Por Pagador
	function estatisticaPorClientePagador2($filtros, $detalhar_filhos = false, $retornar_instrucao_sql = false, $integracao = false, $verificar_falha = false) {
		$filtros['data_inicial'] .= ' 00:00:00';
		$filtros['data_final'] 	 .= ' 23:59:59';

		$Cliente 				 =& ClassRegistry::init('Cliente');
		$ClienteEndereco 		 =& ClassRegistry::init('ClienteEndereco');
		$ClienteProduto 		 =& ClassRegistry::init('ClienteProduto');
		$ClienteProdutoDesconto  =& ClassRegistry::init('ClienteProdutoDesconto');
		$ClienteProdutoServico2  =& ClassRegistry::init('ClienteProdutoServico2');
		$EmbarcadorTransportador =& ClassRegistry::init('EmbarcadorTransportador');
		$Documento 				 =& ClassRegistry::init('Documento');
		$Recebsm 				 =& ClassRegistry::init('Recebsm');
		$TipoFrota 			     =& ClassRegistry::init('TipoFrota');
		$Veiculo				 =& ClassRegistry::init('Veiculo');
		$ClienteVeiculo			 =& ClassRegistry::init('ClienteVeiculo');

		$dbo = $this->getDataSource();

		$conditions = array(
			'Recebsm.data_final BETWEEN ? AND ?' => array(AppModel::dateToDbDate($filtros['data_inicial']), AppModel::dateToDbDate($filtros['data_final'])),
			'Recebsm.encerrada' => 'S',
			'Recebsm.cliente_pagador > 0',
		);

		if ($integracao)
			$conditions['Recebsm.codigo_item_pedido'] = null;
		if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
			unset($conditions['recebsm.cliente_pagador']);
			$conditions['Recebsm.cliente_pagador'] = $filtros['codigo_cliente'];
		}

		$frota = $ClienteProdutoServico2->frotaPorPagador($filtros, true, true);

		$joins = array(
			array(
				'table' 	 => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
				'alias' 	 => '[ClientEmpresa] WITH (NOLOCK)',
				'type' 		 => 'LEFT',
				'conditions' => array('ClientEmpresa.Codigo = Recebsm.cliente'),
			),
			array(
				'table'		 => "{$Veiculo->databaseTable}.{$Veiculo->tableSchema}.{$Veiculo->useTable}",
				'alias'		 => "Veiculo",
				"type"		 => "LEFT",
				"conditions" => array("Veiculo.placa = REPLACE(Recebsm.placa,'-','')"),
			),
			array(
				'table'		 => "({$frota})",
				'alias'		 => "Frota",
				"type"		 => "LEFT",
				"conditions" => array(
					"Frota.codigo_veiculo = Veiculo.codigo",
					"Frota.codigo_cliente = Recebsm.cliente_pagador",
				),
			),
			array(
				'table'		 => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
				'alias'		 => "ClienteProduto",
				"type"		 => "LEFT",
				"conditions" => array(
					"ClienteProduto.codigo_cliente = Recebsm.cliente_pagador",
					"ClienteProduto.codigo_produto" => Produto::BUONNYSAT,
				),
			),
			array(
				'table'		 => "{$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}",
				'alias'		 => "ClienteProdutoServico2Frota",
				"type"		 => "LEFT",
				"conditions" => array(
					"ClienteProdutoServico2Frota.codigo_cliente_produto = ClienteProduto.codigo",
					"ClienteProdutoServico2Frota.codigo_servico" => Servico::PLACA_FROTA,
					"ClienteProdutoServico2Frota.codigo_cliente_pagador = ClienteProduto.codigo_cliente",
					array(
						"OR" => array(
							"ClienteProduto.codigo_motivo_bloqueio" => 1,
							"ClienteProduto.data_inativacao >=" => AppModel::dateToDbDate($filtros['data_inicial']),
						),
					)
				),
			),
			array(
				'table'		 => "{$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}",
				'alias'		 => "ClienteProdutoServico2Avulso",
				"type"		 => "LEFT",
				"conditions" => array(
					"ClienteProdutoServico2Avulso.codigo_cliente_produto = ClienteProduto.codigo",
					"ClienteProdutoServico2Avulso.codigo_servico" => Servico::PLACA_AVULSA,
					"ClienteProdutoServico2Avulso.codigo_cliente_pagador = ClienteProduto.codigo_cliente",
					array(
						"OR" => array(
							"ClienteProduto.codigo_motivo_bloqueio" => 1,
							"ClienteProduto.data_inativacao >=" => AppModel::dateToDbDate($filtros['data_inicial']),
						),
					),
				),
			),
			array(
				'table'		 => "{$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}",
				'alias'		 => "ClienteProdutoServico2Sm",
				"type"		 => "LEFT",
				"conditions" => array(
					"ClienteProdutoServico2Sm.codigo_cliente_produto = ClienteProduto.codigo",
					"ClienteProdutoServico2Sm.codigo_servico" => Servico::SM,
					"ClienteProdutoServico2Sm.codigo_cliente_pagador = ClienteProduto.codigo_cliente",
				),
			),
			array(
				'table'		 => "{$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}",
				'alias'		 => "ClienteProdutoServico2SmTele",
				"type"		 => "LEFT",
				"conditions" => array(
					"ClienteProdutoServico2SmTele.codigo_cliente_produto = ClienteProduto.codigo",
					"ClienteProdutoServico2SmTele.codigo_servico" => Servico::SM_TELE,
					"ClienteProdutoServico2SmTele.codigo_cliente_pagador = ClienteProduto.codigo_cliente",
				),
			),
			array(
				'table'		 => "{$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}",
				'alias'		 => "ClienteProdutoServico2Dia",
				"type"		 => "LEFT",
				"conditions" => array(
					"ClienteProdutoServico2Dia.codigo_cliente_produto = ClienteProduto.codigo",
					"ClienteProdutoServico2Dia.codigo_servico" => Servico::DIA,
					"ClienteProdutoServico2Dia.codigo_cliente_pagador = ClienteProduto.codigo_cliente",
				),
			),
			array(
				'table'		 => "{$ClienteProdutoServico2->databaseTable}.{$ClienteProdutoServico2->tableSchema}.{$ClienteProdutoServico2->useTable}",
				'alias'		 => "ClienteProdutoServico2Km",
				"type"		 => "LEFT",
				"conditions" => array(
					"ClienteProdutoServico2Km.codigo_cliente_produto = ClienteProduto.codigo",
					"ClienteProdutoServico2Km.codigo_servico" => Servico::KM,
					"ClienteProdutoServico2Km.codigo_cliente_pagador = ClienteProduto.codigo_cliente",
				),
			),
		);

		$group = array(
			'Recebsm.cliente_pagador', 
			'Recebsm.cliente',
			'Recebsm.placa',
			'CASE WHEN ClienteProdutoServico2Frota.codigo IS NOT NULL AND Frota.codigo_veiculo IS NOT NULL THEN recebsm.placa ELSE null END',
			'CASE WHEN (ClienteProdutoServico2Avulso.codigo IS NOT NULL OR (ClienteProdutoServico2Sm.codigo IS NULL AND ClienteProdutoServico2SmTele.codigo IS NULL AND ClienteProdutoServico2Dia.codigo IS NULL AND ClienteProdutoServico2Km.codigo IS NULL)) AND Frota.codigo_veiculo IS NULL THEN recebsm.placa ELSE null END',
		);
		$codigo_telemonitorado = "'000012'";
		$fields = array(
			'Recebsm.cliente_pagador',
			'Recebsm.cliente',
			'Recebsm.placa',
			'CASE WHEN ClienteProdutoServico2Frota.codigo IS NOT NULL AND Frota.codigo_veiculo IS NOT NULL THEN recebsm.placa ELSE null END AS placa_frota',
			'CASE WHEN (ClienteProdutoServico2Avulso.codigo IS NOT NULL OR (ClienteProdutoServico2Sm.codigo IS NULL AND ClienteProdutoServico2SmTele.codigo IS NULL AND ClienteProdutoServico2Dia.codigo IS NULL AND ClienteProdutoServico2Km.codigo IS NULL)) AND Frota.codigo_veiculo IS NULL THEN recebsm.placa ELSE null END AS placa_avulso',
			"SUM(CASE WHEN ClienteProdutoServico2Km.codigo IS NOT NULL THEN CONVERT(DECIMAL(10,3), REPLACE(Recebsm.distancia_viagem,',','.')) ELSE 0 END) as qtd_km",
			'SUM(CASE WHEN ClienteProdutoServico2Dia.codigo IS NOT NULL THEN DATEDIFF(day, Recebsm.data_inicio, Recebsm.data_final)+1 ELSE 0 END) as qtd_dia',
			"SUM(CASE WHEN Recebsm.CodEquipamento <> {$codigo_telemonitorado} AND ClienteProdutoServico2Sm.codigo IS NOT NULL AND (ClienteProdutoServico2Frota.codigo IS NULL OR Frota.codigo_veiculo IS NULL) THEN 1 ELSE 0 END) as qtd_sm_fora_frota",
			"SUM(CASE WHEN Recebsm.CodEquipamento = {$codigo_telemonitorado} AND ClienteProdutoServico2SmTele.codigo IS NOT NULL AND (ClienteProdutoServico2Frota.codigo IS NULL OR Frota.codigo_veiculo IS NULL) THEN 1 ELSE 0 END) as qtd_sm_fora_frota_telemonitorada",
		);

		$viagens = $dbo->buildStatement(
			array(
				'fields' 	 => $fields,
				'table' 	 => $Recebsm->databaseTable.'.'.$Recebsm->tableSchema.'.'.$Recebsm->useTable,
				'alias' 	 => '[Recebsm] WITH (NOLOCK'.(Ambiente::getServidor() == Ambiente::SERVIDOR_PRODUCAO ? ', INDEX(ix_dta_fim__encerrada__cliente_pagador_codigo_item_pedido))' : ')'),
				'limit' 	 => null,
				'offset' 	 => null,
				'joins' 	 => $joins,
				'conditions' => $conditions,
				'order' 	 => null,
				'group' 	 => $group,
				), $this
		);

		$por_dia = $dbo->buildStatement(
		  array(
				'fields' => array(
					'ISNULL(valor, 0) as valor_unitario_dia',
					'ISNULL(ClienteProdutoServico2.valor_premio_minimo,0) as valor_premio_minimo_dia' , 
					'ISNULL(ClienteProdutoServico2.qtd_premio_minimo,0) as qtd_premio_minimo_dia' , 
					'ClienteProduto2.codigo_cliente'
				),
				'table' => $ClienteProdutoServico2->databaseTable.'.'.$ClienteProdutoServico2->tableSchema.'.'.$ClienteProdutoServico2->useTable,
				'alias' => '[ClienteProdutoServico2] WITH (NOLOCK)',
				'limit' => null,
				'offset' => null,
				'joins' => array( 
					array(
						'table' 	 => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
						'alias' 	 => '[ClienteProduto2] WITH (NOLOCK)',
						'type' 		 => 'LEFT',
						'conditions' => array(
							'ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto2.codigo',
							'ClienteProduto2.codigo_produto' => 82,
						),
					)
				),
				'conditions' => array('ClienteProdutoServico2.codigo_servico' => 29),
				'order' => null,
				'group' => null,
				), $this
		);

		$por_km = $dbo->buildStatement(
		  array(
				'fields' => array(
					'ISNULL(valor, 0) as valor_unitario_km',
					'ISNULL(ClienteProdutoServico2.valor_premio_minimo,0) as valor_premio_minimo_km',
					'ISNULL(ClienteProdutoServico2.qtd_premio_minimo,0) as qtd_premio_minimo_km',
					'ClienteProduto2.codigo_cliente'
				),
				'table' => $ClienteProdutoServico2->databaseTable.'.'.$ClienteProdutoServico2->tableSchema.'.'.$ClienteProdutoServico2->useTable,
				'alias' => '[ClienteProdutoServico2] WITH (NOLOCK)',
				'limit' => null,
				'offset' => null,
				'joins' => array( array(
					'table' => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
					'alias' => '[ClienteProduto2] WITH (NOLOCK)',
					'type' => 'LEFT',
					'conditions' => array('ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto2.codigo', 
						'ClienteProduto2.codigo_produto' => 82,
					),
				)),
				'conditions' => array('ClienteProdutoServico2.codigo_servico' => 27),
				'order' => null,
				'group' => null,
				), $this
		);

		$por_sm = $dbo->buildStatement(
		  array(
				'fields' => array(
					'ISNULL(valor, 0) as valor_unitario_sm',
					'ISNULL(ClienteProdutoServico2.valor_premio_minimo,0) as valor_premio_minimo_sm', 
					'ISNULL(ClienteProdutoServico2.qtd_premio_minimo,0) as qtd_premio_minimo_sm', 
					'ISNULL(ClienteProdutoServico2.valor_maximo,0) as valor_maximo_sm', 
					'ClienteProduto2.codigo_cliente'
				),
				'table' => $ClienteProdutoServico2->databaseTable.'.'.$ClienteProdutoServico2->tableSchema.'.'.$ClienteProdutoServico2->useTable,
				'alias' => '[ClienteProdutoServico2] WITH (NOLOCK)',
				'limit' => null,
				'offset' => null,
				'joins' => array( array(
					'table' => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
					'alias' => '[ClienteProduto2] WITH (NOLOCK)',
					'type' => 'LEFT',
					'conditions' => array(
						'ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto2.codigo', 
						'ClienteProduto2.codigo_produto' => 82),
				)),
				'conditions' => array('ClienteProdutoServico2.codigo_servico' => 22),
				'order' => null,
				'group' => null,
				), $this
		);

		$por_sm_telemonitorada = $dbo->buildStatement(
		  array(
				'fields' => array(
					'ISNULL(valor, 0) as valor_unitario_sm_telemonitorada',
					'ISNULL(ClienteProdutoServico2.valor_premio_minimo,0) as valor_premio_minimo_sm_telemonitorada', 
					'ISNULL(ClienteProdutoServico2.qtd_premio_minimo,0) as qtd_premio_minimo_sm_telemonitorada', 
					'ISNULL(ClienteProdutoServico2.valor_maximo,0) as valor_maximo_sm_telemonitorada', 
					'ClienteProduto2.codigo_cliente'
				),
				'table' => $ClienteProdutoServico2->databaseTable.'.'.$ClienteProdutoServico2->tableSchema.'.'.$ClienteProdutoServico2->useTable,
				'alias' => '[ClienteProdutoServico2] WITH (NOLOCK)',
				'limit' => null,
				'offset' => null,
				'joins' => array( array(
					'table' => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
					'alias' => '[ClienteProduto2] WITH (NOLOCK)',
					'type' => 'LEFT',
					'conditions' => array(
						'ClienteProdutoServico2.codigo_cliente_produto = ClienteProduto2.codigo', 
						'ClienteProduto2.codigo_produto' => 82),
				)),
				'conditions' => array('ClienteProdutoServico2.codigo_servico' => 23),
				'order' => null,
				'group' => null,
				), $this
		);

		$joins = array(
			array(
				'table' 	 => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
				'alias' 	 => '[Cliente] WITH (NOLOCK)',
				'type' 		 => 'INNER',
				'conditions' => array("ClientEmpresa.codigo_documento = Cliente.codigo_documento COLLATE SQL_Latin1_General_Cp1_CI_AI"),	
			),
			array(
				'table' 	 => "({$viagens})",
				'alias' 	 => 'Viagens',
				'type' 		 => 'INNER',
				'conditions' => array('Viagens.cliente = ClientEmpresa.Codigo'),
			),
			array(
				'table' 	 => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
				'alias' 	 => '[ClienteProduto] WITH (NOLOCK)',
				'type' 		 => 'LEFT',
				'conditions' => array(
					'ClienteProduto.codigo_cliente = Viagens.cliente_pagador',
					'ClienteProduto.codigo_produto' => 82, 
				),	
			),
			array(
				'table' 	 => "({$por_dia})",
				'alias' 	 => 'ClienteProdutoServico2Dia',
				'type' 		 => 'LEFT',
				'conditions' => array('Viagens.cliente_pagador = ClienteProdutoServico2Dia.codigo_cliente'),
			),
			array(
				'table' 	 => "({$por_km})",
				'alias' 	 => 'ClienteProdutoServico2Km',
				'type' 		 => 'LEFT',
				'conditions' => array('Viagens.cliente_pagador = ClienteProdutoServico2Km.codigo_cliente'),
			),
			array(
				'table' 	 => "({$por_sm})",
				'alias' 	 => 'ClienteProdutoServico2sm',
				'type' 		 => 'LEFT',
				'conditions' => array('Viagens.cliente_pagador = ClienteProdutoServico2sm.codigo_cliente'),
			),
			array(
				'table' 	 => "({$por_sm_telemonitorada})",
				'alias' 	 => 'ClienteProdutoServico2smtelemonitorada',
				'type' 		 => 'LEFT',
				'conditions' => array('Viagens.cliente_pagador = ClienteProdutoServico2smtelemonitorada.codigo_cliente'),
			),
		);

		$data_inicial = "'".AppModel::dateToDbDate($filtros['data_inicial'])."'";
		$data_final   = "'".AppModel::dateToDbDate($filtros['data_final'])."'";

		$conditions = array('ClientEmpresa.DtaCadastro <=' => AppModel::dateToDbDate($filtros['data_final']));

		$matriz_placa = $dbo->buildStatement(
		  array(
				'fields' => array(
							"ClientEmpresa.codigo",
							"ClientEmpresa.Raz_Social",
							"Viagens.placa",
							"Viagens.placa_frota",
							"Viagens.placa_avulso",
							"ISNULL(ISNULL(Viagens.cliente_pagador, ClientEmpresa.CodigoInformacoes),cliente.codigo) as cliente_pagador",
							"CONVERT(VARCHAR, ClientEmpresa.DtaCadastro, 120) as DtaCadastro",
							"CONVERT(VARCHAR, ClientEmpresa.DTACANCELAMENTO, 120) as DTACANCELAMENTO",
							"ISNULL(ClienteProdutoServico2sm.valor_maximo_sm,0) as valor_maximo_sm",
							"ISNULL(ClienteProdutoServico2smtelemonitorada.valor_maximo_sm_telemonitorada,0) as valor_maximo_sm_telemonitorada",
				  			"DATEDIFF(day, {$data_inicial}, {$data_final}) + 1 as dias_periodo",
							"{$Cliente->databaseTable}.{$Documento->tableSchema}.ufn_menor_valor(
								DATEDIFF(day, {$data_inicial}, {$data_final}) + 1, 
								CASE WHEN DTACANCELAMENTO IS NULL 
									THEN DATEDIFF(day, {$Cliente->databaseTable}.{$Documento->tableSchema}.ufn_maior_data(ClientEmpresa.DtaCadastro, {$data_inicial}), {$data_final}) + 1 
									ELSE {$Cliente->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor(
										0, 
										DATEDIFF(day, DATEADD(dd,-DAY($data_final)+1, $data_final) + 1,ClientEmpresa.DTACANCELAMENTO)) 
								END) as DiasNoMes",
							"ISNULL(Viagens.qtd_dia, 0) AS qtd_dia",
							"ISNULL(ClienteProdutoServico2Dia.valor_unitario_dia, 0) AS valor_unitario_dia",
							"{$Cliente->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor((ISNULL(qtd_dia,0) * ISNULL(ClienteProdutoServico2Dia.valor_unitario_dia,0)),ISNULL(valor_premio_minimo_dia, 0)) AS valor_dia",
							"ISNULL(Viagens.qtd_km,0) AS qtd_km",
							"ISNULL(ClienteProdutoServico2Km.valor_unitario_km, 0) AS valor_unitario_km",
							"{$Cliente->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor((ISNULL(ClienteProdutoServico2Km.valor_unitario_km,0) * ISNULL(qtd_km,0)),ISNULL(valor_premio_minimo_km, 0)) AS valor_km",
							"ISNULL(Viagens.qtd_sm_fora_frota,0) AS qtd_sm_monitorada",
							"ISNULL(ClienteProdutoServico2sm.valor_unitario_sm, 0) AS valor_unitario_sm_monitorada",
							"CASE WHEN ISNULL(valor_maximo_sm,0) > 0 THEN {$Cliente->databaseTable}.{$Documento->tableSchema}.ufn_menor_valor({$Cliente->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor((ISNULL(ClienteProdutoServico2sm.valor_unitario_sm,0) * ISNULL(qtd_sm_fora_frota,0)),ISNULL(valor_premio_minimo_sm, 0)), ISNULL(valor_maximo_sm,0)) ELSE {$Cliente->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor((ISNULL(ClienteProdutoServico2sm.valor_unitario_sm,0) * ISNULL(qtd_sm_fora_frota,0)),ISNULL(valor_premio_minimo_sm, 0)) END AS valor_sm_monitorada",
							"ISNULL(Viagens.qtd_sm_fora_frota_telemonitorada,0) AS qtd_sm_telemonitorada",
							"ISNULL(ClienteProdutoServico2smtelemonitorada.valor_unitario_sm_telemonitorada, 0) AS valor_unitario_sm_telemonitorada",
							"CASE WHEN ISNULL(valor_maximo_sm_telemonitorada,0) > 0 THEN {$Cliente->databaseTable}.{$Documento->tableSchema}.ufn_menor_valor({$Cliente->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor((ISNULL(ClienteProdutoServico2smtelemonitorada.valor_unitario_sm_telemonitorada,0) * ISNULL(qtd_sm_fora_frota_telemonitorada,0)),ISNULL(valor_premio_minimo_sm_telemonitorada, 0)), ISNULL(valor_maximo_sm_telemonitorada,0)) ELSE {$Cliente->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor((ISNULL(ClienteProdutoServico2smtelemonitorada.valor_unitario_sm_telemonitorada,0) * ISNULL(qtd_sm_fora_frota_telemonitorada,0)),ISNULL(valor_premio_minimo_sm_telemonitorada, 0)) END AS valor_sm_telemonitorada",
				),
				'table' => "{$this->databaseTable}.{$this->tableSchema}.{$this->useTable}",
				'alias' => '[ClientEmpresa] WITH (NOLOCK)',
				'limit' => null,
				'offset' => null,
				'joins' => $joins,
				'conditions' => $conditions,
				'order' => null,
				'group' => null,
				), $this
		);

		$group = array(
			"codigo",
			"Raz_Social",
			"cliente_pagador",
			"valor_maximo_sm",
			"valor_maximo_sm_telemonitorada",
			"DtaCadastro",
			"DTACANCELAMENTO",
  			"dias_periodo",
			"DiasNoMes",
			"valor_unitario_dia ",
			"valor_unitario_km ",
			"valor_unitario_sm_monitorada ",
			"valor_unitario_sm_telemonitorada ",
		);

		$fields = array_merge($group, array(
			"COUNT(distinct placa) AS qtd_placa",
			"COUNT(distinct placa_frota) AS qtd_placa_frota",
			"COUNT(distinct placa_avulso) AS qtd_placa_avulsa",
			"SUM(qtd_dia) AS qtd_dia",
			"SUM(valor_dia) AS valor_dia",
			"SUM(qtd_km) AS qtd_km",
			"SUM(valor_km) AS valor_km",
			"SUM(qtd_sm_monitorada) AS qtd_sm_monitorada",
			"SUM(qtd_sm_telemonitorada) AS qtd_sm_telemonitorada",
			"SUM(valor_sm_monitorada) AS valor_sm_monitorada",
			"SUM(valor_sm_telemonitorada) AS valor_sm_telemonitorada",
		));

		$matriz = $dbo->buildStatement(
		  array(
				'fields' 	 => $fields,
				'table' 	 => "({$matriz_placa})",
				'alias' 	 => 'MatrizPlaca',
				'limit' 	 => null,
				'offset' 	 => null,
				'joins' 	 => array(),
				'conditions' => null,
				'order' 	 => null,
				'group' 	 => $group,
				), $this
		);

		$conditions = null;
		if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente']))
			$conditions['cliente_pagador'] = $filtros['codigo_cliente'];

		$filhos_totalizados = $dbo->buildStatement(
		  array(
			'fields' => array(
				"cliente_pagador",
				"codigo",
				"raz_social",
				"dtacadastro",
				"dtacancelamento",
				"DiasNoMes",
				"qtd_placa",
				"qtd_placa_frota",
				"qtd_placa_avulsa",
				"qtd_dia",
				"valor_unitario_dia",
				"valor_dia",
				"qtd_km",
				"valor_unitario_km",
				"valor_km",
				"qtd_sm_monitorada",
				"valor_unitario_sm_monitorada",
				"valor_sm_monitorada",
				"valor_maximo_sm",
				"qtd_sm_telemonitorada",
				"valor_unitario_sm_telemonitorada",
				"valor_sm_telemonitorada",
				"valor_maximo_sm_telemonitorada",
				"CONVERT(DECIMAL(15,2), valor_sm_monitorada + valor_sm_telemonitorada + valor_dia + valor_km) AS valor_a_pagar"
			),
			'table' => "({$matriz})",
			'alias' => 'Matriz',
			'limit' => null,
			'offset' => null,
			'joins' => array(),
			'conditions' => $conditions,
			'order' => null,
			'group' => null,
			), $this
		);

		if ($detalhar_filhos) {
			if ($retornar_instrucao_sql) {
				return $filhos_totalizados;
			} else {
		  		return $this->query($filhos_totalizados); 
			}
		}

		$conditions = array('(ISNULL(valor_a_pagar,0) + ISNULL([ClientesPremioMinimo].[valor_premio_minimo],0) + ISNULL(ClientesPrecoFechado.ValDeterminado,0) + (ISNULL(ClientesFrota.valor_unitario_frota,0) * ISNULL(ClientesFrota.qtd_frota,0)) + (ISNULL(ClientesAvulsos.valor_unitario_placa_avulsa,ISNULL(ClientesFrota.valor_unitario_frota,0)) * ISNULL(ClientesAvulsos.qtd_placa_avulsa,0))) >' => 0);

		$group = array(
			'ISNULL(ISNULL(ISNULL(FilhosTotalizados.cliente_pagador, ClientesPremioMinimo.cliente_pagador), ClientesPrecoFechado.cliente_pagador), ClientesFrota.cliente_pagador)',
			'ISNULL(ClientesPremioMinimo.valor_premio_minimo,0)',
			'ISNULL(ClientesPrecoFechado.ValDeterminado,0)',
			'ISNULL(ClientesFrota.qtd_frota,0)',
			'ISNULL(ClientesFrota.valor_unitario_frota,0) * ISNULL(ClientesFrota.qtd_frota,0)',
			"ISNULL(ClientesAvulsos.qtd_placa_avulsa,0)",
			"ISNULL(ClientesAvulsos.valor_unitario_placa_avulsa,ISNULL(ClientesFrota.valor_unitario_frota,0)) * ISNULL(ClientesAvulsos.qtd_placa_avulsa,0)",
		);

		$fields = array(
			"ISNULL(ISNULL(ISNULL(FilhosTotalizados.cliente_pagador, ClientesPremioMinimo.cliente_pagador), ClientesPrecoFechado.cliente_pagador), ClientesFrota.cliente_pagador) AS cliente_pagador",
			'ISNULL(ClientesPremioMinimo.valor_premio_minimo,0) AS valor_premio_minimo',
			'ISNULL(ClientesPrecoFechado.ValDeterminado,0) AS ValDeterminado',
			'ISNULL(ClientesFrota.qtd_frota,0) AS qtd_frota',
			'ISNULL(ClientesFrota.valor_unitario_frota,0) * ISNULL(ClientesFrota.qtd_frota,0) AS valor_frota',
			"ISNULL(ClientesAvulsos.qtd_placa_avulsa,0) AS qtd_placa_avulsa",
			"ISNULL(ClientesAvulsos.valor_unitario_placa_avulsa,ISNULL(ClientesFrota.valor_unitario_frota,0)) * ISNULL(ClientesAvulsos.qtd_placa_avulsa,0) AS valor_placa_avulsa",
			"SUM(qtd_dia) AS qtd_dia",
			"SUM(valor_dia) AS valor_dia",
			"SUM(qtd_km) AS qtd_km",
			"SUM(valor_km) AS valor_km",
			"SUM(qtd_sm_monitorada) AS qtd_sm_monitorada",
			"SUM(valor_sm_monitorada) AS valor_sm_monitorada",
			"SUM(qtd_sm_telemonitorada) AS qtd_sm_telemonitorada",
			"SUM(valor_sm_telemonitorada) AS valor_sm_telemonitorada",
			"SUM(ISNULL(valor_a_pagar,0)) + (ISNULL(ClientesFrota.valor_unitario_frota,0) * ISNULL(ClientesFrota.qtd_frota,0)) + (ISNULL(ClientesAvulsos.valor_unitario_placa_avulsa,ISNULL(ClientesFrota.valor_unitario_frota,0)) * ISNULL(ClientesAvulsos.qtd_placa_avulsa,0)) AS valor_a_pagar"
		);

		$queryPremioMinimo = $ClienteProduto->queryPremioMinimoPorCliente($filtros);
		$queryPrecoFechado = $ClienteProduto->queryPrecoFechadoPorCliente($filtros);
		$queryFrota = $ClienteProdutoServico2->frotaPorPagador($filtros, true);
		$queryAvulsos = $Recebsm->placasAvulsasPorCliente($filtros, true);
		$joins = array(
			array(
				'table' => "($queryPremioMinimo)",
				'alias' => 'ClientesPremioMinimo',
				'type' => 'FULL',
				'conditions' => array('ClientesPremioMinimo.cliente_pagador = FilhosTotalizados.cliente_pagador'),
			),
			array(
				'table' => "($queryPrecoFechado)",
				'alias' => 'ClientesPrecoFechado',
				'type' => 'FULL',
				'conditions' => array('ClientesPrecoFechado.cliente_pagador = FilhosTotalizados.cliente_pagador'),
			),
			array(
				'table' => "($queryFrota)",
				'alias' => 'ClientesFrota',
				'type' => 'FULL',
				'conditions' => array('ClientesFrota.cliente_pagador = FilhosTotalizados.cliente_pagador'),
			),
			array(
				'table' => "($queryAvulsos)",
				'alias' => 'ClientesAvulsos',
				'type' => 'FULL',
				'conditions' => array('ClientesAvulsos.cliente_pagador = FilhosTotalizados.cliente_pagador'),
			),
		);

		$pagadores_sub_totalizados = $dbo->buildStatement(
		  array(
				'fields' => $fields,
				'table' => "({$filhos_totalizados})",
				'alias' => 'FilhosTotalizados',
				'limit' => null,
				'offset' => null,
				'joins' => $joins,
				'conditions' => $conditions,
				'order' => null,
				'group' => $group,
				), $this
		);

		$fields = array(
			"cliente_pagador",
			"Cliente.razao_social",
			"ClienteEndereco.codigo AS codigo_endereco",
			"ValDeterminado",
			"valor_premio_minimo",
			"qtd_frota",
			"valor_frota",
			"qtd_placa_avulsa",
			"valor_placa_avulsa",
			"qtd_dia",
			"valor_dia",
			"qtd_km",
			"valor_km",
			"qtd_sm_monitorada",
			"valor_sm_monitorada",
			"qtd_sm_telemonitorada",
			"valor_sm_telemonitorada",
			"ClienteProdutoDesconto.valor AS valor_desconto",
			"CASE WHEN ValDeterminado > 0 THEN CONVERT(DECIMAL(15,2), ValDeterminado) ELSE CONVERT(DECIMAL(15,2), {$Cliente->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor(valor_a_pagar - ISNULL(ClienteProdutoDesconto.valor,0),valor_premio_minimo - ISNULL(ClienteProdutoDesconto.valor,0))) END AS valor_a_pagar",
		);

		$conditions = null;
		if ($verificar_falha) {
			$conditions = array('OR' => array(
				'ClienteEndereco.codigo IS NULL', 
				"CASE WHEN ValDeterminado > 0 THEN CONVERT(DECIMAL(15,2), ValDeterminado) ELSE CONVERT(DECIMAL(15,2), {$Cliente->databaseTable}.{$Documento->tableSchema}.ufn_maior_valor(valor_a_pagar - ISNULL(ClienteProdutoDesconto.valor,0),valor_premio_minimo - ISNULL(ClienteProdutoDesconto.valor,0))) END < 0"
			));
		}
		$cte_pagadores_sub_totalizados = "WITH CteFilhosTotalizados AS ({$pagadores_sub_totalizados})";

		$pagadores_totalizados = $dbo->buildStatement(
		  	array(
				'fields' => $fields,
				'table' => "CteFilhosTotalizados",
				'alias' => 'PagadoresSubTotalizados',
				'limit' => null,
				'offset' => null,
				'joins' => array(
					array(
						'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
						'alias' => '[Cliente] WITH (NOLOCK)',
						'type' => 'INNER',
						'conditions' => array('PagadoresSubTotalizados.cliente_pagador = Cliente.codigo'),
					),
					array(
						'table' => "{$ClienteEndereco->databaseTable}.{$ClienteEndereco->tableSchema}.{$ClienteEndereco->useTable}",
						'alias' => '[ClienteEndereco] WITH (NOLOCK)',
						'type' => 'LEFT',
						'conditions' => array(
							'ClienteEndereco.codigo_cliente = Cliente.codigo',
							'ClienteEndereco.codigo_tipo_contato' => TipoContato::TIPO_CONTATO_COMERCIAL,
						),
					),
					array(
						'table' => "{$ClienteProdutoDesconto->databaseTable}.{$ClienteProdutoDesconto->tableSchema}.{$ClienteProdutoDesconto->useTable}",
						'alias' => '[ClienteProdutoDesconto] WITH (NOLOCK)',
						'type' => 'LEFT',
						'conditions' => array(
							'ClienteProdutoDesconto.codigo_cliente = Cliente.codigo',
							'ClienteProdutoDesconto.mes_ano BETWEEN ? AND ?' => array(AppModel::dateToDbDate($filtros['data_inicial']), AppModel::dateToDbDate($filtros['data_final'])),
							'ClienteProdutoDesconto.codigo_produto' => 82,
						),
					),
				),
				'conditions' => $conditions,
				'order' => null,
				'group' => null,
				), $this
		);

		if ($retornar_instrucao_sql) {
			return array('cte' => $cte_pagadores_sub_totalizados, 'query' => $pagadores_totalizados);
		} else {
			return $this->query($cte_pagadores_sub_totalizados.$pagadores_totalizados);
		}
	}

	//function paginaClientes($pagina = 1, $tamanho = 100, $conditions = array(), $joins = array(), $fields = array(),$group) {
	function paginaClientesMonCliente($pagina = 1, $tamanho = 100) {
		$begin 	= ($pagina*$tamanho)-$tamanho;
		$end 	= $begin+$tamanho-1;

		$sql = ' SELECT * FROM (';
		$sql .=' SELECT ROW_NUMBER() OVER(ORDER BY codigo) AS RowNumber,';
		$sql .=' p.Codigo,p.CNPJCPF,p.Raz_Social ';
		$sql .=' FROM Monitora.dbo.Client_Empresas p WITH(NOLOCK)';
		$sql .=' INNER JOIN Monitora.dbo.MON_ClienteTerceiro m';
		$sql .=' ON m.FAV_Codigo COLLATE DATABASE_DEFAULT = p.Codigo COLLATE DATABASE_DEFAULT';
		$sql .=' WHERE 1 = 1 AND m.codigo_trafegus_refe_referencia IS NULL AND m.CID_Codigo IS NOT NULL';
		$sql .=' GROUP BY p.Codigo,p.CNPJCPF,p.Raz_Social ';
		$sql .=' ) p ';
		$sql .=' WHERE P.RowNumber BETWEEN '.$begin.' AND '.$end;
		$resultado = $this->query($sql);

		return $resultado;
	}

	public function listaEmbarcadoresTransportadores($codigo_empresa,$tipo){
		$MClientRelacionado =& ClassRegistry::Init('MClientRelacionado');

		if($tipo == 4){
			$joins 	= array(
						array(
							'table'	=> $MClientRelacionado->databaseTable.'.'.$MClientRelacionado->tableSchema.'.'.$MClientRelacionado->useTable,
							'alias'	=> 'MClientRelacionado',
							'conditions' => "ClientEmpresa.codigo = MClientRelacionado.CodCliente",
						),
					);
			$conditions = array('MClientRelacionado.CodRelacionado' => $codigo_empresa);

		} else {
			$joins 	= array(
						array(
							'table'	=> $MClientRelacionado->databaseTable.'.'.$MClientRelacionado->tableSchema.'.'.$MClientRelacionado->useTable,
							'alias'	=> 'MClientRelacionado',
							'conditions' => "ClientEmpresa.codigo = MClientRelacionado.CodRelacionado",
						),
					);
			$conditions = array('MClientRelacionado.CodCliente' => $codigo_empresa);
		}
		
		$order		= array('ClientEmpresa.Raz_Social');
		$retorno 	= $this->find('list',compact('joins','conditions','order'));

		return ($retorno)?$retorno:array();
	}

	function descricaoTipoEmpresa($codigo) {
		if ($codigo == self::TIPO_EMPRESA_EMBARCADOR)
			return 'Embarcador';
		if ($codigo == self::TIPO_EMPRESA_TRANSPORTADORA)
			return 'Transportadora';
		if ($codigo == self::TIPO_EMPRESA_SEGURADORA)
			return 'Seguradora';
		if ($codigo == self::TIPO_EMPRESA_CORRETORA)
			return 'Corretora';
	}

	function cnpjsCpfs($codigos) {
		$result = $this->find('all', array('fields' => array("REPLACE(REPLACE(REPLACE(CNPJCPF,'.',''),'-',''),'/','') AS cnpjcpf"), 'conditions' => array('codigo' => $codigos)));
		return Set::extract('/0/cnpjcpf', $result);
	}
	
	function criarSenha($codigo) {
		$registro = $this->find('first', array('fields' => array('codigo','WebSenha'), 'conditions' => array('codigo' => $codigo)));
		if ((!isset($registro['ClientEmpresa']['WebSenha']) || trim($registro['ClientEmpresa']['WebSenha']) === '')) {
			$registro['ClientEmpresa']['WebSenha'] = str_pad((string)mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
			return parent::atualizar($registro);
		} else {
			return true;
		}
	}

	function novoCodigo(){
		$fields      = array('MAX(CAST(Codigo AS INT))+1 AS novo_codigo');
		$novo_codigo = $this->find('first',compact('fields'));
		$novo_codigo = str_pad($novo_codigo[0]['novo_codigo'], 6, "0", STR_PAD_LEFT);   

		return $novo_codigo;
	}
}

?>