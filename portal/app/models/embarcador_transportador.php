<?php
class EmbarcadorTransportador extends AppModel { 
	var $name = 'EmbarcadorTransportador';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'embarcadores_transportadores';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_embarcadores_transportadores'));
	var $validate = array(
		'codigo_cliente_embarcador' => array(
			'notEmpty' 		=> array(
				'rule' 		=> 'notEmpty',
				'message' 	=> 'Informe o código do embarcador',
			 ),
			'validaCombinacao' => array(
				'rule' 		=> 'validaCombinacao',
				'message' 	=> 'Esta combinação Embarcador/Transportador já foi criada',
				'on'		=> 'create',
			),
		),
		'codigo_cliente_transportador' => array(
			'rule' 		=> 'notEmpty',
			'message' 	=> 'Informe o código do transportador',
		),
	);

	var $belongsTo = array(
		'ClienteEmbarcador' 	=> array('className' => 'Cliente', 'foreignKey' => 'codigo_cliente_embarcador' ),
		'ClienteTransportador' 	=> array('className' => 'Cliente', 'foreignKey' => 'codigo_cliente_transportador' )
	
	);

	function validaCombinacao(){
		$conditions = array('codigo_cliente_embarcador' 	=> $this->data[$this->name]['codigo_cliente_embarcador'],
							'codigo_cliente_transportador' 	=> $this->data[$this->name]['codigo_cliente_transportador']);

		return ($this->find('count',compact('conditions')) > 0)?false:true;
	}

	function converteFiltrosEmConditions($filtros) {
		
		$conditions = array();

		if (isset($filtros['codigo_cliente_embarcador']) && !empty($filtros['codigo_cliente_embarcador']))
			$conditions[$this->name.'.codigo_cliente_embarcador'] 	= $filtros['codigo_cliente_embarcador'];		

		if (isset($filtros['codigo_cliente_transportador']) && !empty($filtros['codigo_cliente_transportador']))
			$conditions[$this->name.'.codigo_cliente_transportador'] = $filtros['codigo_cliente_transportador'];

		if (isset($filtros['codigo_cliente_pagador']) && !empty($filtros['codigo_cliente_pagador']))
			$conditions['ClienteProdutoPagador.codigo_cliente_pagador'] = $filtros['codigo_cliente_pagador'];

		if (isset($filtros['codigo_produto']) && !empty($filtros['codigo_produto']))
			$conditions['ClienteProdutoPagador.codigo_produto'] = $filtros['codigo_produto'];
		
		return $conditions;
	}

	function consultaPagadorProdutoPreco($conditions){
	    $MatrizFilial 		= ClassRegistry::init('MatrizFilial'); 
	    $consulta 			= null;

	    if(empty($conditions['EmbarcadorTransportador.codigo_cliente_transportador']) || empty($conditions['ClienteProdutoPagador.codigo_produto'])) {
			return false;
	    }

	    if(isset($conditions['EmbarcadorTransportador.codigo_cliente_embarcador'])) {
			$consulta =  $this->listarEmbarcadorTransportadorProdutoPagador($conditions);	
		}

	 	if(isset($conditions['EmbarcadorTransportador.codigo_cliente_embarcador']) && !empty($conditions['EmbarcadorTransportador.codigo_cliente_embarcador']) 
	 		&& isset($conditions['EmbarcadorTransportador.codigo_cliente_transportador']) && isset($conditions['ClienteProdutoPagador.codigo_produto'])) {
			$consulta =  $this->listarEmbarcadorTransportadorProdutoPagador($conditions);
		}

		if($consulta == false) {
			$filtros = array() ;
			$filtros ['MatrizFilial.codigo_cliente_filial'] = $conditions['EmbarcadorTransportador.codigo_cliente_transportador'];
			$filtros ['Produto.codigo'] 					= $conditions['ClienteProdutoPagador.codigo_produto'];

			$consulta = $MatrizFilial->listarMatrizFilialProdutoPagador($filtros);	
		}

	 	return $consulta;

	}

