<?php
class ItensPedidosController extends AppController {
	
	public $name = 'ItensPedidos';
	var $components = array('Mailer.Scheduler');
	var $uses = array(
		'ItemPedido', 
		'ClienteProdutoDesconto', 
		'Cliente', 
		'Produto', 
		'Servico', 
		'ClientEmpresa', 
		'Pedido', 
		'CondPag',
		'DetalheItemPedidoManual',
		'ListaDePrecoProdutoServico',
		'ListaDePreco',
		'RemessaBancaria'
	);

	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		$this->BAuth->allow(array('incluir_pedido_v2', 'listar_v2', 'editar_v2','get_pedido_produto_servico','excluir_pedido','excluir_item_detalhe','imprime_demonstrativo','__jasperConsultaExamesBaixados','gerar_faturamento_percapita','excluir_pedido_v2', 'lista_clientes_nao_automaticos'));
	}


	function pega_valor_servico( $codigo_lista_de_preco, $codigo_cliente, $codigo_produto, $codigo_lista_de_preco_produto_servico ) {

		$fields = array(
			'Servico.codigo',
			'Servico.descricao',
			'Produto.descricao',
			'Produto.codigo',
			"(SELECT 
				ISNULL(acps2.valor, mcps2.valor) as valor
			FROM grupos_economicos_clientes gec
				INNER JOIN grupos_economicos ge on gec.codigo_grupo_economico = ge.codigo
				LEFT JOIN servico s on 1=1
				LEFT JOIN produto p on 1=1
				LEFT JOIN cliente_produto acp on gec.codigo_cliente = acp.codigo_cliente  and acp.codigo_produto = p.codigo
				LEFT JOIN cliente_produto_servico2 acps2 on acps2.codigo_cliente_produto = acp.codigo and s.codigo = acps2.codigo_servico
				LEFT JOIN cliente_produto mcp on ge.codigo_cliente = mcp.codigo_cliente and mcp.codigo_produto = p.codigo
				LEFT JOIN cliente_produto_servico2 mcps2 on mcps2.codigo_cliente_produto = mcp.codigo and s.codigo = mcps2.codigo_servico
			WHERE ISNULL(gec.codigo_cliente, ge.codigo_cliente) = ".$codigo_cliente." AND s.codigo = Servico.codigo AND p.codigo = Produto.codigo) as valor_venda",
			"(	SELECT cps2.valor AS valor 
				FROM cliente_produto_servico2 cps2
					LEFT JOIN cliente_produto cp ON cps2.codigo_cliente_produto = cp.codigo
				WHERE cps2.codigo_cliente_pagador = " . $codigo_cliente . " 
					AND cp.codigo_produto = Produto.codigo 
					AND cp.codigo_cliente = " . $codigo_cliente . " 
					AND cps2.codigo_servico = Servico.codigo) AS valor_venda2"
        );

        $servicos = $this->Servico->find('first', array(
            'recursive' => -1,
            'joins' => array(
                array(
                    'table' => 'listas_de_preco_produto_servico',
                    'alias' => 'ListaDePrecoProdutoServico',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ListaDePrecoProdutoServico.codigo_servico = Servico.codigo'
                        )
                    ),
                array(
                    'table' => 'listas_de_preco_produto',
                    'alias' => 'ListaDePrecoProduto',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ListaDePrecoProduto.codigo = ListaDePrecoProdutoServico.codigo_lista_de_preco_produto'
                        )
                    ),
                array(
                    'table' => 'listas_de_preco',
                    'alias' => 'ListaDePreco',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ListaDePreco.codigo = ListaDePrecoProduto.codigo_lista_de_preco'
                        )
                    ),
                array(
                    'table' => 'produto',
                    'alias' => 'Produto',
                    'type' => 'INNER',
                    'conditions' => array(
                        'Produto.codigo = ListaDePrecoProduto.codigo_produto'
                        )
                    )
                ),
            'conditions' => array(
                'ListaDePreco.codigo_fornecedor' => NULL,
                'Produto.codigo' => $codigo_produto,
                'ListaDePrecoProdutoServico.codigo' => $codigo_lista_de_preco_produto_servico
                ),
            'fields' => $fields,
            'order' => 'Servico.descricao ASC'
            )
        );

        //seta o valor
        $valor = "";
        
        if(!empty($servicos)) {
        
        	if(!empty($servicos[0]['valor_venda'])) {
        
        		$valor = $servicos[0]['valor_venda'];
        	}
        	else {
        
        		$valor = $servicos[0]['valor_venda2'];
        	}
        }

        if($valor != "") {
        
        	echo number_format($valor,2,",",".");
        }
        else {
        	echo "";
        }
		
		$this->autoRender = false;
	}	

	function listar( $codigo_cliente = null ) {

		$this->pageTitle = 'Pedidos Assinaturas';

        $mes_ano = Comum::anoMes(null, true);
        $this->data['ItemPedido']['mes_pedido'] = isset($this->data['ItemPedido']['mes_pedido']) ? $this->data['ItemPedido']['mes_pedido'] : date('m');
        $this->data['ItemPedido']['ano_pedido'] = isset($this->data['ItemPedido']['ano_pedido']) ? $this->data['ItemPedido']['ano_pedido'] : date('Y');
        $conditions = array(
            'Pedido.mes_referencia' => $this->data['ItemPedido']['mes_pedido'],
            'Pedido.ano_referencia' => $this->data['ItemPedido']['ano_pedido']
        );

		$this->loadModel('Cliente');

		$cliente = null;

		if ( isset($this->data) && !empty($this->data['ItemPedido']['codigo_cliente'])) {
			$codigo_cliente = $this->data['ItemPedido']['codigo_cliente'];
			$cliente = $this->Cliente->carregar($codigo_cliente);

            if( isset($this->data['ItemPedido']['mes_pedido'])) {
                if(!empty($this->data['ItemPedido']['mes_pedido'])){
                    $conditions = array(
                        'Pedido.mes_referencia' => $this->data['ItemPedido']['mes_pedido'],
                        'Pedido.ano_referencia' => $this->data['ItemPedido']['ano_pedido']
                    );
                }
            }

		} elseif( !is_null($codigo_cliente) ) {
			$cliente = $this->Cliente->carregar($codigo_cliente);
		}

		// verifica qual é o tipo da unidade caso seja O = operacional não pode gerar o pedido
		if(isset($cliente['Cliente'])) {
			if($cliente['Cliente']['tipo_unidade'] == 'O' || is_null($cliente['Cliente']['tipo_unidade'])) {
				
				$this->BSession->setFlash(array(MSGT_ERROR, 'Cliente tipo Operacional, só pode gerar pedidos para Clientes Fiscal.'));
				$this->redirect( array( 'action'=>'listar') );

			}
		}

		$pedidos = $this->Pedido->pedidoManualPorCliente($cliente['Cliente']['codigo'], $conditions);
		$meses   = Comum::listMeses();

		$this->set(compact('cliente', 'pedidos', 'meses', 'mes_ano'));
	}

	function listar_v2( $codigo_cliente = null ) {

		$this->pageTitle = 'Pedido';

		$mes_ano = Comum::anoMes(null, true);

		$this->data['ItemPedido']['mes_faturamento'] = isset($this->data['ItemPedido']['mes_faturamento']) ? $this->data['ItemPedido']['mes_faturamento'] : date('m');
		
		$this->data['ItemPedido']['ano_faturamento'] = isset($this->data['ItemPedido']['ano_faturamento']) ? $this->data['ItemPedido']['ano_faturamento'] : date('Y');

		$conditions = array(
			'Pedido.mes_referencia' => $this->data['ItemPedido']['mes_faturamento'],
			'Pedido.ano_referencia' => $this->data['ItemPedido']['ano_faturamento']
		);

		$this->loadModel('Cliente');

		$cliente = null;

		if ( isset($this->data) && !empty($this->data['ItemPedido']['codigo_cliente'])) {

			$codigo_cliente = $this->data['ItemPedido']['codigo_cliente'];
		
			$cliente = $this->Cliente->carregar($codigo_cliente);

			if( isset($this->data['ItemPedido']['mes_faturamento'])) {
		
				if(!empty($this->data['ItemPedido']['mes_faturamento'])){
		
					$conditions = array(
						'Pedido.mes_referencia' => $this->data['ItemPedido']['mes_faturamento'],
						'Pedido.ano_referencia' => $this->data['ItemPedido']['ano_faturamento']
					);
				}
			}		

		} elseif( !is_null($codigo_cliente) ) {
		
			$cliente = $this->Cliente->carregar($codigo_cliente);
		}

		// verifica qual é o tipo da unidade caso seja O = operacional não pode gerar o pedido
		if(isset($cliente['Cliente'])) {			
		
			if($cliente['Cliente']['tipo_unidade'] == 'O' || is_null($cliente['Cliente']['tipo_unidade'])) {
				
				$this->BSession->setFlash(array(MSGT_ERROR, 'Cliente tipo Operacional, só pode gerar pedidos para Clientes Fiscal.'));
				$this->redirect( array( 'action'=>'listar_v2') );
			}
		}
		
		$pedidos = $this->Pedido->pedidoManualPorCliente_v2($cliente['Cliente']['codigo'], $conditions);
		
		$meses   = Comum::listMeses();

		$this->set(compact('cliente', 'pedidos', 'meses', 'mes_ano'));
	}//FINAL FUNCTION listar_v2

	public function incluir_pedido_v2($codigo_cliente)
	{
		// se cliente não existir
		$this->loadModel('Cliente');
		$this->Cliente->id = $codigo_cliente;
		
		if(!$this->Cliente->exists()) {
			$this->Session->setFlash('cliente_nao_encontrado');
			return $this->redirect(array('action' => 'listar_v2', $codigo_cliente));
		}

		//disponibiliza dados do cliente
		$cliente = $this->Cliente->carregar($codigo_cliente);
		
		// verifica qual é o tipo da unidade caso seja O = operacional não pode gerar o pedido
		if(isset($cliente['Cliente'])) {
		
			if($cliente['Cliente']['tipo_unidade'] == 'O' || is_null($cliente['Cliente']['tipo_unidade'])) {
				
				$this->BSession->setFlash(array(MSGT_ERROR, 'Cliente tipo Operacional, só pode gerar pedidos para Clientes Fiscal.'));
				$this->redirect( array( 'action'=>'listar_v2') );

			}
		}

		//$this->Pedido->validate = $this->Pedido->pedidoValidate;
		$this->pageTitle = 'Incluir Pedido'; 

		if($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
		
			//organiza os dados para inserir
			$dados = $this->ItemPedido->ajustaDadosParaSalvamentoRecursivo($this->data, $codigo_cliente);

			//errro caso ocorra
			$errorPedido = false;
			$errorItem = false;
			$errorDetalhe = false;

			//pega somente o pedido
			$pedido_codigo = "";

			if($this->Pedido->incluir($dados["Pedido"])) {
			
				//verifica se o pedido foi gravado
				$pedido_codigo = $this->Pedido->id;

				//seta o codigo do pedido
				foreach($dados["ItemPedido"] as $key => $itens) {
			
					$dados["ItemPedido"][$key]["codigo_pedido"] = $pedido_codigo;

					//inclui o item do pedido
					if($this->ItemPedido->incluir($dados["ItemPedido"][$key])){

						//varre os detalhes e insere
						foreach($itens["DetalheItemPedidoManual"] as $keyDet => $det) {

							//seta o codigo do item
							$dados["ItemPedido"][$key]["DetalheItemPedidoManual"][$keyDet]["codigo_item_pedido"] = $this->ItemPedido->id;

							if(!$this->DetalheItemPedidoManual->incluir($dados["ItemPedido"][$key]["DetalheItemPedidoManual"][$keyDet])) {
			
								$errorDetalhe = true;
							} //fim if incluir detalhes

						}//fim foreach detalhes
					} 
					else {
			
						$errorItem = true;
					}//fim if/else incluir item pedido
				} //fim foreach itens
			} 
			else {	

				//seta que houve erro ao inserir pedido
				$errorPedido = true;

			}//fim if/else incluir pedido

			//verifica se existe erros setados
			if($errorPedido || $errorItem || $errorDetalhe) {
				
				$this->BSession->setFlash('save_error');
			} else {
				
				$this->BSession->setFlash('save_success');
				
				$this->redirect( array( 'action'=>'listar_v2', $codigo_cliente ) );	
			}//fim msg e redirecionamento
			
		} //fim if request

		$this->set(compact('cliente', 'codigo_cliente'));

		// disponibiliza vendedores para a view
		$vendedores = $this->ItemPedido->obtemVendedores();
		
		$this->set(compact('vendedores'));

		// disponibiliza Formas de pagto para a view
		$formas_pagto = $this->CondPag->listar_condicoes(1);
		
		$this->set(compact('formas_pagto'));
	}//FINAL FUNCTION incluir_pedido_v2

	public function editar_v2($codigo_cliente, $codigo_pedido)
	{
		$this->pageTitle = 'Editar Pedido';
		
		// se cliente não existir
		$this->loadModel('Cliente');
		$this->Cliente->id = $codigo_cliente;
		
		if(!$this->Cliente->exists()) {
			$this->Session->setFlash('cliente_nao_encontrado');
			return $this->redirect(array('action' => 'listar_v2', $codigo_cliente));
		}

		if($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
		
			$dados = $this->ItemPedido->ajustaDadosParaSalvamentoRecursivo($this->data, $codigo_cliente);
		
			$this->Pedido->id = $codigo_pedido;
		
			$dados['Pedido']['codigo'] = $codigo_pedido;
		
			if($this->Pedido->atualizarTodos($dados, array('deep' => true))) {
				$this->BSession->setFlash('save_success');
				$this->redirect( array( 'action'=>'listar_v2', $codigo_cliente));
			} else {
		
				$this->BSession->setFlash('save_error');
			}

		} else {
		
			$this->data = $this->ItemPedido->montaDadosParaEditarPedido($codigo_pedido);
		}

		//disponibiliza dados do cliente
		$cliente = $this->Cliente->carregar($codigo_cliente);
		
		$this->set(compact('cliente', 'codigo_cliente'));

		// disponibiliza vendedores para a view
		$vendedores = $this->ItemPedido->obtemVendedores();
		
		$this->set(compact('vendedores'));

		// disponibiliza Formas de pagto para a view
		$formas_pagto = $this->CondPag->listar_condicoes(1);
		
		$this->set(compact('formas_pagto'));

		$this->set(compact('codigo_pedido'));
	}//FINAL FUNCTION editar_v2

	function incluir_pedido($codigo_cliente)
	{
		$this->pageTitle = 'Incluir Pedido'; 
		
		$cliente = $this->Cliente->carregar($codigo_cliente);

		// verifica qual é o tipo da unidade caso seja O = operacional não pode gerar o pedido
		if(isset($cliente['Cliente'])) {
		
			if($cliente['Cliente']['tipo_unidade'] == 'O' || is_null($cliente['Cliente']['tipo_unidade'])) {
				
				$this->BSession->setFlash(array(MSGT_ERROR, 'Cliente tipo Operacional, só pode gerar pedidos para Clientes Fiscal.'));
				$this->redirect( array( 'action'=>'listar') );

			}
		}


		if( isset($this->data) && !empty($this->data) ){

			$qdtParcela   = $this->data['ItemPedido']['quantidade_parcela'];
			$ano 		  = (int) $this->data['ItemPedido']['ano'];
			$mes 		  = (int) $this->data['ItemPedido']['mes'];

			$this->data['ItemPedido']['valor_total'] = str_replace('.', '', $this->data['ItemPedido']['valor_total']);
			$this->data['ItemPedido']['valor_total'] = str_replace(',', '.', $this->data['ItemPedido']['valor_total']);            

			$this->data['ItemPedido']['valor'] = $this->data['ItemPedido']['valor_total'] ;
			$this->data['ItemPedido']['valor_total'] = $this->data['ItemPedido']['quantidade'] * $this->data['ItemPedido']['valor_total'];

			$produto_codigo_servico = $this->Produto->carregar($this->data['ItemPedido']['codigo_produto']);
			$this->data['ItemPedido']['codigo_servico'] = $produto_codigo_servico['Produto']['codigo_servico_prefeitura'];

			try{
				$this->ItemPedido->query('begin transaction');
				for( $i = 0; $i < $qdtParcela; $i++ ){
					if( $mes > 12 ){ $mes = 1; $ano++; }

					$codigo_pedido = $this->cadastraPedido( $mes, $ano );
					if( !$codigo_pedido )
						throw new Exception("Pedido");

					$codigo_item_pedido = $this->cadastraProduto( $codigo_pedido );
					if( !$codigo_item_pedido )
						throw new Exception("ItemPedido");

					if( !$this->cadastraDetalheItemPedidoManual( $codigo_item_pedido ) )
						throw new Exception("Detalhe");

					$mes++;
				}
				$this->ItemPedido->commit();
				$this->BSession->setFlash('save_success');
				$this->redirect( array( 'action'=>'listar', $codigo_cliente ) );
			}catch( Exception $e ){        		
				$this->ItemPedido->rollback();
				$this->BSession->setFlash('save_error');
				$cliente_codigo = $this->data['ItemPedido']['codigo_cliente_pagador'];
			}
			$this->set(compact('cliente'));
		}

		$produtos = array();

		$cliente = $this->Cliente->carregar( $codigo_cliente );
		$ListaDePreco = ClassRegistry::init('ListaDePreco');

		$condicoes_pagamento = $this->CondPag->listar_condicoes(1);

		$lista_de_preco = $ListaDePreco->porFornecedor( null );
		$lista_de_preco_codigo = $lista_de_preco['ListaDePreco']['codigo'];
		$lista_de_preco = $lista_de_preco['ListaDePreco']['descricao'];        

		foreach($this->Produto->listaProduto( null, $lista_de_preco_codigo, false ) as $key => $value) {
			$produtos[$value['Produto']['codigo']] = $value['Produto']['descricao'];            
		}                

		foreach($condicoes_pagamento as $key => $condicao) $condicoes_pagamento[$key] = strtoupper($condicao);

		$authUsuario = $this->BAuth->user();
		$codigo_usuario_inclusao = $authUsuario['Usuario']['codigo'];
		$this->data['ItemPedido']['mes'] = date('m');
		$meses   = Comum::listMeses();
		$anos  = array(date('Y') => date('Y'), (date('Y')-1) => (date('Y')-1), (date('Y')+1) => (date('Y')+1));
		$this->set( compact('produtos', 'codigo_usuario_inclusao', 'cliente', 'codigo_cliente', 'lista_de_preco','meses','condicoes_pagamento','lista_de_preco_codigo', 'anos') );
	}//FINAL FUNCTION incluir_pedido

	private function cadastraPedido( $mes, $ano ) {   	

		$dataPedido  = array();
		$dataPedido['codigo_cliente_pagador']  	 = $this->data['ItemPedido']['codigo_cliente_pagador'];
		$dataPedido['manual']                  	 = $this->data['ItemPedido']['manual'];
		$dataPedido['codigo_usuario_inclusao'] 	 = $this->data['ItemPedido']['codigo_usuario_inclusao'];
		$dataPedido['codigo_condicao_pagamento'] = $this->data['ItemPedido']['codigo_condicao_pagamento'];        
		$dataPedido['mes_referencia']            = $mes;
		$dataPedido['ano_referencia']            = $ano;

		if( $this->Pedido->incluir($dataPedido) )
			return $this->Pedido->id;
		else
			return false;        
	}//FINAL FUNCTION cadastraPedido

	private function cadastraProduto( $codigo_pedido ) {

		$dataProduto = array();

		$dataProduto['codigo_pedido']            = $codigo_pedido;
		$dataProduto['codigo_produto']           = $this->data['ItemPedido']['codigo_produto'];
		$dataProduto['quantidade']               = 1;        		
		$dataProduto['valor_total']              = $this->data['ItemPedido']['valor_total'];
		$dataProduto['codigo_usuario_inclusao']  = $this->data['ItemPedido']['codigo_usuario_inclusao'];

		if( $this->ItemPedido->incluir($dataProduto) )
			return $this->ItemPedido->id;
		else
			return false;
	}//FINAL FUNCTION cadastraProduto

	private function cadastraDetalheItemPedidoManual($codigo_item_pedido){

		$lista_preco = $this->ListaDePrecoProdutoServico->carregar($this->data['ItemPedido']['servico_codigo']);

		$dataDetalhe = array();
		$dataDetalhe['codigo_item_pedido'] = $codigo_item_pedido;

		$dataDetalhe['valor'] 			   = $this->data['ItemPedido']['valor'];
		$dataDetalhe['quantidade'] 	       = $this->data['ItemPedido']['quantidade'];
		$dataDetalhe['codigo_servico']     = $lista_preco['ListaDePrecoProdutoServico']['codigo_servico'];

		if( $this->DetalheItemPedidoManual->incluir($dataDetalhe) )
			return true;
		else
			return false;
	}//FINAL FUNCTION cadastraDetalheItemPedidoManual

	public function excluir_pedido( $codigo_cliente=null, $codigo_pedido=null ){

		//pega os pedidos verifica se foi integrado e se tem remessa
		$joinPedido = array(
			array(
				'table' => "{$this->RemessaBancaria->databaseTable}.{$this->RemessaBancaria->tableSchema}.{$this->RemessaBancaria->useTable}",
				'alias' => 'RemessaBancaria',
				'type' => 'LEFT',
				'conditions' => 'Pedido.codigo = RemessaBancaria.codigo_pedido',
			),
		);
		$pedido = $this->Pedido->find('first', array('fields' => array('Pedido.codigo','RemessaBancaria.codigo'),'joins'=>$joinPedido,'conditions' =>array('Pedido.codigo' => $codigo_pedido, 'Pedido.data_integracao' => NULL)));

		if(empty($pedido)) {
			$this->BSession->setFlash('Pedido já integrado, não é possivel deletar o registro!');
		} else {

			try{

				$this->Pedido->query('begin transaction');
				
				if(!empty($pedido['RemessaBancaria']['codigo'])) {
					
					//seta os valores
					$remessa = $this->RemessaBancaria->find('first', array('conditions' => array('codigo' => $pedido['RemessaBancaria']['codigo'])));

					//codigo retorno da tabela
					if($remessa['RemessaBancaria']['codigo_banco'] == '341') {
						$codigo_remessa_retorno = 2;
					} else if($remessa['RemessaBancaria']['codigo_banco'] == '033') {
						$codigo_remessa_retorno = 94;
					}
					//seta os valores anteriores
					$remessa['RemessaBancaria']['codigo_remessa_status'] = $codigo_remessa_retorno;
					$remessa['RemessaBancaria']['codigo_remessa_status'] = '1';
					$remessa['RemessaBancaria']['codigo_pedido'] = '';

					//atualiza os valores
					if(!$this->RemessaBancaria->atualizar($remessa)) {
						throw new Exception("Erro: Remessa");
					}
				}//fim verificacao da remessa

				$itens_pedido = $this->ItemPedido->find('all',array('conditions'=>array('codigo_pedido'=>$codigo_pedido)));

				//varre os itens cadastrados
				if(!empty($itens_pedido)) {
					foreach($itens_pedido as $item) {
						//deleta os detalhes dos itens
						if(!$this->DetalheItemPedidoManual->deleteAll(array('DetalheItemPedidoManual.codigo_item_pedido' => $item['ItemPedido']['codigo']))) {
							throw new Exception("Erro ao deletar detalhes");
						}
					} //fim foreach
				}

				//deleta os itens
				if(!$this->ItemPedido->deleteAll(array('ItemPedido.codigo_pedido' => $codigo_pedido))) {
					throw new Exception("Erro ao deletar Itens");
				}

				//deleta o pedido
				if(!$this->Pedido->excluir($codigo_pedido)){
					throw new Exception("Erroa o deletar Pedido");
				}

				$this->Pedido->commit();
				$this->BSession->setFlash('delete_success');

				// echo json_encode(array("msg"=>"ok"));
				// exit;

			}catch( Exception $e ){
				// pr($e->getMessage()); exit;
				
				$this->log($e->getMessage(),'debug');

				$this->Pedido->rollback();
				$this->BSession->setFlash('delete_error');

				// return -1;
			}

		}//fim pedido data integracao


		$this->autoRender = false;
		$this->redirect( array( 'action'=>'listar', $codigo_cliente ) );
	}//FINAL FUNCTION excluir_pedido

	public function excluir_pedido_v2( $codigo_cliente=null, $codigo_pedido=null ){

		try{

			$this->ItemPedido->query('begin transaction');

			$itens_pedido = $this->ItemPedido->find('all',array('conditions'=>array('codigo_pedido'=>$codigo_pedido)));

			//varre os itens cadastrados
			if(!empty($itens_pedido)) {
				foreach($itens_pedido as $item) {
					//deleta os detalhes dos itens
					if(!$this->DetalheItemPedidoManual->deleteAll(array('DetalheItemPedidoManual.codigo_item_pedido' => $item['ItemPedido']['codigo']))) {
						throw new Exception("");
					}
				} //fim foreach
			}

			//deleta os itens
			if(!$this->ItemPedido->deleteAll(array('ItemPedido.codigo_pedido' => $codigo_pedido))) {
				throw new Exception("");
			}

			//deleta o pedido
			if(!$this->Pedido->excluir($codigo_pedido)){
				throw new Exception("");
			}

			$this->ItemPedido->commit();
			// $this->BSession->setFlash('delete_success');

			echo json_encode(array("msg"=>"ok"));
			exit;

		}catch( Exception $e ){

			$this->ItemPedido->rollback();
			// $this->BSession->setFlash('delete_error');

			return -1;
		}	

		// $this->autoRender = false;
		// $this->redirect( array( 'action'=>'listar', $codigo_cliente ) );	
	}//FINAL FUNCTION excluir_pedidoV2

	/**
	 * Metodo para excluir o item e o detalhe
	 */ 
	public function excluir_item_detalhe($codigo_item, $codigo_detalhe){
		try{
			$this->ItemPedido->query('begin transaction');

			$detalhesItemPedidoManual = $this->DetalheItemPedidoManual->find('first',array('fields' => array('codigo_item_pedido','COUNT(codigo_item_pedido) AS TOTAL'),
																						'conditions'=>array('codigo_item_pedido'=>$codigo_item),
																						'group' => array('codigo_item_pedido')
																						));

			//delete o detalhe pedido manual
			if(!$this->DetalheItemPedidoManual->delete($codigo_detalhe)) {
				throw new Exception("");
			}

			if($detalhesItemPedidoManual[0]['TOTAL'] == '1') {
				//deleta os itens
				if(!$this->ItemPedido->delete($codigo_item)) {
					throw new Exception("");
				}
			}

			$this->ItemPedido->commit();

			echo json_encode(array("msg"=>"ok"));
			exit;

		}catch( Exception $e ){

			$this->ItemPedido->rollback();
			return -1;
		}
	} //fim excluir_item_detalhe

	public function editar( $codigo_cliente, $codigo_item_pedido ) {

		$this->pageTitle = 'Editar Pedido';

		if( isset($this->data) && !empty($this->data) ) {

			$this->data['ItemPedido']['valor'] = str_replace('.', '', $this->data['ItemPedido']['valor']);
			$this->data['ItemPedido']['valor'] = str_replace(',', '.', $this->data['ItemPedido']['valor']);
			$this->data['ItemPedido']['valor_total'] = $this->data['ItemPedido']['quantidade'] * $this->data['ItemPedido']['valor'];

			try{
				$this->ItemPedido->query('begin transaction');

				$dataItemPedido['ItemPedido']['codigo'] = $this->data['ItemPedido']['codigo'];
				$dataItemPedido['ItemPedido']['quantidade'] = 1;        		
				$dataItemPedido['ItemPedido']['valor_total'] = $this->data['ItemPedido']['valor_total'];


				if( !$this->ItemPedido->atualizar($dataItemPedido)){
					throw new Exception("ItemPedido");
				}
				else{
					$dataDetalhePedido['DetalheItemPedidoManual']['codigo'] = $this->data['DetalheItemPedidoManual']['codigo'];
					$dataDetalhePedido['DetalheItemPedidoManual']['codigo_item_pedido'] = $codigo_item_pedido;
					$dataDetalhePedido['DetalheItemPedidoManual']['valor'] = $this->data['ItemPedido']['valor'];
					$dataDetalhePedido['DetalheItemPedidoManual']['quantidade'] = $this->data['ItemPedido']['quantidade'];
					if( !$this->DetalheItemPedidoManual->atualizar($dataDetalhePedido)){
						throw new Exception("ItemPedido");
					}
				}
				$this->ItemPedido->commit();
				$this->BSession->setFlash('save_success');
				$this->redirect( array( 'action'=>'listar', $codigo_cliente ) );  
			}
			catch( Exception $e ){        		
				$this->ItemPedido->rollback();
				$this->BSession->setFlash('save_error');
			} 
		} 

		$dados	  		  = $this->ItemPedido->carregar( $codigo_item_pedido );
		$produto		  = $this->Produto->carregar($dados['ItemPedido']['codigo_produto']);
		$descricaoProduto = $produto['Produto']['descricao'];	

		$lista_de_preco = $this->ListaDePreco->porFornecedor( null );
		$lista_de_preco_descricao = $lista_de_preco['ListaDePreco']['descricao'];

		$detalhe = $this->DetalheItemPedidoManual->carregar($dados['ItemPedido']['codigo']);
		$servico  = $this->Servico->carregar($detalhe['DetalheItemPedidoManual']['codigo_servico']);
		$descricaoServicoPedido = $servico['Servico']['descricao'];

		$this->set( compact('dados','descricaoProduto', 'lista_de_preco_descricao','descricaoServicoPedido','detalhe') );
	}//FINAL FUNCTION editar

	public function lista_de_preco($lista_de_preco,$produto){
		$servicos = $this->ListaDePrecoProdutoServico->servicosPorProdutoEListaDePreco($lista_de_preco,$produto);	
		$this->set(compact('servicos'));
	}//FINAL FUNCTION lista_de_preco

	public function integracao() {

		$this->pageTitle = 'Integração';
			
		$this->loadModel('Pedido');

		$base_periodo = strtotime('-1 month', strtotime(Date('Y-m-01')));
		
		$filtros = array('data_inicial' => Date('01/m/Y', $base_periodo), 'data_final' => Date('t/m/Y', $base_periodo));

		$this->set('anos', Comum::listAnos());
		
		$this->set('meses', Comum::listMeses());

		$isPost = $this->RequestHandler->isPost();
		
		if ($isPost && !empty($this->data)) {

			$codigo_cliente = null;
			$aguardar_liberacao = 1;

			if(isset($this->data['Cliente']) && isset($this->data['Cliente']['codigo']) && !empty($this->data['Cliente']['codigo'])){
				$codigo_cliente = $this->data['Cliente']['codigo'];
				$aguardar_liberacao = null;
			}

			// comandos de processamento
			$comando_carregar = (strtolower($this->data['Submit']['type']) == 'carregar');
			$comando_carregar_clientes_selecionados = (strtolower($this->data['Submit']['type']) == 'carregar clientes selecionados');
			$comando_reverter_clientes_selecionados = (strtolower($this->data['Submit']['type']) == 'reverter clientes selecionados');
			$comando_reverter = (strtolower($this->data['Submit']['type']) == 'reverter');
			$comando_integrar = (strtolower($this->data['Submit']['type']) == 'integrar');
			$comando_integrar_pedidos_manuais = (strtolower($this->data['Submit']['type']) == 'integrar pedidos manuais');
			
			if($comando_carregar_clientes_selecionados && empty($codigo_cliente)){
				$this->BSession->setFlash(array(MSGT_ERROR,'Código Cliente não encontrado'));
				return;
			}

			if($comando_reverter_clientes_selecionados && empty($codigo_cliente)){
				$this->BSession->setFlash(array(MSGT_ERROR,'Código Cliente não encontrado'));
				return;
			}

			if ($comando_carregar || $comando_carregar_clientes_selecionados) {

				ini_set('max_execution_time', 0);
				set_time_limit(0);

				$options = array(
					'from' => 'faturamento@rhhealth.com.br',
					'cc' => null,
					'sent' => null,
					'to' => 'tid@ithealth.com.br',
					'subject' => 'Carga de Faturamento',
					'liberar_envio_em' => null
				);

				// processar integração percapita
				if($carregar_integracao_percapita = $this->Pedido->carregarIntegracaoPercapita(null,null,$aguardar_liberacao, $codigo_cliente)){
					$this->Scheduler->schedule('Executado carga faturamento - Percapita', $options);
				}

				// processar integração exames complementares
				if($carregar_integracao_exames_complementares = $this->Pedido->carregarIntegracaoExamesComplementares(null,null,$aguardar_liberacao, $codigo_cliente)){
	                $this->Scheduler->schedule('Executado carga faturamento - Exames Complementares', $options);
				}
				
				// processar integração pacote mensal
				if ($carregar_integracao_pacote_mensal = $this->Pedido->carregarIntegracaoPacoteMensal($base_periodo, $filtros,$aguardar_liberacao, $codigo_cliente)) { 
	                $this->Scheduler->schedule('Executado carga faturamento - Pacote Mensal', $options);
				}

				// se qualquer um dos carregamentos tiver uma condição negativa 
				if(!$carregar_integracao_percapita 
						|| !$carregar_integracao_exames_complementares 
						|| !$carregar_integracao_pacote_mensal){

					$filtros = array('mes_faturamento' => Date('m'), 'ano_faturamento' => Date('Y'));
					
					$data['Integfat'] = $filtros;

					$data['Integfat']['somente_problemas'] = true;

					$this->Filtros->controla_sessao($data, 'Integfat');

					$error_msg = "";

					//tratamento de erros para o usuario;
					
					if(isset($_SESSION['erro_percapita'])) {
						
						$error_msg = $_SESSION['erro_percapita'];
						unset($_SESSION['erro_percapita']);
					}

					//tratamento de erros para o usuario;
					if(isset($_SESSION['erro_exame_complementar'])) {
						
						if(!empty($error_msg)) {
							$error_msg .= "<br>";
						}

						$error_msg .= $_SESSION['erro_exame_complementar'];						
						
						unset($_SESSION['erro_exame_complementar']);
					}

					if(!empty($error_msg)) {
		
						$this->BSession->setFlash(array(MSGT_ERROR,$error_msg));
					} else {
						$this->BSession->setFlash('save_error');
					}
				}
				// se qualquer condição 	
				if($carregar_integracao_percapita 
						|| $carregar_integracao_exames_complementares 
						|| $carregar_integracao_pacote_mensal){

					$this->BSession->setFlash('save_success');
							
				}

				// $this->data['Submit']['type'] = null;

				return;

			}elseif ($comando_reverter || $comando_reverter_clientes_selecionados) {
				
				ini_set('max_execution_time', 0);
				set_time_limit(0);

				if($this->Pedido->reverterCarregamentoIntegracao($codigo_cliente)){
					
					$options = array(
	                    'from' => 'faturamento@rhhealth.com.br',
	                    'cc' => null,
	                    'sent' => null,
	                    'to' => 'williansbuonny@gmail.com, coreexpress@gmail.com',
	                    'subject' => 'Reversão de Carga de Faturamento',
	                    'liberar_envio_em' => null
	                );
	                
	                $this->Scheduler->schedule('Executado reversão carga faturamento', $options);
					
					$this->BSession->setFlash('save_success');
				} else {
					
					$this->BSession->setFlash('save_error');
				};

				return;

			}elseif ($comando_integrar) {
				
				ini_set('max_execution_time', 0);
				set_time_limit(0);

				$this->loadModel('Integfat');
				
				$parametros = array();
				
				if($this->Integfat->importar($parametros)) {
					
					$options = array(
	                    'from' => 'faturamento@rhhealth.com.br',
	                    'cc' => null,
	                    'sent' => null,
	                    'to' => 'williansbuonny@gmail.com, coreexpress@gmail.com',
	                    'subject' => "Integração Pedidos",
	                    'liberar_envio_em' => null
	                );
	                
	                $this->Scheduler->schedule("Integração pedidos", $options);
					
					$this->BSession->setFlash('save_success');
				} else {
					
					$this->BSession->setFlash('save_error');
				};

				return;

			}elseif ($comando_integrar_pedidos_manuais) {
				
				$this->loadModel('Integfat');

				$parametros = array(
					'mes_referencia_manual' => $this->data['ItemPedido']['mes'],
					'ano_referencia_manual' => $this->data['ItemPedido']['ano'],
					'manual' => 1,
					'codigo_empresa' => $_SESSION['Auth']['Usuario']['codigo_empresa']
				);

				if($this->Integfat->importar($parametros)) {
					
					$options = array(
	                    'from' => 'faturamento@rhhealth.com.br',
	                    'cc' => null,
	                    'sent' => null,
	                    'to' => 'williansbuonny@gmail.com, coreexpress@gmail.com',
						'subject' => '[RHHealth] - Integração Pedidos Manuais',
						'liberar_envio_em' => null
						);
					
					$this->Scheduler->schedule('Integração pedidos manuais', $options);
					
					$this->BSession->setFlash('integrate_success');
				}else {
					
					//implementado para deixar a mensagem de erro personalizada na integração
					$msgintegracao = "";
					
					//verifica se existe a sessao
					if(isset($_SESSION['integfat_erro'])) {
						
						//seta a mensagem
						$msgintegracao = $_SESSION['integfat_erro'];
						
						//elimina a mensagem
						unset($_SESSION['integfat_erro']);
					}
					
					$this->BSession->setFlash('integrate_error' . $msgintegracao);
				}

				return;
			}
		} //fim this->data


	} //fim metodo integracao

	/**
	 * Método para gerar o demonstrativo de faturamento
	 * 
	 * @param  [int] codigo_cliente_pagador [codigo do cliente do pagador]
	 * @param  [date] data_inicial [passa a data incial para gerar o demonstrativo]
	 * @param  [date] data_fim [passa a data fim para gerar o demonstrativo]
	 * @return [pdf] [relatorio pdf com o demonstrativo]
	 */
	public function imprime_demonstrativo($codigo_cliente_pagador, $data_inicial = null, $data_fim = null) 
	{
		//verifica se tem dados passados
		if(is_null($data_inicial) && is_null($data_fim)) {
			
			//pega o mes passado
			$base_periodo = strtotime('-1 month', strtotime(Date('Y-m-01')));
			
			//seta a data de inicio/fim
			
			$data_inicial = Date('Ym01', $base_periodo);
			
			$data_fim = Date('Ymt', $base_periodo);
		}//fim is null dados

		$this->__jasperConsultaExamesBaixados($codigo_cliente_pagador, $data_inicial, $data_fim);
	} //fim imprime

	/**
	 * metodo para chamar o jasper
	 */ 
	private function __jasperConsultaExamesBaixados( $codigo_cliente_pagador, $data_inicial, $data_fim) 
	{
		$this->autoRender = false;
		
		require_once APP . 'vendors' . DS . 'buonny' . DS . 'RelatorioWebService.php';
		
		$RelatorioWebService = new RelatorioWebService();
		
		$parametros = array(
			'CODIGO_CLIENTE_PAGADOR' => $codigo_cliente_pagador,
			'DATA_INICIAL' => $data_inicial,
			'DATA_FIM' => $data_fim,
			);

			header(sprintf('Content-Disposition: attachment; filename="%s"', 'demonstrativo_exame_complementar.pdf' ));

		//condição implementada com os novos relatorios dos exames complementares onde foi adicionado o codigo_cliente_utilizador
		$anomes = substr($dados[2],0,6);
		
		if($anomes <= '201804') {
			
			$url = $RelatorioWebService->executarRelatorio( '/reports/RHHealth/demostrativo_exame_complementar', $parametros, 'pdf');
		}
		else {
			
			$url = $RelatorioWebService->executarRelatorio( '/reports/RHHealth/demostrativo_exame_complementar_1', $parametros, 'pdf');
		}

		header('Pragma: no-cache');
		
		header('Content-type: application/pdf');
		
		echo $url; 
		exit;
	}//fim jasper
	
	/**
	 * Metodo para enviar para o naveg os pedidos que foram gerados pelo sistema.
	 */ 
	public function pedidos_nao_integrados () 
	{

		$this->pageTitle = 'Pedidos não integrados';
		
		$mes_nao_integrados = Comum::anoMes(null, true);

		$this->data['ItemPedidoNaoIntegrados']['mes_faturamento'] = isset($this->data['ItemPedidoNaoIntegrados']['mes_faturamento']) ? $this->data['ItemPedidoNaoIntegrados']['mes_faturamento'] : date('m', strtotime('-1 months', strtotime(date('Y-m-d'))));

		$this->data['ItemPedidoNaoIntegrados']['ano_faturamento'] = isset($this->data['ItemPedidoNaoIntegrados']['ano_faturamento']) ? $this->data['ItemPedidoNaoIntegrados']['ano_faturamento'] : date('Y');

		$this->set(compact('mes_nao_integrados'));

		if($this->RequestHandler->isPost()) {
			
			$filtrado = true;
			
			$data_integracao = '01/'.$this->data['ItemPedidoNaoIntegrados']['mes_faturamento'].'/'.$this->data['ItemPedidoNaoIntegrados']['ano_faturamento'];
			
			if( Comum::isDate($data_integracao) ){

				if (isset($this->data['Submit']['type']) && strtolower($this->data['Submit']['type']) == 'integrar') {
					
					$pedidos = isset($_POST['codigo_pedido']) ? $_POST['codigo_pedido'] : null;
					
					if($pedidos) {
							
						$codigos_pedidos = array('codigo_pedido' => $pedidos);

						$this->loadModel('Integfat');
						
						if($this->Integfat->importar($codigos_pedidos)) {
							
							$this->BSession->setFlash('save_success');
						} else {

							//implementado para deixar a mensagem de erro personalizada na integração
							$msgintegracao = "";
							
							//verifica se existe a sessao
							if(isset($_SESSION['integfat_erro'])) {
								
								//seta a mensagem
								$msgintegracao = $_SESSION['integfat_erro'];
								
								//elimina a mensagem
								unset($_SESSION['integfat_erro']);
							}

							$this->BSession->setFlash(array(MSGT_ERROR,'Erro ao reintegrar pedido.'.$msgintegracao));
						}

					}else {

						$this->BSession->setFlash(array(MSGT_ERROR,'Selecione pelo menos um pedido.'));
					}
				}

				$conditions = array(
					'Pedido.mes_referencia' => $this->data['ItemPedidoNaoIntegrados']['mes_faturamento'],
					'Pedido.ano_referencia' => $this->data['ItemPedidoNaoIntegrados']['ano_faturamento'],
					'Pedido.data_integracao'=> NULL
				);
				$this->ItemPedido->bindModelTaxaAdm();

				$fields = array('Cliente.codigo','Cliente.razao_social', 'Pedido.mes_referencia', 'Pedido.ano_referencia', 'ItemPedido.codigo_pedido', 'SUM(ItemPedido.valor_total) as valor_pedido');
				
				$group = array('Cliente.codigo','Cliente.razao_social', 'Pedido.mes_referencia', 'Pedido.ano_referencia', 'ItemPedido.codigo_pedido');
				
				$lista_nao_integrados = $this->ItemPedido->find('all', compact('conditions', 'fields', 'group'));
				
				$this->set(compact('lista_nao_integrados', 'lista_detalhe_itens'));

			}//fim if comum is date

			$this->set(compact('filtrado'));

		} //fim request

	}//fim pedidos_nao_integrados

	/**
	 * Metodo para pegar os produtos e serviços do pedido
	 */ 
	public function get_pedido_produto_servico($codigo_pedido)
	{

		//campos para a query de apresentacao
		$fieldsItem= array(
			'Produto.codigo as produto_codigo',
			'Produto.descricao AS produto',
			'Servico.codigo AS servico_codigo',
			'Servico.descricao AS servico',
			'DetalheItemPedidoManual.quantidade AS quantidade',
			'RHHealth.publico.ufn_formata_moeda(DetalheItemPedidoManual.valor,2) AS valor',
		);

		//joins para pegar os dados dos produtos e servicos
		$joinItem = array(
			array(
				'table' => "{$this->Produto->databaseTable}.{$this->Produto->tableSchema}.{$this->Produto->useTable}",
				'alias' => 'Produto',
				'conditions' => 'Produto.codigo = ItemPedido.codigo_produto',
				'type' => 'INNER',
			),
			array(
				'table' => "{$this->DetalheItemPedidoManual->databaseTable}.{$this->DetalheItemPedidoManual->tableSchema}.{$this->DetalheItemPedidoManual->useTable}",
				'alias' => 'DetalheItemPedidoManual',
				'conditions' => 'DetalheItemPedidoManual.codigo_item_pedido = ItemPedido.codigo',
				'type' => 'INNER',
			),
			array(
				'table' => "{$this->Servico->databaseTable}.{$this->Servico->tableSchema}.{$this->Servico->useTable}",
				'alias' => 'Servico',
				'conditions' => 'DetalheItemPedidoManual.codigo_servico = Servico.codigo',
				'type' => 'INNER',
			),
		);

		//monta o filtro
		$conditions = array('ItemPedido.codigo_pedido' => $codigo_pedido);

		//recupera os dados do banco
		$lista_detalhe_itens = $this->ItemPedido->find('all', 
			array(
				'fields' => $fieldsItem,
				'joins' => $joinItem,
				'conditions' => $conditions,
			)
		);

		//varre os dados para tansformar em json
		$retorno = "erro";		
		
		if(isset($lista_detalhe_itens)) {
			
			if(!empty($lista_detalhe_itens)) {
				
				//retorna o json
				$retorno = json_encode($lista_detalhe_itens);
			}
		}

		echo $retorno;
		exit;

	} //fim get_produto_servico

	public function gerar_faturamento_percapita($codigo_cliente_pagador,$mes_referencia=null,$ano_referencia=null) {
		
		//verifica se tem dados passados
		if(is_null($mes_referencia) && is_null($ano_referencia)) {
			
			//pega o mes passado
			$base_periodo = strtotime('-1 month', strtotime(Date('Y-m')));

			//seta a data de inicio
			$mes_referencia = Date('m', $base_periodo);
			$ano_referencia = Date('Y', $base_periodo);
		}//fim is null dados

		$this->__jasperFaturamentoPerCapita($codigo_cliente_pagador,$mes_referencia,$ano_referencia);
	}//FINAL FUNCTION gerar_faturamento_percapita
	
	private function __jasperFaturamentoPerCapita( $codigo_cliente, $mes_referencia, $ano_referencia ) {
		
		require_once APP . 'vendors' . DS . 'buonny' . DS . 'RelatorioWebService.php';
		
		$RelatorioWebService = new RelatorioWebService();
		
		$parametros = array( 'CODIGO_CLIENTE_PAGADOR' => $codigo_cliente, 'MES_REFERENCIA' => $mes_referencia, 'ANO_REFERENCIA' => $ano_referencia );
		
		header(sprintf('Content-Disposition: attachment; filename="%s"', basename( 'demonstrativo_percapita.pdf' )));
		
		$url = $RelatorioWebService->executarRelatorio( '/reports/RHHealth/demonstrativo_percapita', $parametros, 'pdf');
		
		header('Pragma: no-cache');
		
		header('Content-type: application/pdf');
		
		echo $url;die;
	}//FINAL FUNCTION __jasperFaturamentoPerCapita

	public function lista_clientes_nao_automaticos(){
		
		$this->layout = 'ajax';

		$mes_referencia = date("m",strtotime("-1 month"));

		$ano_referencia = date("Y",strtotime("-1 month"));        
        
       	$filtros = $this->Filtros->controla_sessao($this->data, $this->Cliente->name);

		$fields = array(
			'Cliente.codigo',
			'Cliente.nome_fantasia',
			'Cliente.codigo_documento',
			'CASE
				WHEN [Pedido].[codigo] is null THEN \'1\' 
				WHEN [Pedido].[data_integracao] is null THEN \'2\' 
				WHEN [Pedido].[data_integracao] is not null THEN \'3\'
			END AS status_pedido'
		);

		$joins = array(
			array(
          		'table' => 'pedidos',
          		'alias' => 'Pedido',
          		'type' => 'LEFT',
          		'conditions' => 'Pedido.codigo = (SELECT TOP 1 codigo FROM pedidos WHERE codigo_cliente_pagador = Cliente.codigo AND ano_referencia = '. $ano_referencia .' AND mes_referencia = '.$mes_referencia.')'
      		),
		);

		$conditions = $this->ItemPedido->converteFiltroEmConditiON($filtros);

		$conditions[] = array('Cliente.aguardar_liberacao' => 1);

		$order = 'Cliente.codigo';

		$this->paginate['Cliente'] = array(
            'fields' => $fields,
            'conditions' => $conditions,
            'limit' => 50,
            'joins' => $joins,
            'order' => $order,
        );

        // pr($this->Cliente->find('sql', $this->paginate['Cliente']));
        
        $dados_liberacao = $this->paginate('Cliente');

        $codigo_cliente = $this->data['Cliente']['codigo'];

        $this->set(compact('dados_liberacao', 'codigo_cliente'));
	}//FINAL FUNCTION lista_clientes_nao_automaticos


}//FINAL CLASS ItensPedidosController	