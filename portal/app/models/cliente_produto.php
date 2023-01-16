<?php
class ClienteProduto extends AppModel {
	public $name = 'ClienteProduto';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'cliente_produto';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_cliente_produto'));
	public $belongsTo = array(
		'Produto' => array(
			'className' => 'Produto',
			'foreignKey' => 'codigo_produto'
		),
		'MotivoBloqueio' => array(
			'className' => 'MotivoBloqueio',
			'foreignKey' => 'codigo_motivo_bloqueio'
		),

	);

	public function beforeSave() {
		if (!empty($this->data[$this->name][$this->primaryKey]) && isset($this->data[$this->name]['codigo_motivo_bloqueio'])) {
			$anterior = $this->find('first', array('fields' => 'codigo_motivo_bloqueio', 'conditions' => array($this->name.'.codigo' => $this->data[$this->name][$this->primaryKey])));
			if ($anterior) {
				if ($this->data[$this->name]['codigo_motivo_bloqueio'] != $anterior[$this->name]['codigo_motivo_bloqueio']) {
					if ($this->data[$this->name]['codigo_motivo_bloqueio'] == 1) {
						$this->data[$this->name]['data_inativacao'] = null;
					} else {
						if ($anterior[$this->name]['codigo_motivo_bloqueio'] == 1 || empty($anterior[$this->name]['data_inativacao'])) {
							$this->data[$this->name]['data_inativacao'] = date('Y-m-d H:i:s');
						}
					}
				}
			}
		}
		return true;
	}