	public function pagadorPorProduto($codigo_cliente_transportador, $codigo_produto, $codigo_cliente_embarcador = null, $somente_ativo = true) {
		$this->bindModel(array('hasOne' => array(
			'ClienteProdutoPagador' => array('foreignKey' => 'codigo_embarcador_transportador'),
			'ClienteProduto' => array(
				'foreignKey' => false,
				'conditions' => array(
					'ClienteProduto.codigo_cliente = ClienteProdutoPagador.codigo_cliente_pagador',
					'ClienteProduto.codigo_produto = ClienteProdutoPagador.codigo_produto',
				),
			),
		)));
		$fields = array(
			'ClienteProdutoPagador.codigo_cliente_pagador AS codigo_cliente_pagador',
		);
		$conditions = array(
			'codigo_cliente_transportador' => $codigo_cliente_transportador, 
			'codigo_cliente_embarcador' => $codigo_cliente_embarcador, 
			'ClienteProdutoPagador.codigo_produto' => $codigo_produto
		);
		if ($somente_ativo) {
			$conditions['ClienteProduto.codigo_motivo_bloqueio'] = MotivoBloqueio::MOTIVO_OK;
		}
		return $this->find('first', compact('fields', 'conditions'));
	}

	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {
		
		if( isset($extra['extra']['method']) && $extra['extra']['method'] = 'listarEmbarcadorTransportadorProdutoPagador' )
			return $this->listarEmbarcadorTransportadorProdutoPagador($conditions, $limit, $page, $order);

		$joins = null;

		if (isset($extra['joins']))
			$joins = $extra['joins'];

		return $this->find('all', compact('conditions', 'fields', 'order', 'limit', 'page', 'recursive', 'group', 'joins'));
	}

	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		
		if( isset($extra['extra']['method']) && $extra['extra']['method'] == 'listarEmbarcadorTransportadorProdutoPagador' )
			return $this->listarEmbarcadorTransportadorProdutoPagador($conditions, null, null, null, 'count');
		
		$joins = null;

		if (isset($extra['joins']))
			$joins = $extra['joins'];