	public $validate = array(
		'codigo_cliente' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o Cliente',
			'required' => true,
		),
		'codigo_produto' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o produto',
			),
			'validaClienteProdutoUnico' => array(
				'rule' => 'validaClienteProdutoUnico',
				'message' => 'Este cliente já possui este produto.'
			)
		),
		'codigo_motivo_bloqueio' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe um status'
			),
			// 'validaStatusClienteProduto' => array(
			// 	'rule' => 'validaStatusClienteProduto',
			// 	'message' => 'O status selecionado para o produto não confere com a pendência selecionada'
			// ),
			'verificaSeClienteAtivo' => array(
				'rule' => 'verificaSeClienteAtivo',
				'message' => 'Esse cliente está inativo, antes ele deve ser reativado'
			),
		),
		'codigo_motivo_cancelamento' => array(
			'verificaSeCancelamento' => array(
				'rule' => 'verificaSeCancelamento',
				'message' => 'Favor informar o motivo de cancelamento'
			)
		),
	);

	const BUONNYSAT = 82;

	public function unbindAll() {
        $tiposDeJoins = array(
            'hasMany',
            'hasOne',
            'belongsTo',
            'hasAndBelongsToMany'
        );

        foreach ($tiposDeJoins as $join) {
            $models = array_keys($this->$join);
            $this->unbindModel(array($join => $models));
        }
    }

	public function validaStatusClienteProduto() {
		if (isset($this->data[$this->name]['pendencias'])) {
			if (empty($this->data[$this->name]['pendencias'])) {
				return $this->data[$this->name]['codigo_motivo_bloqueio'] != 8;
			} else {
				return $this->data[$this->name]['codigo_motivo_bloqueio'] == 8;
			}
		} else {
			if (empty($this->data[$this->name]['pendencia_financeira']) && empty($this->data[$this->name]['pendencia_comercial']) && empty($this->data[$this->name]['pendencia_juridica'])) {
				return $this->data[$this->name]['codigo_motivo_bloqueio'] != 8;
			} else {
				return $this->data[$this->name]['codigo_motivo_bloqueio'] == 8;
			}
		}
	}

	public function verificaSeCancelamento() {
		if((isset($this->data[$this->name]['codigo_motivo_bloqueio']) && !empty($this->data[$this->name]['codigo_motivo_bloqueio'])) && ($this->data[$this->name]['codigo_motivo_bloqueio'] == 17) && !$this->data[$this->name]['codigo_motivo_cancelamento'])
			return false;
		return true;
	}

	public function verificaSeClienteAtivo() {
		if (isset($this->data[$this->name]['codigo_cliente']) && !empty($this->data[$this->name]['codigo_cliente'])) {
			$Cliente = & ClassRegistry::init('Cliente');
			$inativo = $Cliente->find('count', array('recursive' => -1, 'conditions' => array('codigo' => $this->data[$this->name]['codigo_cliente'], 'ativo' => 0)));
			return ($inativo && $this->data[$this->name]['codigo_motivo_bloqueio'] == 1) ? false: true;
		} else {
			return true;
		}
	}

	public function carregaDadosLogFaturamento(){
		$faturamento['codigo_produto'] = Produto::SCORECARD; // codigo ficha scorecard
		$faturamento['codigo_cliente'] = $this->data['FichaScorecard']['codigo_cliente'];
		$faturamento['codigo_cliente_pagador']  = $this->data['FichaScorecard']['codigo_cliente'];
		$faturamento['codigo_profissional']  = $this->data['Profissional']['codigo'];
		$faturamento['codigo_profissional_tipo']  = $this->data['FichaScorecard']['codigo_profissional_tipo'];
		$faturamento['codigo_veiculo']  = isset($this->data['Veiculo']['codigo_veiculo']);
		$faturamento['codigo_veiculo_carreta']  =''; // no informAÇões sempre foi passado null
		$faturamento['codigo_tipo_operacao']  = $this->LogFaturamentoTeleconsult->obterTipoOperacaoLogFaturamento($faturamento['codigo_cliente'],$faturamento['codigo_profissional'],Produto::SCORECARD); 
		$this->loadModel('ClienteOperacao');
		$this->ClienteOperacao ->operacoesDoCliente($faturamento['codigo_cliente']);
		$codigo_operacao= $this->ClienteOperacao ->operacoesDoCliente($faturamento['codigo_cliente']); 
		$faturamento['codigo_operacao']  = $codigo_operacao[0]['ClienteOperacao']['codigo_operacao'];
		$faturamento['valor']  = $this->data['FichaScorecard']['codigo_carga_valor'];
		$this->loadModel('ClienteProduto');
		$valor_taxa_bancaria = $this->ClienteProduto->carregarClienteProduto($faturamento['codigo_cliente']);
		$faturamento['valor_premio_minimo']  = $valor_taxa_bancaria['0']['ClienteProduto']['valor_premio_minimo'];
		$faturamento['valor_taxa_bancaria']  = $valor_taxa_bancaria['0']['ClienteProduto']['valor_taxa_bancaria'];
		$faturamento['codigo_carga_tipo']  = $this->data['FichaScorecard']['codigo_carga_tipo'];
		$faturamento['codigo_endereco_cidade_origem']   = $this->data['FichaScorecard']['codigo_endereco_cidade_carga_origem'];
		$faturamento['codigo_endereco_cidade_destino']  = $this->data['FichaScorecard']['codigo_endereco_cidade_carga_destino'];
		$faturamento['codigo_carga_valor']  = $this->data['FichaScorecard']['codigo_carga_valor'];
		$faturamento['observacao']  = $this->data['FichaScorecard']['observacao'];                                                                                                                                                                                                                   
	}

	public function bindClienteProdutoLog() {
		$this->bindModel(array(
			'hasMany' => array(
				'ClienteProdutoLog' => array(
					'class' => 'ClienteProdutoLog',
					'foreignKey' => 'codigo_cliente_produto',
					'order' => 'data_inclusao DESC',
					'limit' => 1
				)
			)
		));
	}

	public function unbindClienteProdutoLog() {
		$this->unbindModel(array(
			'hasMany' => array(
				'ClienteProdutoLog'
			)
		));
	}
	
	public function bindLazyCliente() {
		$this->bindModel(
			array(
				'belongsTo' => array(
					'Cliente' => array(
						'class' => 'Cliente',
						'foreignKey' => 'codigo_cliente'
					)
				)
			)
		);
	}
		
	public function bindClienteProdutoContrato() {
		$this->bindModel(
			array(
				'hasOne' => array(
					'ClienteProdutoContrato' => array(
						'class' => 'ClienteProdutoContrato',
						'foreignKey' => 'codigo_cliente_produto'
					)
				)
			)				
		);
	}
		
	public function unbindClienteProdutoContrato() {
		$this->unbindModel(
			array(
				'hasOne' => array(
					'ClienteProdutoContrato'
				)
			)
		);
	}
		
	public function carregarClienteProduto($codigo_cliente) {
		$this->bindClienteProdutoContrato();
		$clientes_produtos = $this->find('all', array('conditions' => array('ClienteProduto.codigo_cliente' => $codigo_cliente)));
		$this->unbindClienteProdutoContrato();
		return $clientes_produtos;
	}

	public function validaClienteProdutoUnico($check) {
		if (!empty($this->data[$this->name]['codigo_cliente']) && !empty($this->data[$this->name]['codigo_produto'])) {
			$conditions = array(
				$this->name.'.codigo_cliente' => $this->data[$this->name]['codigo_cliente'],
				$this->name.'.codigo_produto' => $this->data[$this->name]['codigo_produto'],
			);
			if (!empty($this->data[$this->name][$this->primaryKey])) {
				$conditions[$this->name.'.'.$this->primaryKey.' !='] = $this->data[$this->name][$this->primaryKey];
			}
			return $this->find('count', compact('conditions')) == 0;
		}
	}

	public function produtosServicosProfissionaisPorClienteTipoProfissional($codigo_cliente, $somente_tlc = false) {
		$this->bindModel(array('hasMany' => array(
				// 'ClienteProdutoServico' => array(
				// 	'className' => 'ClienteProdutoServico',
				// 	'foreignKey' => 'codigo_cliente_produto'
				// ),
				'ClienteProdutoServico2' => array(
					'className' => 'ClienteProdutoServico2',
					'foreignKey' => 'codigo_cliente_produto'
				),
			)
		));
		$conditions = array('ClienteProduto.codigo_cliente' => $codigo_cliente);
		if ($somente_tlc) {
			$conditions['ClienteProduto.codigo_produto'] = array(Produto::TELECONSULT_STANDARD, Produto::TELECONSULT_PLUS, Produto::SCORECARD);
		}
		$produtos = $this->find('all', array('conditions' => $conditions, 'recursive' => 2));

		foreach ($produtos as $key_produto => $produto){
			$servicos = array();
			foreach ($produto['ClienteProdutoServico2'] as $profissional) {
				$servico = array('Servico' => $profissional['Servico']);
				if (!in_array($servico, $servicos)) $servicos[]['Servico'] = $profissional['Servico'];
			}
			foreach ($produto['ClienteProdutoServico2'] as $profissional) {
				$servico = array('Servico' => $profissional['Servico']);
				$key_servico = $this->procuraServico($servico, $servicos, false);
				unset($profissional['Servico']);
				$servicos[$key_servico]['ClienteProdutoServico2'][] = $profissional;
				$servicos[$key_servico]['Servico']['valor'] = $profissional['valor'];
			}
			$produtos[$key_produto]['ClienteProdutoServico2'] = $servicos;
		}
		
		return $produtos;
	}

	public function produtosServicosProfissionaisPorCliente($codigo_cliente, $somente_tlc = false) {
		$Servico = ClassRegistry::init('Servico');
		$this->bindModel(array('hasMany' => array(
				'ClienteProdutoServico2' => array(
					'className' => 'ClienteProdutoServico2',
					'foreignKey' => 'codigo_cliente_produto'
				),
			)
		));
		$conditions = array('ClienteProduto.codigo_cliente' => $codigo_cliente);
		if ($somente_tlc) {
			$conditions['ClienteProduto.codigo_produto'] = array(Produto::TELECONSULT_STANDARD, Produto::TELECONSULT_PLUS, Produto::SCORECARD);
		}
		$produtos = $this->find('all', array('conditions' => $conditions, 'recursive' => 2));
		foreach ($produtos as $key_produto => $produto){
			foreach ($produto['ClienteProdutoServico2'] as $key => $cliente_produto_servico2) {
				$servico = $Servico->carregar($cliente_produto_servico2['codigo_servico']);
				$produtos[$key_produto]['ClienteProdutoServico2'][$key]['Servico'] = $servico['Servico'];
			}
		}
		return $produtos;
	}

	/**
	 * Se já existe atualiza senão inclui
	 *
	 * @param array $dados
	 *
	 * @return boolean
	 */
	public function gravar($dados) {

		$conditions = array($this->name.'.codigo_cliente' => $dados[$this->name]['codigo_cliente'], $this->name.'.codigo_produto' => $dados[$this->name]['codigo_produto']);

		$cliente_produto = $this->find('first', compact('conditions'));

		if ($cliente_produto)
		{
			
			$dados[$this->name]['cliente_produto_servico'] = $cliente_produto[$this->name]['cliente_produto_servico'];
			$dados[$this->name]['codigo'] = $cliente_produto[$this->name]['codigo'];
			return $this->atualizar($dados);
		} else {
			return $this->incluir($dados);
		}
	}

	/**
	 * Insere um novo produto para o cliente.
	 *
	 * @param array $dados
	 *
	 * @return boolean
	 */
	public function incluir($dados, $metodo_antigo = false) {

		if (!$metodo_antigo) {
			return parent::incluir($dados);			
		}
		try {
			$this->alteracoesProdutos = null;
			$this->inclusaoCliente = null;
			$this->create();
			// Tentativa de edição (html injection)
			unset($dados['ClienteProduto']['codigo']);

			if(!isset($dados['ClienteProduto']['codigo_cliente']) || empty($dados['ClienteProduto']['codigo_cliente'])) {
				throw new Exception();
			}
			
			$qtde_produtos_do_cliente_anterior = $this->find('count', array('conditions' => array('ClienteProduto.codigo_cliente' => $dados['ClienteProduto']['codigo_cliente'])));

			$result = $this->save($dados);

			$produto = $this->find('first', array(
				'recursive' => 0,
				'fields' => array(
					'ClienteProduto.codigo', 'Produto.descricao'
				),
				'conditions' => array(
					'ClienteProduto.codigo_produto' => $dados['ClienteProduto']['codigo_produto'],
					'ClienteProduto.codigo_cliente' => $dados['ClienteProduto']['codigo_cliente']
				),
				'order' => array(
					'ClienteProduto.codigo desc'
				)
			));
			$novo_produto = $produto['Produto']['descricao'];

			if ($result) {
				if ($qtde_produtos_do_cliente_anterior > 0) {
					$this->alteracoesProdutos = $novo_produto;
				} else {
					$this->inclusaoCliente = $novo_produto;
				}
			}

			return $result;
		} catch(Exception $e) {
			return false;
		}
	}

	public function incluirAssinatura($dados) {	
	
		$this->bindModel(array('hasMany' => array(
			'ListaDePrecoProdutoServico' => array('foreignKey' => 'codigo_servico'),
			'ClienteProdutoServico2' 	 => array('foreignKey' => 'codigo_cliente_produto'),
			// 'ClienteProdutoServico' 	 => array('foreignKey' => 'codigo_cliente_produto')
			)));

		try {
			
			if(!isset($dados['ClienteProdutoServico2']['codigo_cliente']) || empty($dados['ClienteProdutoServico2']['codigo_cliente'])) {
				throw new Exception();
			}

			$retorno = array('erros' => array());

			$codigo_cliente = $dados['ClienteProdutoServico2']['codigo_cliente'];
			
			$this->query('BEGIN TRANSACTION');
			foreach ($dados['ClienteProdutoServico2']['codigo_lista_de_preco_produto_servico'] as $key => $value) {
				
				if ($value == 1) {
					
					$codigo_cliente_pagador = $dados['ClienteProdutoServico2']['codigo_cliente_pagador'][$key];

					$valor_premio_minimo 	= $dados['ClienteProdutoServico2']['valor_premio_minimo'][$key];
					$tipo_premio_minimo	 	= $dados['ClienteProdutoServico2']['tipo_premio_minimo'][$key];
					$valor_taxa_corretora 	= $dados['ClienteProdutoServico2']['valor_taxa_corretora'][$key];
					$valor_taxa_bancaria 	= $dados['ClienteProdutoServico2']['valor_taxa_bancaria'][$key];
					$quantidade          	= !empty($dados['ClienteProdutoServico2']['quantidade'][$key]) ? $dados['ClienteProdutoServico2']['quantidade'][$key] : 0;
					$valor                  = $dados['ClienteProdutoServico2']['valor'][$key];
					
					$lista_preco_produto_servico = $this->ListaDePrecoProdutoServico->find('all',array('conditions' => array('ListaDePrecoProdutoServico.codigo' => $key)));
					
					$codigo_servico = $lista_preco_produto_servico[0]['Servico']['codigo'];
					$codigo_produto = $lista_preco_produto_servico[0]['ListaDePrecoProduto']['codigo_produto'];

					$cliente_produto['ClienteProduto']['codigo_cliente'] 		 = $codigo_cliente;
					$cliente_produto['ClienteProduto']['codigo_produto'] 		 = $codigo_produto;
					$cliente_produto['ClienteProduto']['data_faturamento']		 = Date('Y-m-d H:i:s');
					$cliente_produto['ClienteProduto']['codigo_motivo_bloqueio'] = 1;
					$cliente_produto['ClienteProduto']['valor_premio_minimo'] 	 = ($tipo_premio_minimo == 'PRODUTO' ? $valor_premio_minimo : 0);
					$cliente_produto['ClienteProduto']['valor_taxa_corretora']   = $valor_taxa_corretora;
					$cliente_produto['ClienteProduto']['valor_taxa_bancaria']    = $valor_taxa_bancaria;

					$atualizar = !$this->incluir($cliente_produto, true);
					$cliente_produto_incluido = $this->find('first',array('conditions' => array('codigo_cliente' => $codigo_cliente, 'codigo_produto' => $codigo_produto),'fields' => array('codigo') )) ;

					if($atualizar) {
						$cliente_produto['ClienteProduto']['codigo'] = $cliente_produto_incluido['ClienteProduto']['codigo'];
						parent::atualizar($cliente_produto,false,array('valor_premio_minimo','qtd_premio_minimo','valor_taxa_bancaria','valor_taxa_corretora','codigo_usuario_alteracao','data_alteracao'));
					}
					
					if (isset($cliente_produto_incluido) && !empty($cliente_produto_incluido)) {
						$cliente_produto_servico = array(
							'ClienteProdutoServico2' => array(
								'codigo_cliente_produto' 	=> $cliente_produto_incluido['ClienteProduto']['codigo'],
								'codigo_servico' 			=> $codigo_servico,
								'valor' 					=> $valor,
								'data_inclusao' 			=> Date('Y-m-d H:i:s'),
								'valor_premio_minimo' 		=> ($tipo_premio_minimo == 'SERVICO' ? $valor_premio_minimo : 0),
								'codigo_cliente' 			=> $codigo_cliente,
								'codigo_cliente_pagador' 	=> $codigo_cliente_pagador,
								'codigo_produto' 			=> $codigo_produto,
								'codigo_motivo_bloqueio' 	=> 1,
								'quantidade'                => 1
							)
						);
						
						$ClienteProdutoServico2Incluir = classRegistry::init('ClienteProdutoServico2');
						$return_servico_incluido = $ClienteProdutoServico2Incluir->incluir($cliente_produto_servico);
						if(isset($return_servico_incluido) && !empty($return_servico_incluido)) {
							unset($return_servico_incluido['ClienteProdutoServico2']['data_inclusao']);							
							array_push($retorno, $return_servico_incluido);
						
							if ($codigo_produto == 1 || $codigo_produto == 2 || $codigo_produto==Produto::SCORECARD) {
								for ($i = 0 ; $i < 10; $i++) {
									$dados_cliente_produto_servico = array(
										'ClienteProdutoServico2' => array(
											'codigo_cliente_produto' 	=> $cliente_produto_incluido['ClienteProduto']['codigo'],
											'codigo_servico' 			=> $codigo_servico,
											'codigo_profissional_tipo' 	=> $i+1,
											'valor' 					=> $valor,
											'consistencia_motorista' 	=> (($i ==0) ? true : false),
											'consistencia_veiculo' 		=> true,
											'tempo_pesquisa' 			=> (($i ==0) ? 80 : 4320),
											'validade' 					=> 6,
										)
									);
									$return_servico_incluido_antigo = $this->ClienteProdutoServico2->incluir($dados_cliente_produto_servico);
								}
							} else {
								$dados_cliente_produto_servico = array(
									'ClienteProdutoServico2' => array(
										'codigo_cliente_produto' 	=> $cliente_produto_incluido['ClienteProduto']['codigo'],
										'codigo_servico' 			=> $codigo_servico,
										'codigo_profissional_tipo' 	=> null,
										'valor' 					=> $valor,
										'consistencia_motorista' 	=> 0,
										'consistencia_veiculo' 		=> 0,
										'consulta_embarcador' 		=> 0,
										'tempo_pesquisa' 			=> 4320,
										'validade' 					=> 6,
									)
								);

								$return_servico_incluido_antigo = $this->ClienteProdutoServico2->incluir($dados_cliente_produto_servico);					        

							}
						}else{							
							$retorno['erros'][$key] = $ClienteProdutoServico2Incluir->validationErrors;
						}
					//Se não criou o produto e este não existe para este cliente	
					} else {
						$retorno['erros'][$key] = $this->validationErrors;
					}
				}
			}

			if(count($retorno['erros']) == 0){
				$this->commit();
			}else{
				$this->rollback();
			}
			return $retorno;

		} catch(Exception $e) {
			$this->rollback();
			
			return false;
		}
	}

	public function copiarAssinatura($dados, $codigo_cliente) {
		$this->ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');

		try {
			
			if(empty($codigo_cliente)) {
				throw new Exception();
			}

			$retorno = array('erros' => array());
			
			$this->query('BEGIN TRANSACTION');
			foreach ($dados['ClienteProduto'] as $codigo_produto => $dados) {
				foreach ($dados as $codigo_servico => $valor) {
					
					$cliente_produto['ClienteProduto']['codigo_cliente'] 		 = $codigo_cliente;
					$cliente_produto['ClienteProduto']['codigo_produto'] 		 = $codigo_produto;
					$cliente_produto['ClienteProduto']['data_faturamento']		 = Date('Y-m-d H:i:s');
					$cliente_produto['ClienteProduto']['codigo_motivo_bloqueio'] = 1;
					$cliente_produto['ClienteProduto']['valor_premio_minimo'] 	 = 0;
					$cliente_produto['ClienteProduto']['valor_taxa_corretora']   = 0;
					$cliente_produto['ClienteProduto']['valor_taxa_bancaria']    = 0;

					$atualizar = !$this->incluir($cliente_produto, true);
					$cliente_produto_incluido = $this->find('first',array('conditions' => array('codigo_cliente' => $codigo_cliente, 'codigo_produto' => $codigo_produto),'fields' => array('codigo') )) ;

					if($atualizar) {
						$cliente_produto['ClienteProduto']['codigo'] = $cliente_produto_incluido['ClienteProduto']['codigo'];
						parent::atualizar($cliente_produto,false,array('valor_premio_minimo','qtd_premio_minimo','valor_taxa_bancaria','valor_taxa_corretora','codigo_usuario_alteracao','data_alteracao'));
					}
					
					if (isset($cliente_produto_incluido) && !empty($cliente_produto_incluido)) {
						$cliente_produto_servico = array(
							'ClienteProdutoServico2' => array(
								'codigo_cliente_produto' 	=> $cliente_produto_incluido['ClienteProduto']['codigo'],
								'codigo_servico' 			=> $codigo_servico,
								'valor' 					=> $valor,
								'data_inclusao' 			=> Date('Y-m-d H:i:s'),
								'valor_premio_minimo' 		=> 0,
								'codigo_cliente_pagador' 	=> $codigo_cliente,
								'codigo_produto' 			=> $codigo_produto,
								'codigo_motivo_bloqueio' 	=> 1,
								'quantidade'                => 1
							)
						);
						
						$ClienteProdutoServico2Incluir = classRegistry::init('ClienteProdutoServico2');
						$return_servico_incluido = $ClienteProdutoServico2Incluir->incluir($cliente_produto_servico);
						if(isset($return_servico_incluido) && !empty($return_servico_incluido)) {
							unset($return_servico_incluido['ClienteProdutoServico2']['data_inclusao']);							
							array_push($retorno, $return_servico_incluido);
						
							if ($codigo_produto == 1 || $codigo_produto == 2 || $codigo_produto==Produto::SCORECARD) {
								for ($i = 0 ; $i < 10; $i++) {
									$dados_cliente_produto_servico = array(
										'ClienteProdutoServico2' => array(
											'codigo_cliente_produto' 	=> $cliente_produto_incluido['ClienteProduto']['codigo'],
											'codigo_servico' 			=> $codigo_servico,
											'codigo_profissional_tipo' 	=> $i+1,
											'valor' 					=> $valor,
											'consistencia_motorista' 	=> (($i ==0) ? true : false),
											'consistencia_veiculo' 		=> true,
											'tempo_pesquisa' 			=> (($i ==0) ? 80 : 4320),
											'validade' 					=> 6,
											'codigo_produto'			=> $codigo_produto,
										)
									);
									$return_servico_incluido_antigo = $this->ClienteProdutoServico2->incluir($dados_cliente_produto_servico);
								}
							} else {
								$dados_cliente_produto_servico = array(
									'ClienteProdutoServico2' => array(
										'codigo_cliente_produto' 	=> $cliente_produto_incluido['ClienteProduto']['codigo'],
										'codigo_servico' 			=> $codigo_servico,
										'codigo_profissional_tipo' 	=> null,
										'valor' 					=> $valor,
										'consistencia_motorista' 	=> 0,
										'consistencia_veiculo' 		=> 0,
										'consulta_embarcador' 		=> 0,
										'tempo_pesquisa' 			=> 4320,
										'validade' 					=> 6,
										'codigo_produto'			=> $codigo_produto,
									)
								);

								$return_servico_incluido_antigo = $this->ClienteProdutoServico2->incluir($dados_cliente_produto_servico);					        

							}
						} else {							
							$retorno['erros'][$codigo_produto][$codigo_servico] = $ClienteProdutoServico2Incluir->validationErrors;
						}
					//Se não criou o produto e este não existe para este cliente	
					} else {
						$retorno['erros'][$codigo_produto][$codigo_servico] = $this->validationErrors;
					}
				}
			}

			if(count($retorno['erros']) == 0) {
				unset($retorno['erros']);
				$this->commit();
			} else {
				$this->rollback();
			}
			return $retorno;

		} catch(Exception $e) {
			$this->rollback();
			
			return false;
		}
	}

	public function converteFiltroEmCondition($data) {
		$conditions = array();
		if (!empty($data['codigo'])) {
			$conditions['ClienteProduto.codigo'] = $data['codigo'];
		}
		if (!empty($data['codigo_cliente'])) {
			$conditions['ClienteProduto.codigo_cliente'] = preg_replace('/\D/', '', $data['codigo_cliente']);
		}
		if (!empty($data['razao_social']) && empty($data['codigo_cliente'])) {
			$conditions['Cliente.razao_social like'] = '%' . $data['razao_social'] . '%';
		}
		if (!empty($data['codigo_produto'])) {
			$conditions['ClienteProduto.codigo_produto'] = $data['codigo_produto'];
		}
		if (!empty($data['codigo_status_contrato'])) {
			$conditions['ClienteProdutoContrato.codigo_status_contrato'] = $data['codigo_status_contrato'];
		}
		if (!empty($data['codigo_contrato'])) {
			$conditions['ClienteProdutoContrato.numero'] = $data['codigo_contrato'];
		}
		if (!empty($data['data_envio'])) {
			$conditions['ClienteProdutoContrato.data_envio >='] =  AppModel::dateToDbDate($data['data_envio']) . ' 00:00:00.0';
			$conditions['ClienteProdutoContrato.data_envio <='] =  AppModel::dateToDbDate($data['data_envio']) . ' 23:59:59.997';
		}
		if (!empty($data['data_contrato'])) {
			$conditions['ClienteProdutoContrato.data_contrato >='] =  AppModel::dateToDbDate($data['data_contrato']) . ' 00:00:00.0';
			$conditions['ClienteProdutoContrato.data_contrato <='] =  AppModel::dateToDbDate($data['data_contrato']) . ' 23:59:59.997';
		}
		if (!empty($data['codigo_motivo_bloqueio'])) {
			$conditions['ClienteProduto.codigo_motivo_bloqueio'] = $data['codigo_motivo_bloqueio'];
		}
		if (isset($data['possui_contrato'])) {
			if ($data['possui_contrato'] === 0) {
				$conditions[] = 'ClienteProdutoContrato.codigo IS NULL';
			} elseif ($data['possui_contrato'] == 1) {
				$conditions[] = 'ClienteProdutoContrato.codigo IS NOT NULL';
			}
		}
		if (!empty($data['mes'])){
			$conditions['MONTH(ClienteProduto.data_inativacao)'] = array($data['mes']);
		}
		if (!empty($data['codigo_motivo_cancelamento'])){
			$conditions['ClienteProduto.codigo_motivo_cancelamento'] = $data['codigo_motivo_cancelamento'];
		}

		return $conditions;
	}


	/**
	 * Obtem um ClienteProduto pelo codigo do Cliente e pelo codigo do Produto
	 *
	 * @param int $codigo_cliente
	 * @param int $codigo_produto
	 *
	 * @return array
	 */
	public function getClienteProdutoByCodigoClienteEProduto($codigo_cliente, $codigo_produto) {
		$result = $this->find('first', array(
			'conditions' => array(
				'codigo_cliente' => $codigo_cliente,
				'codigo_produto' => $codigo_produto
		)));

		return $result;
	}

	/**
	 * Obtem um ClienteProduto pelo codigo (pk).
	 *
	 * @param int $codigo
	 *
	 * @return void
	 */
	public function getClienteProdutoByCodigo($codigo) {
		$this->bindClienteProdutoLog();
		$result = $this->find('first', array(
			'conditions' => array(
				'ClienteProduto.codigo' => $codigo,
		)));
		$this->unbindClienteProdutoLog();
		return $result;
	}

	/**
	 * Atualiza dados de um determinado produto
	 *
	 * @param array $dados
	 *
	 * @return boolean
	 */
	public function atualizar($dados, $metodo_antigo = false) {
		if (!$metodo_antigo) {
			return parent::atualizar($dados);			
			
		}

		try {

			$this->alteracoesProdutos = array();
			$this->inclusaoCliente = array();

			if (!isset($dados['ClienteProduto']['codigo']) || empty($dados['ClienteProduto']['codigo'])) {
				throw new Exception();
			}

			$result = $this->save($dados);
			
			if (!isset($dados['ClienteProduto']['codigo_produto']) || empty($dados['ClienteProduto']['codigo_produto'])) {
				$clienteproduto = $this->find('first',array('conditions' => array('ClienteProduto.codigo' => $result['ClienteProduto']['codigo'])));
				$dados['ClienteProduto']['codigo_produto'] = $clienteproduto['ClienteProduto']['codigo_produto'];
			  
			}

			if ($result) {
				$qtde_produtos_do_cliente_anterior = $this->find('count', array(
					'conditions' => array(
						'ClienteProduto.codigo_cliente' => $dados['ClienteProduto']['codigo_cliente']
					)
				));

				$produto = $this->find('first', array(
					'recursive' => 0,
					'fields' => array(
						'ClienteProduto.codigo', 'Produto.descricao'
					),
					'conditions' => array(
						'ClienteProduto.codigo_produto' => $dados['ClienteProduto']['codigo_produto'],
						'ClienteProduto.codigo_cliente' => $dados['ClienteProduto']['codigo_cliente']
					),
					'order' => array(
						'ClienteProduto.codigo desc'
					)
				));
				$novo_produto = $produto['Produto']['descricao'];
				if ($qtde_produtos_do_cliente_anterior > 0) {
					array_push($this->alteracoesProdutos, $novo_produto);
				} else {
					array_push($this->inclusaoCliente, $novo_produto);
				}
				return true;
				
			} else {
				throw new Exception();
			}

		} catch (Exception $e) {
			
			pr($e);
			exit;
			
			return false;
		}
	}

	public function excluir($codigo_cliente_produto, $metodo_antigo = false) {
		if (!$metodo_antigo) {
			return parent::excluir($codigo_cliente_produto);
		}
		$this->alteracoesProdutos = array();
		$cliente_produto = $this->getClienteProdutoByCodigo($codigo_cliente_produto);
		$result = $this->delete($codigo_cliente_produto);
		if ($result == true) {
			array_push($this->alteracoesProdutos, $cliente_produto['Produto']['descricao']);
			return true;
		} else {
			return false;
		}
	}

	public function procuraServico($procurado, $servicos) {
		$retorno = null;
		foreach($servicos as $key => $servico){
			if ($servico['Servico']['codigo'] == $procurado['Servico']['codigo']) {
				$retorno = $key;
				break;
			}
		}
		return $retorno;
	}

	public function getAll($options) {
		$this->Cliente =& ClassRegistry::init('Cliente');
		$this->ClienteProdutoContrato =& ClassRegistry::init('ClienteProdutoContrato');
		$this->StatusContrato =& ClassRegistry::init('StatusContrato');
		$default = array(
			'recursive' => 0,
				'fields' => array(
					'Cliente.codigo',
					'Cliente.data_inclusao',
					'Cliente.razao_social',
					'ClienteProduto.data_faturamento',
					'Produto.descricao',
					'StatusContrato.descricao',
					'MotivoBloqueio.descricao',
					'ClienteProdutoContato.codigo',
					'ClienteProdutoContato.numero',
					'ClienteProdutoContato.codigo_status_contrato',
					'convert(varchar, ClienteProdutoContato.data_contrato, 103) as data_contrato',
					'convert(varchar, ClienteProdutoContato.data_envio, 103) as data_envio',
					'convert(varchar, ClienteProdutoContato.data_vigencia, 103) as data_vigencia',
				),
				'joins' => array(
					array(
						'table' => "{$this->Cliente->databaseTable}.{$this->Cliente->tableSchema}.{$this->Cliente->useTable}",
						'alias' => 'Cliente',
						'type' => 'INNER',
						'conditions' => 'Cliente.codigo = ClienteProduto.codigo_cliente'
					),
					array(
						'table' => "{$this->ClienteProdutoContrato->databaseTable}.{$this->ClienteProdutoContrato->tableSchema}.{$this->ClienteProdutoContrato->useTable}",
						'alias' => 'ClienteProdutoContato',
						'type' => 'LEFT',
						'conditions' => 'ClienteProdutoContato.codigo_cliente_produto = ClienteProduto.codigo'
					),
					array(
						'table' => "{$this->StatusContrato->databaseTable}.{$this->StatusContrato->tableSchema}.{$this->StatusContrato->useTable}",
						'alias' => 'StatusContrato',
						'type' => 'LEFT',
						'conditions' => 'StatusContrato.codigo = ClienteProdutoContato.codigo_status_contrato'
					),
				),
		);

		$options = array_merge($default, $options);

		return $this->find('all', $options);
	}

	public function buscaPorCodigoCliente($codigo_cliente) {
		$conditions = array(
			'conditions' => array(
				'ClienteProduto.codigo_cliente' => $codigo_cliente
			),
			'order' => array(
				'ClienteProduto.data_inclusao desc'
			)
		);
		return $this->find('all', $conditions);
	}

	public function listaProdutos($codigo_cliente, $teleconsult = false, $naveg = false) {
		if (!empty($codigo_cliente) && preg_match('/^[0-9]+$/', $codigo_cliente)) {
			$options = array(
				'fields' => array('Produto.codigo', 'Produto.descricao'),
				'conditions' => array(
					'ClienteProduto.codigo_cliente' => $codigo_cliente,
					//'Produto.ativo' => 1
				)
			);
			if ($teleconsult) {
				$options['conditions']['Produto.descricao LIKE'] = 'TELECONSULT%';
			}
			if ($naveg) {
				$options['conditions']['Produto.codigo_naveg <>'] = '';
				$options['conditions']['Produto.codigo_naveg NOT'] = null;
			}
			$produtos_cliente = $this->find('all', $options);
			return Set::combine($produtos_cliente, '{n}.Produto.codigo', '{n}.Produto.descricao');
		}
		return false;
	}
	
	public function listaProdutosTLCS($codigo_cliente) {
		if (!empty($codigo_cliente) && preg_match('/^[0-9]+$/', $codigo_cliente)) {
			$options = array(
				'fields' => array('Produto.codigo', 'Produto.descricao'),
				'conditions' => array(
					'ClienteProduto.codigo_cliente' => $codigo_cliente,
					'Produto.codigo' => array(1, 2, Produto::SCORECARD)
				)
			);
			$produtos_cliente = $this->find('all', $options);
			return Set::combine($produtos_cliente, '{n}.Produto.codigo', '{n}.Produto.descricao');
		}
		return false;
	}
	
	public function obterEnderecoDoClientePorCodigoClienteProduto($codigo_cliente_produto = null) {
		$ClienteEndereco = & ClassRegistry::init('ClienteEndereco');
		
		if($codigo_cliente_produto) {
			$codigo_cliente = $this->find('first', array('fields' => $this->name.'.codigo_cliente', 'recursive' => -1, 'conditions' => array($this->name.'.codigo' => $codigo_cliente_produto)));
			$codigo_cliente = $codigo_cliente[$this->name]['codigo_cliente'];
			$endereco = $ClienteEndereco->find('first', array('conditions' => array('ClienteEndereco.codigo_cliente' => $codigo_cliente), 'recursive' => -1));
        	$enderecoCompleto = $endereco['ClienteEndereco']['logradouro'] . ' ' . $endereco['ClienteEndereco']['numero'] . ' - ' . $endereco['ClienteEndereco']['cidade'] . ' - ' . $endereco['ClienteEndereco']['estado_descricao'];
			return (!is_array($endereco) || !is_array($endereco_completo)) ? false: array_merge($endereco, $endereco_completo);
		} else {
			return false;
		}
	}

	public function listarPorCodigoCliente($codigo_cliente, $teleconsult = false, $listar_motivo_cancelamento = false) {
		
		$this->ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');

		if($listar_motivo_cancelamento) {
			$this->bindModel(array(
				'belongsTo' => array(
					'Produto' => array('foreignKey' => 'codigo_produto'),
					'MotivoCancelamento' => array('foreignKey' => 'codigo_motivo_cancelamento'),
					),
				)
			);
		} else {
			$this->bindModel(array(				
				'belongsTo' => array(
					'Produto' => array('foreignKey' => 'codigo_produto'),
					),
				)
			);
		}
		$conditions = array(
			$this->name.'.codigo_cliente' => $codigo_cliente, 
			// 'Produto.codigo' => 59 // SOMENTE PRODUTOS DE EXAMES COMPLEMENTARES. {09-12-2016: Linha removida, pois na assinatura não estava sendo exibido todos os produtos, somente exames complementares(Regra desconhecida).}
				//'Produto.ativo' => 1
			);
		if ($teleconsult) {
			$conditions['Produto.descricao LIKE'] = 'TELECONSULT%';
		}

		$linhas = $this->find('all', array('conditions' => $conditions, 'recursive' => 2));
		
		foreach($linhas as $key => $linha){
			$this->ClienteProdutoServico2->bindModel(array(
				'belongsTo' => array(
					'Servico' => array('foreignKey' => 'codigo_servico'),
					'ProdutoServico' => array(
						'foreignKey' => false, 
						'type' => 'INNER',
						'conditions' => 
						'ProdutoServico.codigo_produto = '.$linha['ClienteProduto']['codigo_produto'].' And 
						ProdutoServico.codigo_servico = Servico.codigo  '),
					 //And ProdutoServico.ativo = 1
					)
				)
			);	
			$conditions = array(
				'ClienteProdutoServico2.codigo_cliente_produto' => $linha['ClienteProduto']['codigo'],
					 'Servico.ativo' => 1 //SERVIÇO TEM QUE ESTAR ATIVO
				);

			

			$this->ClienteProdutoServico2->Servico->virtualFields = array(
						'credenciados' => 'SELECT count(*) FROM 
								listas_de_preco_produto_servico LPPS
								    INNER JOIN listas_de_preco_produto LPP 
								        ON(LPP.codigo = LPPS.codigo_lista_de_preco_produto)
								    INNER JOIN listas_de_preco LP
								        ON(LP.codigo = LPP.codigo_lista_de_preco)
								    INNER JOIN clientes_fornecedores CF
								        ON(CF.codigo_fornecedor = LP.codigo_fornecedor AND CF.ativo = 1)
								WHERE LPPS.codigo_servico = ClienteProdutoServico2.codigo_servico AND CF.codigo_cliente = ClienteProdutoServico2.codigo_cliente_pagador'
			);

			$retornos = $this->ClienteProdutoServico2->find('all', compact('conditions'));

			// debug($this->ClienteProdutoServico2->find('sql', compact('conditions')));exit;

			if(count($retornos) > 0){
				$cliente_produto_servico = array();
				foreach($retornos as $posicao => $retorno){				
					$cliente_produto_servico[$posicao] = $retorno['ClienteProdutoServico2'];
					$cliente_produto_servico[$posicao]['Servico'] = $retorno['Servico'];
					$cliente_produto_servico[$posicao]['ProdutoServico'] = $retorno['ProdutoServico'];
				}
				$linhas[$key]['ClienteProdutoServico2'] = $cliente_produto_servico;
			}else{
				unset($linhas[$key]);
			}			
		}
		
		return $linhas;
	}

	public function listarPorCodigoClienteParaCliente($codigo_cliente) {
		
		$this->ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');

		$this->bindModel(array(
			'belongsTo' => array(
				'Produto' => array('foreignKey' => 'codigo_produto'),
				),
			)
		);

		$conditions = array(
			$this->name.'.codigo_cliente' => $codigo_cliente, 
		);

		$linhas = $this->find('all', array('conditions' => $conditions, 'recursive' => 2));
		
		foreach($linhas as $key => $linha){

			$this->ClienteProdutoServico2->bindModel(array(
				'belongsTo' => array(
					'Servico' => array('foreignKey' => 'codigo_servico'),
					'ProdutoServico' => array(
						'foreignKey' => false,
						'type' => 'INNER',
						'conditions' =>
						'ProdutoServico.codigo_produto = '.$linha['ClienteProduto']['codigo_produto'].' AND	ProdutoServico.codigo_servico = Servico.codigo'),
					)
				)
			);	

			$conditions = array(
				'ClienteProdutoServico2.codigo_cliente_produto' => $linha['ClienteProduto']['codigo'],
				'Servico.ativo' => 1 //SERVIÇO TEM QUE ESTAR ATIVO
			);

			$retornos = $this->ClienteProdutoServico2->find('all', compact('conditions'));

			if(count($retornos) > 0){
				$cliente_produto_servico = array();
				foreach($retornos as $posicao => $retorno){
					$cliente_produto_servico[$posicao] = $retorno['ClienteProdutoServico2'];
					$cliente_produto_servico[$posicao]['Servico'] = $retorno['Servico'];
					$cliente_produto_servico[$posicao]['ProdutoServico'] = $retorno['ProdutoServico'];
				}
				$linhas[$key]['ClienteProdutoServico2'] = $cliente_produto_servico;
			}else{
				unset($linhas[$key]);
			}
		}

		return $linhas;
	}

	/**
	 * [listarPorCodigoCliente2 description]
	 * 
	 * metodo para pegar os dados dos servicos quando for matriz e ou o cliente é o pagador
	 * 
	 * @param  [type]  $codigo_cliente         [description]
	 * @param  boolean $servico_not_in         [description]
	 * @param  boolean $codigo_cliente_pagador [description]
	 * @return [type]                          [description]
	 */
	public function listarPorCodigoCliente2($codigo_cliente, $servico_not_in = false, $somente_exame_complementar = false) {
		//instancia a cliente produto servico 2
		$this->ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');

		//pega os produtos
		$this->bindModel(array(
			'belongsTo' => array(
				'Produto' => array('foreignKey' => 'codigo_produto'),
				),
			)
		);
		//condicao com o codigo do cliente
		$conditions = array($this->name.'.codigo_cliente' => $codigo_cliente);
		//busca as linhas do produtos
		$linhas = $this->find('all', array('conditions' => $conditions, 'recursive' => 2));
		
		//verifica se tem este parametros
		$conditionsServico = array();
		if($servico_not_in) {
			$servico_not_in = implode(",", $servico_not_in);
			
			//caso tenha, nao ira mostrar os dados destes servicos
			$conditionsServico = array('ClienteProdutoServico2.codigo_servico NOT IN ('.$servico_not_in.')');
		}//fim servico_not_in

		//varre os produtos
		foreach($linhas as $key => $linha) {

			//monta os jons da cliente produto servico
			$this->ClienteProdutoServico2->bindModel(array(
				'belongsTo' => array(
					'Servico' => array('foreignKey' => 'codigo_servico'),
					'ProdutoServico' => array(
						'foreignKey' => false, 
						'type' => 'INNER',
						'conditions' => 
						'ProdutoServico.codigo_produto = '.$linha['ClienteProduto']['codigo_produto'].' And ProdutoServico.codigo_servico = Servico.codigo'),
						// 'ProdutoServico.codigo_produto = 59 And ProdutoServico.codigo_servico = Servico.codigo'), //exames complementares
					)
				)
			);	

			//filtros
			$conditions = array(
				'ClienteProdutoServico2.codigo_cliente_produto' => $linha['ClienteProduto']['codigo'],
					 'Servico.ativo' => 1, //SERVIÇO TEM QUE ESTAR ATIVO
					 $conditionsServico
				);

			//monta os campos virtuais
			$this->ClienteProdutoServico2->Servico->virtualFields = array(
						'credenciados' => 'SELECT count(*) FROM 
								listas_de_preco_produto_servico LPPS
								    INNER JOIN listas_de_preco_produto LPP 
								        ON(LPP.codigo = LPPS.codigo_lista_de_preco_produto)
								    INNER JOIN listas_de_preco LP
								        ON(LP.codigo = LPP.codigo_lista_de_preco)
								    INNER JOIN clientes_fornecedores CF
								        ON(CF.codigo_fornecedor = LP.codigo_fornecedor AND CF.ativo = 1)
								WHERE LPPS.codigo_servico = ClienteProdutoServico2.codigo_servico AND CF.codigo_cliente = ClienteProdutoServico2.codigo_cliente_pagador'
			);

			//executa a query
			$retornos = $this->ClienteProdutoServico2->find('all', compact('conditions'));

			//monta o retorno
			if($somente_exame_complementar) {
				if(count($retornos) > 0 && $linha['ClienteProduto']['codigo_produto'] == 59){ //exames complementares
					$cliente_produto_servico = array();
					foreach($retornos as $posicao => $retorno){				
						$cliente_produto_servico[$posicao] = $retorno['ClienteProdutoServico2'];
						$cliente_produto_servico[$posicao]['Servico'] = $retorno['Servico'];
						$cliente_produto_servico[$posicao]['ProdutoServico'] = $retorno['ProdutoServico'];
					}
					$linhas[$key]['ClienteProdutoServico2'] = $cliente_produto_servico;
				}else{
					unset($linhas[$key]);
				}
			
			}
			else {
				if(count($retornos) > 0){
					$cliente_produto_servico = array();
					foreach($retornos as $posicao => $retorno){				
						$cliente_produto_servico[$posicao] = $retorno['ClienteProdutoServico2'];
						$cliente_produto_servico[$posicao]['Servico'] = $retorno['Servico'];
						$cliente_produto_servico[$posicao]['ProdutoServico'] = $retorno['ProdutoServico'];
					}
					$linhas[$key]['ClienteProdutoServico2'] = $cliente_produto_servico;
				}else{
					unset($linhas[$key]);
				}
			}
		
		}//fim foreach

		// debug($linhas);exit;
		
		return $linhas;
	}//fim 

	/**
	 * [listarPorCodigoClientePagador description]
	 * 
	 * metodo para pegar os servicos que irá pagar as empresas que te relacionaram
	 * 
	 * @param  [type]  $codigo_cliente         [description]
	 * @param  boolean $servico_not_in         [description]
	 * @param  boolean $codigo_cliente_pagador [description]
	 * @return [type]                          [description]
	 */
	public function listarPorCodigoClientePagador($codigo_cliente_pagador) {
		//instancia a cliente produto servico 2
		$this->ClienteProdutoServico2 = ClassRegistry::init('ClienteProdutoServico2');

		//pega os produtos
		$this->bindModel(array(
			'belongsTo' => array(
				'Produto' => array('foreignKey' => 'codigo_produto'),				
				'ClienteProdutoServico2' => array('foreignKey' => false, 'type' => 'INNER', 'conditions' => 'ClienteProdutoServico2.codigo_cliente_produto = '.$this->name.'.codigo' )
				),
			)
		);
		//condicao com o codigo do cliente

		$fields = array(
			'ClienteProduto.codigo_motivo_bloqueio',
			'ClienteProduto.codigo_produto',
			'ClienteProduto.codigo',
			'ClienteProduto.codigo_cliente',
			'ClienteProduto.codigo_usuario_inclusao',
			'ClienteProduto.qtd_premio_minimo',
			'ClienteProduto.codigo_motivo_bloqueio_bkp',
			'ClienteProduto.codigo_usuario_alteracao',
			'ClienteProduto.codigo_motivo_bloqueio_bkp2',
			'ClienteProduto.codigo_motivo_cancelamento',
			'ClienteProduto.codigo_empresa',
			'ClienteProduto.valor_taxa_corretora',
			'ClienteProduto.valor_taxa_bancaria',
			'ClienteProduto.data_faturamento',
			'ClienteProduto.data_inclusao',
			'ClienteProduto.data_inativacao',
			'ClienteProduto.data_alteracao',
			'ClienteProduto.valor_premio_minimo',
			'ClienteProduto.possui_contrato',
			'ClienteProduto.pendencia_comercial',
			'ClienteProduto.pendencia_financeira',
			'ClienteProduto.pendencia_juridica',
			'ClienteProduto.premio_minimo_por_produto',
			'Produto.codigo',
			'Produto.codigo_usuario_inclusao',
			'Produto.codigo_empresa',
			'Produto.codigo_antigo',
			'Produto.formula_valor_acima_de',
			'Produto.valor_acima_irrf',
			'Produto.data_inclusao',
			'Produto.ativo',
			'Produto.faturamento',
			'Produto.controla_volume',
			'Produto.mensalidade',
			'Produto.percentual_irrf',
			'Produto.percentual_irrf_acima',
			'Produto.descricao',
			'Produto.codigo_naveg',
			'Produto.codigo_ccusto_naveg',
			'Produto.codigo_formula_naveg',
			'Produto.codigo_formula_naveg_sp',
			'Produto.codigo_servico_prefeitura',
			'Produto.codigo_formula_naveg_sp_acima',
			'Produto.codigo_formula_naveg_acima',
			'MotivoBloqueio.codigo',
			'MotivoBloqueio.codigo_usuario_inclusao',
			'MotivoBloqueio.data_inclusao',
			'MotivoBloqueio.descricao'
		);

		$conditions = array(
			$this->name.'.codigo_cliente <>' => $codigo_cliente_pagador,
			'ClienteProdutoServico2.codigo_cliente_pagador' => $codigo_cliente_pagador,
		);
		//busca as linhas do produtos

		$group = array(
			'ClienteProduto.codigo_motivo_bloqueio',
			'ClienteProduto.codigo_produto',
			'ClienteProduto.codigo',
			'ClienteProduto.codigo_cliente',
			'ClienteProduto.codigo_usuario_inclusao',
			'ClienteProduto.qtd_premio_minimo',
			'ClienteProduto.codigo_motivo_bloqueio_bkp',
			'ClienteProduto.codigo_usuario_alteracao',
			'ClienteProduto.codigo_motivo_bloqueio_bkp2',
			'ClienteProduto.codigo_motivo_cancelamento',
			'ClienteProduto.codigo_empresa',
			'ClienteProduto.valor_taxa_corretora',
			'ClienteProduto.valor_taxa_bancaria',
			'ClienteProduto.data_faturamento',
			'ClienteProduto.data_inclusao',
			'ClienteProduto.data_inativacao',
			'ClienteProduto.data_alteracao',
			'ClienteProduto.valor_premio_minimo',
			'ClienteProduto.possui_contrato',
			'ClienteProduto.pendencia_comercial',
			'ClienteProduto.pendencia_financeira',
			'ClienteProduto.pendencia_juridica',
			'ClienteProduto.premio_minimo_por_produto',
			'Produto.codigo',
			'Produto.codigo_usuario_inclusao',
			'Produto.codigo_empresa',
			'Produto.codigo_antigo',
			'Produto.formula_valor_acima_de',
			'Produto.valor_acima_irrf',
			'Produto.data_inclusao',
			'Produto.ativo',
			'Produto.faturamento',
			'Produto.controla_volume',
			'Produto.mensalidade',
			'Produto.percentual_irrf',
			'Produto.percentual_irrf_acima',
			'Produto.descricao',
			'Produto.codigo_naveg',
			'Produto.codigo_ccusto_naveg',
			'Produto.codigo_formula_naveg',
			'Produto.codigo_formula_naveg_sp',
			'Produto.codigo_servico_prefeitura',
			'Produto.codigo_formula_naveg_sp_acima',
			'Produto.codigo_formula_naveg_acima',
			'MotivoBloqueio.codigo',
			'MotivoBloqueio.codigo_usuario_inclusao',
			'MotivoBloqueio.data_inclusao',
			'MotivoBloqueio.descricao'
		);

		$linhas = $this->find('all', array('conditions' => $conditions,'fields' => $fields,'group' => $group));

		// pr($linhas);exit;
	
		//varre os produtos
		foreach($linhas as $key => $linha) {

			//monta os jons da cliente produto servico
			$this->ClienteProdutoServico2->bindModel(array(
				'belongsTo' => array(
					'Servico' => array('foreignKey' => 'codigo_servico'),
					'ProdutoServico' => array(
						'foreignKey' => false, 
						'type' => 'INNER',
						'conditions' => 
						'ProdutoServico.codigo_produto = '.$linha['ClienteProduto']['codigo_produto'].' And 
						ProdutoServico.codigo_servico = Servico.codigo  '),
					)
				)
			);	

			//filtros
			$conditions = array(
				'ClienteProdutoServico2.codigo_cliente_produto' => $linha['ClienteProduto']['codigo'],
				'Servico.ativo' => 1, //SERVIÇO TEM QUE ESTAR ATIVO
				'ClienteProdutoServico2.codigo_cliente_pagador' => $codigo_cliente_pagador,
				);

			//monta os campos virtuais
			$this->ClienteProdutoServico2->Servico->virtualFields = array(
						'credenciados' => 'SELECT count(*) FROM 
								listas_de_preco_produto_servico LPPS
								    INNER JOIN listas_de_preco_produto LPP 
								        ON(LPP.codigo = LPPS.codigo_lista_de_preco_produto)
								    INNER JOIN listas_de_preco LP
								        ON(LP.codigo = LPP.codigo_lista_de_preco)
								    INNER JOIN clientes_fornecedores CF
								        ON(CF.codigo_fornecedor = LP.codigo_fornecedor AND CF.ativo = 1)
								WHERE LPPS.codigo_servico = ClienteProdutoServico2.codigo_servico AND CF.codigo_cliente = ClienteProdutoServico2.codigo_cliente_pagador'
			);

			//executa a query
			$retornos = $this->ClienteProdutoServico2->find('all', compact('conditions'));

			// pr($retornos);exit;

			//monta o retorno
			if(count($retornos) > 0){
				$cliente_produto_servico = array();
				foreach($retornos as $posicao => $retorno){
					$cliente_produto_servico[$posicao] = $retorno['ClienteProdutoServico2'];
					$cliente_produto_servico[$posicao]['Servico'] = $retorno['Servico'];
					$cliente_produto_servico[$posicao]['ProdutoServico'] = $retorno['ProdutoServico'];
				}
				$linhas[$key]['ClienteProdutoServico2'] = $cliente_produto_servico;
			}else{
				unset($linhas[$key]);
			}			
		
		}//fim foreach
		
		return $linhas;
	}//fim listarPorCodigoClientePagador

	public function clienteTemProdutoBuonnySatAtivo($codigo_cliente) {
		$conditions = array('codigo_cliente' => $codigo_cliente, 'codigo_produto' => ClienteProduto::BUONNYSAT, 'codigo_motivo_bloqueio' => 1);
		return $this->find('count', compact('conditions'));
	}

	function produtoClienteAtivo($codigo_cliente, $produto){
		$conditions = array('codigo_cliente' => $codigo_cliente, 'codigo_produto' => $produto, 'codigo_motivo_bloqueio' => 1);
		return $this->find('count', compact('conditions'));
	}
	
	function inativarProdutos($codigo_cliente) {
		$produtos = $this->find('all', array('conditions' => array('codigo_cliente' => $codigo_cliente), 'recursive' => -1));
		foreach ($produtos as &$produto) {
			if ($produto['ClienteProduto']['codigo_motivo_bloqueio'] == 1) {
				$produto['ClienteProduto']['codigo_motivo_bloqueio'] = 8;
				$produto['ClienteProduto']['pendencia_comercial'] = 1;
				$produto['ClienteProduto']['data_inativacao'] = date('d/m/Y H:i:s');
				if (!parent::atualizar($produto, false))
					return false;
			}
		}
		return true;
	}
	
	function estatisticaCancelamento($filtros) {
		$MotivoBloqueio = ClassRegistry::init('MotivoBloqueio');
		$MotivoCancelamento = ClassRegistry::init('MotivoCancelamento');
		$Produto		= ClassRegistry::init('Produto');

		$mes = !empty($filtros['ClienteProduto']['mes']) ? "AND MONTH(data_inativacao) = {$filtros['ClienteProduto']['mes']}":'';
		
		$group[] = $filtros['ClienteProduto']['agrupamento'] == 1 ? 'Produto.descricao': 'MotivoCancelamento.descricao';
		
		$order = $group[0];
		
		$ano_selecionado = $filtros['ClienteProduto']['ano'];
		$ano_anterior = $filtros['ClienteProduto']['ano'] - 1;
		
		$fields = $group;

		$fields[] = "SUM(CASE WHEN YEAR(data_inativacao) = {$ano_anterior} {$mes}
						THEN 1
						ELSE 0
					 END) AS '{$ano_anterior}'";

		$fields[] = "SUM(CASE WHEN YEAR(data_inativacao) = {$ano_selecionado} {$mes}
						THEN 1
						ELSE 0
					 END) AS '{$ano_selecionado}'";
		$joins = array(
			array(
				'table' => "{$MotivoCancelamento->databaseTable}.{$MotivoCancelamento->tableSchema}.{$MotivoCancelamento->useTable}",
				'alias' => 'MotivoCancelamento',
				'type' => 'INNER',
				'conditions' => 'MotivoCancelamento.codigo = ClienteProduto.codigo_motivo_cancelamento'
			),
			array(
				'table' => "{$MotivoBloqueio->databaseTable}.{$MotivoBloqueio->tableSchema}.{$MotivoBloqueio->useTable}",
				'alias' => 'MotivoBloqueio',
				'type' => 'INNER',
				'conditions' => 'MotivoBloqueio.codigo = ClienteProduto.codigo_motivo_bloqueio'
			),
			array(
				'table' => "{$Produto->databaseTable}.{$Produto->tableSchema}.{$Produto->useTable}",
				'alias' => 'Produto',
				'type' => 'INNER',
				'conditions' => 'Produto.codigo = ClienteProduto.codigo_produto'
			),
		);
		$conditions = $this->converteFiltroEmCondition($filtros['ClienteProduto']);
		$conditions['YEAR(data_inativacao)'] =  array($ano_selecionado, $ano_anterior);
		$conditions['ClienteProduto.codigo_motivo_bloqueio '] = 17; 
		
		$recursive = -1;
		return $this->find('all', compact('fields', 'joins', 'conditions', 'group', 'order', 'recursive'));
	}

	function preparaDadosListagemAnaliticoCancelamento($filtros) {
	    $MotivoBloqueio = ClassRegistry::init('MotivoBloqueio');
		$MotivoCancelamento = ClassRegistry::init('MotivoCancelamento');
		$Produto		= ClassRegistry::init('Produto');
		$Cliente		= ClassRegistry::init('Cliente');

		if(empty($filtros['codigo_produto'])){
			$codigo = $Produto->find('first', array('conditions' => array('descricao LIKE' => $filtros['descricao_produto']."%"), 'fields' => 'codigo'));
			if(!empty($codigo['Produto']['codigo']))
				$filtros['codigo_produto'] = $codigo['Produto']['codigo'];
		}
		if(empty($filtros['codigo_motivo_cancelamento'])){
			$codigo_motivo_cancelamento = $MotivoCancelamento->find('first', array('conditions' => array('descricao LIKE' => $filtros['descricao_produto']."%"), 'fields' => 'codigo'));
			if(!empty($filtros['codigo_motivo_cancelamento']))
				$filtros['codigo_motivo_cancelamento'] = $codigo_motivo_cancelamento['Produto']['codigo'];
		}
		$ano_selecionado = $filtros['ano'];

		$joins = array(
			array(
				'table' => "{$MotivoCancelamento->databaseTable}.{$MotivoCancelamento->tableSchema}.{$MotivoCancelamento->useTable}",
				'alias' => 'MotivoCancelamento',
				'type' => 'INNER',
				'conditions' => 'MotivoCancelamento.codigo = ClienteProduto.codigo_motivo_cancelamento'
			),
			array(
				'table' => "{$MotivoBloqueio->databaseTable}.{$MotivoBloqueio->tableSchema}.{$MotivoBloqueio->useTable}",
				'alias' => 'MotivoBloqueio',
				'type' => 'INNER',
				'conditions' => 'MotivoBloqueio.codigo = ClienteProduto.codigo_motivo_bloqueio'
			),
			array(
				'table' => "{$Produto->databaseTable}.{$Produto->tableSchema}.{$Produto->useTable}",
				'alias' => 'Produto',
				'type' => 'INNER',
				'conditions' => 'Produto.codigo = ClienteProduto.codigo_produto'
			),
			array(
				'table' => "{$Cliente->databaseTable}.{$Cliente->tableSchema}.{$Cliente->useTable}",
				'alias' => 'Cliente',
				'type' => 'INNER',
				'conditions' => 'Cliente.codigo = ClienteProduto.codigo_cliente'
			),
		);
		$conditions = $this->converteFiltroEmCondition($filtros);
		$conditions['YEAR(ClienteProduto.data_inativacao)'] =  $ano_selecionado;
		$conditions['ClienteProduto.codigo_motivo_bloqueio'] = 17; 
		if(!empty($filtros['codigo_produto']))
			$conditions['ClienteProduto.codigo_produto'] = $filtros['codigo_produto'];
		if(!empty($filtros['codigo_motivo_cancelamento']))
			$conditions['ClienteProduto.codigo_motivo_cancelamento'] = $filtros['codigo_motivo_cancelamento']; 
		$fields = array(
			'MotivoCancelamento.descricao',
			'Produto.descricao',
			'ClienteProduto.codigo_cliente',
			'Cliente.razao_social'
			);
		return $this->set(compact('fields', 'conditions', 'joins'));
	}

	function queryPremioMinimoPorCliente($filtros) {
		$this->bindModel(array('belongsTo' => array('Cliente' => array('foreignKey' => 'codigo_cliente', 'type' => 'INNER'))));
		$conditions = array(
			$this->name.'.codigo_produto' => Produto::BUONNYSAT,
			$this->name.'.valor_premio_minimo >' => 0,
			$this->name.'.data_inclusao <=' => AppModel::dateToDbDate($filtros['data_final']),
			array(
				'OR' => array(
					"{$this->name}.codigo_motivo_bloqueio" => 1,
					"{$this->name}.data_inativacao >=" => AppModel::dateToDbDate($filtros['data_inicial']),
				)
			),
		);
		$data_inicial = AppModel::dateToDbDate($filtros['data_inicial']);
		$dias_do_mes = "DATEPART(DAY, DATEADD(s,-1,DATEADD(mm, DATEDIFF(m,0,'{$data_inicial}')+1,0)))";
		$dias_utilizados = "(CASE WHEN ClienteProduto.codigo_motivo_bloqueio = 1 THEN {$dias_do_mes} ELSE DATEPART(DAY, ClienteProduto.data_inativacao) END)";
		$valor_proporcional = "CONVERT(DECIMAL(14,2), ({$this->name}.valor_premio_minimo / {$dias_do_mes} ) * {$dias_utilizados})";
		$fields = array(
			'Cliente.codigo AS cliente_pagador',
			"{$valor_proporcional} AS valor_premio_minimo",
		);
		if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
			$conditions[$this->name.'.codigo_cliente'] = $filtros['codigo_cliente'];
		}
		return $this->find('sql', compact('fields', 'conditions'));
	}

	function queryPrecoFechadoPorCliente($filtros) {
		$this->bindModel(array(
			'belongsTo' => array(
				'Cliente' => array(
					'foreignKey' => 'codigo_cliente', 
					'type' => 'INNER'
				),
			),
			'hasOne' => array(
				'ClienteProdutoServico2' => array(
					'foreignKey' => 'codigo_cliente_produto', 
					'type' => 'INNER',
					'conditions' => array('ClienteProdutoServico2.codigo_servico' => Servico::PRECO_FECHADO),
				),
			),
		));
		$conditions = array(
			$this->name.'.codigo_produto' => Produto::BUONNYSAT,
			'ClienteProduto.data_inclusao <=' => AppModel::dateToDbDate($filtros['data_final']),
			array(
				'OR' => array(
					"{$this->name}.codigo_motivo_bloqueio" => 1,
					"{$this->name}.data_inativacao >=" => AppModel::dateToDbDate($filtros['data_inicial']),
				)
			),
		);
		$data_inicial = AppModel::dateToDbDate($filtros['data_inicial']);
		$dias_do_mes = "DATEPART(DAY, DATEADD(s,-1,DATEADD(mm, DATEDIFF(m,0,'{$data_inicial}')+1,0)))";
		$dias_utilizados = "(CASE WHEN ClienteProduto.codigo_motivo_bloqueio = 1 THEN {$dias_do_mes} ELSE DATEPART(DAY, ClienteProduto.data_inativacao) END)";
		$valor_proporcional = "CONVERT(DECIMAL(14,2), (ClienteProdutoServico2.valor / {$dias_do_mes} ) * {$dias_utilizados})";
		$fields = array(
			'Cliente.codigo AS cliente_pagador',
			"{$valor_proporcional} AS ValDeterminado",
		);
		if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
			$conditions[$this->name.'.codigo_cliente'] = $filtros['codigo_cliente'];
		}
		return $this->find('sql', compact('fields', 'conditions'));
	}

	public function pagadorPorProduto($codigo_cliente_transportador, $codigo_produto, $codigo_cliente_embarcador, $somente_ativo = true) {
		if ($codigo_cliente_embarcador != null) {
			$cliente_pagador = ClassRegistry::init('EmbarcadorTransportador')->pagadorPorProduto($codigo_cliente_transportador, $codigo_produto, $codigo_cliente_embarcador, $somente_ativo);
		}
		if (empty($cliente_pagador)) {
			$cliente_pagador = ClassRegistry::init('MatrizFilial')->pagadorPorProduto($codigo_cliente_transportador, $codigo_produto, $somente_ativo);
		}
		if (empty($cliente_pagador)) {
			$fields = array('codigo_cliente AS codigo_cliente_pagador');
			$conditions = array(
				'codigo_cliente' => $codigo_cliente_transportador,
				'codigo_produto' => $codigo_produto,
			);
			if ($somente_ativo) {
				$conditions['codigo_motivo_bloqueio'] = MotivoBloqueio::MOTIVO_OK;
			}
			$cliente_pagador =  $this->find('first', compact('conditions', 'fields'));
		}
		return (isset($cliente_pagador[0]['codigo_cliente_pagador']) ? $cliente_pagador[0]['codigo_cliente_pagador'] : null);
	}

	public function taxasTeleconsultPorCliente($filtros) {
		$this->Produto = ClassRegistry::init('Produto');
		$fields = array(
			'codigo_cliente AS codigo_cliente', 
			'MAX(valor_premio_minimo) AS valor_premio_minimo',
			'MAX(valor_taxa_bancaria) AS valor_taxa_bancaria',
			'MAX(valor_taxa_corretora) AS valor_taxa_corretora',
		);
		$conditions = array('codigo_produto' => array(1,2,134), 'codigo_motivo_bloqueio' => MotivoBloqueio::MOTIVO_OK);
		if (isset($filtros['codigo_cliente']) && !empty($filtros['codigo_cliente'])) {
			$conditions['codigo_cliente'] = $filtros['codigo_cliente'];
		}
		$group = array('codigo_cliente');
		return $this->find('sql', compact('conditions', 'fields', 'group'));
	}

	function carregarPagadorPorClienteProduto($codigo_cliente_pagador,$codigo_produto){
		$this->bindModel(array('belongsTo' => array(
			'Cliente' => array('foreignKey' => 'codigo_cliente'),
		)));
		
		$conditions = array(
			'ClienteProduto.codigo_cliente' 	=> $codigo_cliente_pagador,
			'ClienteProduto.codigo_produto' 	=> $codigo_produto,
			'ClienteProduto.codigo_motivo_bloqueio'	=> MotivoBloqueio::MOTIVO_OK
		);

		$fields = array('Cliente.*');
		$cliente = $this->find('first', compact('fields', 'conditions'));

		return $cliente;
	}

	function status($codigo_cliente,$codigo_produto){
		$this->bindModel(array('belongsTo' => array(
			'Cliente' => array('foreignKey' => 'codigo_cliente'),
		)));
		
		$conditions = array(
			'ClienteProduto.codigo_cliente' 	=> $codigo_cliente,
			'ClienteProduto.codigo_produto' 	=> $codigo_produto
		);

		$fields = array(
			'ClienteProduto.pendencia_comercial',
			'ClienteProduto.pendencia_financeira',
			'ClienteProduto.pendencia_juridica',
		);
		return $this->find('first',compact('fields','conditions'));
	}


}