		return $this->find('count', compact('conditions', 'recursive', 'joins'));
	}

	public function listarAssinaturas($codigo_cliente) {
        $ClienteProdutoPagador = ClassRegistry::init('ClienteProdutoPagador');
        $ClienteProduto        = ClassRegistry::init('ClienteProduto');
        $Produto 			   = ClassRegistry::init('Produto');
        $MotivoBloqueio 	   = ClassRegistry::init('MotivoBloqueio');
        $Cliente               = ClassRegistry::init('Cliente');

        $joins = array(
            array(
                'table' => "{$ClienteProdutoPagador->databaseTable}.{$ClienteProdutoPagador->tableSchema}.{$ClienteProdutoPagador->useTable}",
                'alias' => 'ClienteProdutoPagador',
                'conditions' => 'ClienteProdutoPagador.codigo_embarcador_transportador = EmbarcadorTransportador.codigo'
            ),
            array(
                'table' => "{$ClienteProduto->databaseTable}.{$ClienteProduto->tableSchema}.{$ClienteProduto->useTable}",
                'alias' => 'ClienteProduto',
                'conditions' => array(
                	'ClienteProduto.codigo_cliente = ClienteProdutoPagador.codigo_cliente_pagador',
                 	'ClienteProduto.codigo_produto = ClienteProdutoPagador.codigo_produto'
             	)
            ),
            array(
                'table' => "{$Produto->databaseTable}.{$Produto->tableSchema}.{$Produto->useTable}",
                'alias' => 'Produto',
                'conditions' => 'Produto.codigo = ClienteProdutoPagador.codigo_produto'
            ),
            array(
                'table' => "{$MotivoBloqueio->databaseTable}.{$MotivoBloqueio->tableSchema}.{$MotivoBloqueio->useTable}",
                'alias' => 'MotivoBloqueio',
                'conditions' => 'ClienteProduto.codigo_motivo_bloqueio = MotivoBloqueio.codigo'
            ),
            array(
            	'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
            	'type' => "LEFT",
                'alias' => 'ClienteEmbarcador',
                'conditions' => 'ClienteEmbarcador.codigo = EmbarcadorTransportador.codigo_cliente_embarcador'
        	),
        	array(
            	'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
            	'type' => "LEFT",
                'alias' => 'ClienteTransportador',
                'conditions' => 'ClienteTransportador.codigo = EmbarcadorTransportador.codigo_cliente_transportador'
        	),
        	array(
            	'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
            	'type' => "LEFT",
                'alias' => 'ClientePagador',
                'conditions' => 'ClientePagador.codigo = ClienteProdutoPagador.codigo_cliente_pagador'
        	),
        );

        $conditions = array(
            'codigo_cliente_transportador' => $codigo_cliente,
            'ClienteProdutoPagador.codigo_cliente_pagador <> EmbarcadorTransportador.codigo_cliente_transportador'
        );

        $fields = array(
            'EmbarcadorTransportador.codigo_cliente_embarcador',
            'EmbarcadorTransportador.codigo_cliente_transportador',
            'Produto.descricao',
            'ClienteProdutoPagador.codigo_cliente_pagador',
            'MotivoBloqueio.descricao',
            'ClienteEmbarcador.razao_social as nome_embarcador',
            'ClienteTransportador.razao_social as nome_transportador',
            'ClientePagador.razao_social as nome_pagador'
        );
        $recursive = -1;        
        return $this->find('all', compact('joins', 'conditions', 'fields', 'recursive'));
    }

	public function listarEmbarcadorTransportadorProdutoPagador($conditions, $limit = null, $page = 1, $order = null, $tipo_find = 'all') {

		$Cliente 			   = ClassRegistry::init('Cliente');        
		$ClienteProdutoPagador = ClassRegistry::init('ClienteProdutoPagador');        
		$Produto               = ClassRegistry::init('Produto');  
		$fields 			   = null;

        $joins = array(            
            
            array(
                'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
                'alias' => 'ClienteEmbarcador',
                'conditions' => 'EmbarcadorTransportador.codigo_cliente_embarcador = ClienteEmbarcador.codigo'
            ),
            array(
                'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
                'alias' => 'ClienteTransportador',
                'conditions' => 'EmbarcadorTransportador.codigo_cliente_transportador = ClienteTransportador.codigo'
            ),
            array(
                'table' => "{$ClienteProdutoPagador->databaseTable}.{$ClienteProdutoPagador->tableSchema}.{$ClienteProdutoPagador->useTable}",
                'alias' => 'ClienteProdutoPagador',
                'conditions' => 'EmbarcadorTransportador.codigo = ClienteProdutoPagador.codigo_embarcador_transportador',
                'type' => 'LEFT',
            ),
            array(
                'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
                'alias' => 'ClientePagador',
                'conditions' => 'ClienteProdutoPagador.codigo_cliente_pagador = ClientePagador.codigo',
                'type' => 'LEFT',
            ),
            array(
                'table' => "{$Produto->databaseTable}.{$Produto->tableSchema}.{$Produto->useTable}",
                'alias' => 'Produto',
                'conditions' => 'ClienteProdutoPagador.codigo_produto = Produto.codigo',
                'type' => 'LEFT',
            ),
        );
		  
		if( $tipo_find == 'all' )
			$fields = array(
				'EmbarcadorTransportador.codigo',

				'ClienteEmbarcador.codigo',
				'ClienteEmbarcador.razao_social',

				'ClienteProdutoPagador.codigo',
				
				'ClienteTransportador.codigo',
				'ClienteTransportador.razao_social',

				'Produto.codigo',
				'Produto.descricao',
				
				'ClientePagador.codigo',
				'ClientePagador.razao_social',
			);
		
		$recursive = -1;

		return $this->find($tipo_find, compact('conditions', 'joins', 'fields', 'recursive', 'limit', 'page', 'order', 'group'));	
	}

	function verificaExiste($conditions) {
		return ($this->find('count', compact('conditions')) > 0);
	}


	function incluir(&$dados) {
		$Cliente 				= ClassRegistry::init('Cliente');
		$ClientEmpresa 			= ClassRegistry::init('ClientEmpresa');
		$MClientRelacionado 	= ClassRegistry::init('MClientRelacionado');
		$ClienteProdutoPagador  = ClassRegistry::init('ClienteProdutoPagador');
		try {
			if($this->useDbConfig != 'test_suite'){
				$this->query('BEGIN TRANSACTION');
			}
			$embarcador_transportador = $this->find('first', array('conditions' => array('codigo_cliente_embarcador' => $dados['EmbarcadorTransportador']['codigo_cliente_embarcador'], 'codigo_cliente_transportador' => $dados['EmbarcadorTransportador']['codigo_cliente_transportador'])));
			if(!$embarcador_transportador || ($dados['ClienteProdutoPagador']['codigo_cliente_pagador'] == null && $dados['ClienteProdutoPagador']['codigo_produto'] == null)) {
				if(empty($dados[$this->name]['codigo_cliente_transportador']) && !empty($dados[$this->name]['codigo_cliente_embarcador'])){
					$cliente = $Cliente->carregar($this->getProprio(),0);
					$dados[$this->name]['codigo_cliente_transportador']	= $cliente['Cliente']['codigo'];
					$dados[$this->name]['nome_transportador']			= $cliente['Cliente']['razao_social'];
				}
				$embarcador 	= $this->ClienteEmbarcador->find('first', array('conditions' => array('codigo' => $dados[$this->name]['codigo_cliente_embarcador']), 'recursive' => -1));
				$transportador 	= $this->ClienteTransportador->find('first', array('conditions' => array('codigo' => $dados[$this->name]['codigo_cliente_transportador']), 'recursive' => -1));
				//$pagador 		= $this->ClienteTransportador->find('first', array('conditions' => array('codigo' => $dados[$this->name]['codigo_cliente_pagador']), 'recursive' => -1));
				$embarcadores_monitora 		= $ClientEmpresa->carregarPorCnpjCpf($embarcador['ClienteEmbarcador']['codigo_documento']);
				$transportadores_monitora 	= $ClientEmpresa->carregarPorCnpjCpf($transportador['ClienteTransportador']['codigo_documento']);
				if (empty($embarcadores_monitora)) {
					$this->invalidate('codigo_cliente_embarcador','Embarcador não encontrado no Monitora!');
				}
				if (empty($transportadores_monitora)) {
					$this->invalidate('codigo_cliente_transportador','Transportador não encontrado no Monitora!');
				}
				if($this->invalidFields())
					throw new Exception();

				foreach ($embarcadores_monitora as $codigo_embarcador_monitora => $embarcador_monitora) {
					foreach ($transportadores_monitora as $codigo_transportador_monitora => $transportador_monitora) {
						if (!$MClientRelacionado->jaTem($codigo_embarcador_monitora, $codigo_transportador_monitora)) {
							$dados_monitora = array('MClientRelacionado' => array('CodCliente' => $codigo_embarcador_monitora, 'CodRelacionado' => $codigo_transportador_monitora, 'TipoEmpresa' => 0, 'SITE_EXCLUSIVO' => 0));
							if (!$MClientRelacionado->incluir($dados_monitora)) throw new Exception('MClientRelacionado não incluído');
						}
						if (!$MClientRelacionado->jaTem($codigo_transportador_monitora, $codigo_embarcador_monitora)) {
							$dados_monitora = array('MClientRelacionado' => array('CodCliente' => $codigo_transportador_monitora, 'CodRelacionado' => $codigo_embarcador_monitora, 'TipoEmpresa' => 0, 'SITE_EXCLUSIVO' => 0));
							if (!$MClientRelacionado->incluir($dados_monitora)) throw new Exception('MClientRelacionado não incluído');
						}
					}
				}
				if (!parent::incluir($dados)) throw new Exception('EmbarcadorTransportador não incluído');
				$codigo_embarcador_transportador = $this->getLastInsertId();
			} else {
				$codigo_embarcador_transportador = $embarcador_transportador['EmbarcadorTransportador']['codigo'];
			}
			if(isset($dados['ClienteProdutoPagador'])) {
				$dados_cliente_produto_pagador['ClienteProdutoPagador']['codigo_cliente_pagador'] 			= $dados['ClienteProdutoPagador']['codigo_cliente_pagador'];
				$dados_cliente_produto_pagador['ClienteProdutoPagador']['codigo_produto'] 					= $dados['ClienteProdutoPagador']['codigo_produto'];
				$dados_cliente_produto_pagador['ClienteProdutoPagador']['codigo_embarcador_transportador'] 	= $codigo_embarcador_transportador;
				if($dados_cliente_produto_pagador['ClienteProdutoPagador']['codigo_cliente_pagador'] != null || $dados_cliente_produto_pagador['ClienteProdutoPagador']['codigo_produto'] != null)
					if (!$ClienteProdutoPagador->incluir($dados_cliente_produto_pagador)) throw new Exception('Produto e pagador não incluídos');
			}
			if($this->useDbConfig != 'test_suite'){
				$this->commit();
			}
			return true;
		} catch (Exception $ex) {
			if($this->useDbConfig != 'test_suite'){
				$this->rollback();
			}
			return false;
		}
	}

	function excluir($codigo) {
		$ClientEmpresa 			= ClassRegistry::init('ClientEmpresa');
		$MClientRelacionado 	= ClassRegistry::init('MClientRelacionado');
		$ClienteProdutoPagador  = ClassRegistry::init('ClienteProdutoPagador');

		try {
			$this->query('BEGIN TRANSACTION');
			$dados = $this->carregar($codigo);

			$embarcador 				= $this->ClienteEmbarcador->find('first', array('conditions' => array('codigo' => $dados[$this->name]['codigo_cliente_embarcador']), 'recursive' => -1));
			$transportador 				= $this->ClienteTransportador->find('first', array('conditions' => array('codigo' => $dados[$this->name]['codigo_cliente_transportador']), 'recursive' => -1));
			
			$cliente_produto_pagador 	= $ClienteProdutoPagador->find('all', array('conditions' => array('codigo_embarcador_transportador' => $codigo)));
			$sql = "
				DELETE FROM {$MClientRelacionado->databaseTable}.{$MClientRelacionado->tableSchema}.{$MClientRelacionado->table}
				WHERE codigo IN (
						SELECT cliente_relacionado.codigo FROM {$MClientRelacionado->databaseTable}.{$MClientRelacionado->tableSchema}.{$MClientRelacionado->table} AS cliente_relacionado
							JOIN {$ClientEmpresa->databaseTable}.{$ClientEmpresa->tableSchema}.{$ClientEmpresa->table} AS embarcador 
							ON embarcador.Codigo = CodCliente
							JOIN {$ClientEmpresa->databaseTable}.{$ClientEmpresa->tableSchema}.{$ClientEmpresa->table} AS transportador
							ON transportador.Codigo = CodRelacionado
						WHERE
							(
								embarcador.codigo_documento = '{$embarcador['ClienteEmbarcador']['codigo_documento']}'
								AND transportador.codigo_documento = '{$transportador['ClienteTransportador']['codigo_documento']}'
							) OR
							(
								transportador.codigo_documento = '{$embarcador['ClienteEmbarcador']['codigo_documento']}'
								AND embarcador.codigo_documento = '{$transportador['ClienteTransportador']['codigo_documento']}'
							)
					)	
			";
			
			$MClientRelacionado->query($sql);

			foreach($cliente_produto_pagador as $dado) {
				if (!$ClienteProdutoPagador->excluir($dado['ClienteProdutoPagador']['codigo']))
					throw new Exception('Produto e pagador não excluídos');
			}


			if (!parent::excluir($codigo)) throw new Exception('EmbarcadorTransportador não excluído');
			
			$this->commit();
			return true;
		} catch (Exception $ex) {

			$this->rollback();
			return false;
		}
	}

	function dadosPorCliente($codigo_cliente) {
		$Cliente =& ClassRegistry::init('Cliente');
        App::import('Model', 'ClienteSubTipo');
        $cliente = null;
        if ($codigo_cliente != null) {
            $cliente = $Cliente->carregar($codigo_cliente);
        }
        $result = array(
            'transportadores' => array(),
            'embarcadores' => array(),
        );
        if ($cliente) {
            $tipo = (ClienteSubTipo::subTipo($cliente['Cliente']['codigo_cliente_sub_tipo']) == ClienteSubTipo::SUBTIPO_TRANSPORTADOR) ? 'T' : 'E';
            if ($tipo == 'T') {
                $result['transportadores'] = array($cliente['Cliente']['codigo'] => $cliente['Cliente']['razao_social']);
            } else {
                $result['embarcadores'] = array($cliente['Cliente']['codigo'] => $cliente['Cliente']['razao_social']);
            }
            $fields = array(
                'Embarcador.codigo',
                'Embarcador.razao_social',
                'Transportador.codigo',
                'Transportador.razao_social',
            );
            if ($tipo == 'T') {
                $conditions = array('codigo_cliente_transportador' => $cliente['Cliente']['codigo']);
                $order = array('Embarcador.razao_social');
            } else {
                $conditions = array('codigo_cliente_embarcador' => $cliente['Cliente']['codigo']);
                $order = array('Transportador.razao_social');
            }
            $this->bindModel(array('belongsTo' => array(
                'Embarcador' => array('className' => 'Cliente', 'foreignKey' => 'codigo_cliente_embarcador', 'conditions' => array('Embarcador.ativo' => 'S'), 'fields' => array('Embarcador.codigo', 'Embarcador.razao_social'), 'type' => 'INNER'),
                'Transportador' => array('className' => 'Cliente', 'foreignKey' => 'codigo_cliente_transportador', 'conditions' => array('Transportador.ativo' => 'S'), 'fields' => array('Transportador.codigo', 'Transportador.razao_social'), 'type' => 'INNER'),
            )));
            $clientes = $this->find('all', compact('conditions', 'order', 'fields'));
            if ($clientes) {
                if ($tipo == 'T') {
                    $tipo = 'Embarcador';
                    $indice = 'embarcadores';
                } else {
                    $tipo = 'Transportador';
                    $indice = 'transportadores';
                }
                foreach ($clientes as $cliente) {
                    $result[$indice][$cliente[$tipo]['codigo']] = $cliente[$tipo]['razao_social'];
                }
            }
        }
        return $result;
	}

	function carregarEmbarcadorTransportadorPorBaseDocumento($codigo_cleinte,$codigo_documento){
		$conditions = array(
			array(
				'OR' => array(
					'ClienteEmbarcador.codigo' => $codigo_cleinte,
					'ClienteTransportador.codigo' => $codigo_cleinte,
				),
			),
			array(
				'OR' => array(
					'SUBSTRING(ClienteEmbarcador.codigo_documento,1,8)' => substr($codigo_documento,0,8),
					'SUBSTRING(ClienteTransportador.codigo_documento,1,8)' => substr($codigo_documento,0,8),
				),
			),
		);
		return $this->find('first',compact('conditions'));
	}

	function carregarClientePagador($codigo_cliente_transportador, $codigo_cliente_embarcador, $codigo_produto) {
		$this->bindModel(array('hasOne' => array(
			'ClienteProdutoPagador' => array('foreignKey' => 'codigo_embarcador_transportador'),
			'Cliente' => array('foreignKey' => false, 'conditions' => array('ClienteProdutoPagador.codigo_cliente_pagador = Cliente.codigo')),
		)));
		
		$fields = array('Cliente.*');
		$conditions = array(
			'OR' => array(
				array(
					'codigo_cliente_transportador' => $codigo_cliente_transportador, 
					'codigo_cliente_embarcador' => $codigo_cliente_embarcador, 
				),
				array(
					'codigo_cliente_transportador' => $codigo_cliente_embarcador, 
					'codigo_cliente_embarcador' => $codigo_cliente_transportador, 
				),
			),
			'ClienteProdutoPagador.codigo_produto' => $codigo_produto,
		);
		return $this->find('first', compact('fields', 'conditions'));
	}

	public function vincularEmbarcadorTransportador($codigo_cliente_transportador, $codigo_cliente_embarcador, $codigo_produto = null, $in_another_transaction = false) {
		try {
			if (!$in_another_transaction) $this->query('BEGIN TRANSACTION');
			$embarcador_transportador = $this->find('first', array('fields' => 'codigo', 'conditions' => array('codigo_cliente_transportador' => $codigo_cliente_transportador, 'codigo_cliente_embarcador' => $codigo_cliente_embarcador)));
			if ($embarcador_transportador) {
				$codigo_embarcador_transportador = $embarcador_transportador['EmbarcadorTransportador']['codigo'];
			} else {
				$data = array('EmbarcadorTransportador' => array(
					'codigo_cliente_transportador' => $codigo_cliente_transportador,
					'codigo_cliente_embarcador' => $codigo_cliente_embarcador,
				));
				if (!parent::incluir($data)) {
					throw new Exception("Erro ao incluir vínculo Embarcador/Transportador");
				}
				$codigo_embarcador_transportador = $this->id;
			}
			if ($codigo_produto) {
				$ClienteProduto =& ClassRegistry::init('ClienteProduto');
				$ClienteProdutoPagador =& ClassRegistry::init('ClienteProdutoPagador');
				$embarcador_tem_contrato = $ClienteProduto->find('count', array('conditions' => array('codigo_cliente' => $codigo_cliente_embarcador, 'codigo_produto' => $codigo_produto, 'codigo_motivo_bloqueio' => MotivoBloqueio::MOTIVO_OK)));
				if ($embarcador_tem_contrato) {
					$data = array(
						'codigo_embarcador_transportador' => $codigo_embarcador_transportador,
						'codigo_produto' => $codigo_produto,
						/*RETIRADA CONDIÇÃO DEVIDO A ERRO GERADO QUANDO O VINCULO JA EXISTE COM UM 
						PAGADOR DIFERENTE DO EMBARCADOR.
						ALTERADO PARA, SE EXISTIR UM PAGADOR, NÃO TENTAR INCLUIR UM NOVO */
						//'ClienteProdutoPagador.codigo_cliente_pagador' => $codigo_cliente_embarcador,
					);
					if ($ClienteProdutoPagador->find('count', array('conditions' => $data)) < 1) {
						$data_inclusao = array(
							'codigo_embarcador_transportador' => $codigo_embarcador_transportador,
							'codigo_produto' => $codigo_produto,
							'codigo_cliente_pagador' => $codigo_cliente_embarcador,
						);
						if (!$ClienteProdutoPagador->incluir($data_inclusao)) {
							throw new Exception("Erro ao incluir Produto/Pagador");
						}
					}
				}
			}

			if (!$in_another_transaction) $this->commit();
			return true;
		} catch (Exception $e) {
			if (!$in_another_transaction) $this->rollback();
			return false;
		}
	}
}
?>