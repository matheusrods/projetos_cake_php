<?php

class ClientesProdutosController extends AppController {

	public $name = 'ClientesProdutos';
	public $layout = 'cliente';
	public $helpers = array('Highcharts');
	public $components = array();
	public $uses = array('ClienteProduto', 'Cliente', 'Produto', 'ProdutoServico','Servico', 'ClienteProdutoServico2', 'PServicoProfissionalTipo', 'MotivoBloqueio', 'StatusContrato', 'ClienteProdutoLog', 'GrupoEconomicoCliente');

	function lista_produtos_tlcs($codigo_cliente) {
		$this->layout = 'ajax';
		$produtos = $this->ClienteProduto->listaProdutosTLCS($codigo_cliente);
		$produtos = array('Produto') + $produtos;
		$this->set(compact('produtos'));
		$this->render('lista_produtos_combo');
	}

	/**
	 * Inclui um novo Cliente Produto no banco.
	 *
	 * @return void
	 *
	 * @param type $codigo_cliente_produto
	 */
	public function excluir($codigo_cliente_produto) {
		$this->loadModel('ClienteProdutoServico2');

		$cliente_produto = $this->ClienteProduto->getClienteProdutoByCodigo($codigo_cliente_produto);
		$cliente = $this->Cliente->find('first', array(
			'conditions' => array(
				'Cliente.codigo' => $cliente_produto['ClienteProduto']['codigo_cliente']
			)
		));

		$cliente_produto_servico2 = $this->ClienteProdutoServico2->find('all',array('conditions' => array('codigo_cliente_produto' => $codigo_cliente_produto)));
				
		foreach($cliente_produto_servico2 as $servico) {
			$result_servico = $this->ClienteProdutoServico2->excluir($servico['ClienteProdutoServico2']['codigo']);
		}

		$result = $this->ClienteProduto->excluir($codigo_cliente_produto, true);

		if($result) {
			$this->BSession->setFlash('delete_success');
		} else {
			$this->BSession->setFlash('delete_error');
		}

		$this->redirect($this->referer());

	}

	/**
	 * Inclui um novo produto
	 *
	 * @return void
	 */
	public function incluir($codigo_cliente) {
		if ($this->RequestHandler->isPost()) {
			$codigo_cliente = $this->data['ClienteProduto']['codigo_cliente'];
			$codigo_produto = $this->data['ClienteProduto']['codigo_produto'];

			$this->data['ClienteProduto']['data_faturamento'] = Date('Ymd H:i:s');
			$this->data['ClienteProduto']['codigo_motivo_bloqueio'] = 1;


			$result = $this->ClienteProduto->incluir($this->data, true);

			if ($result) {
				$codigo_produto_servico = $this->ClienteProduto->id;
				$this->criar_usuario_para_cliente($codigo_cliente, $codigo_produto);
				$this->PServicoProfissionalTipo->incluirProfissionaisPorClienteProduto(
					$codigo_cliente,
					$codigo_produto,
					$codigo_produto_servico
				);

				if ($result) {
					$this->BSession->setFlash('save_success');

				} else {
					$this->BSession->setFlash('save_error');
				}
			} else {
			   $this->BSession->setFlash('save_error');
			}
		}

		$this->layout = 'ajax';
		$this->data['ClienteProduto']['codigo_cliente'] = $codigo_cliente;
		$produtos = $this->Produto->listar();
                                    
		$this->set(compact('codigo_cliente', 'produtos'));
	}
	
	function criar_usuario_para_cliente($codigo_cliente, $codigo_produto) {
		$this->loadModel('Usuario');

		if (count($this->Usuario->listaPorCliente($codigo_cliente, false, false)) && $codigo_produto != 82)
			return false;

		$this->loadModel('Cliente');
		$this->loadModel('TipoRetorno');
		$this->loadModel('ClientEmpresa');
		$this->loadModel('ClienteContato');

		$dados_cliente = $this->Cliente->carregar($codigo_cliente);

		$dados = array();
		$dados['Usuario']['ativo'] = 1;
		$dados['Usuario']['codigo_uperfil'] = 21;
		$dados['Usuario']['codigo_departamento'] = 11;
		$dados['Usuario']['apelido'] = $dados_cliente['Cliente']['codigo'];
		$dados['Usuario']['nome'] = $dados_cliente['Cliente']['razao_social'];
		$dados['Usuario']['codigo_cliente'] = $dados_cliente['Cliente']['codigo'];
		$dados['Usuario']['codigo_documento'] = $dados_cliente['Cliente']['codigo_documento'];
		if (in_array($codigo_produto, array(1,2)))
			$dados['Usuario']['data_senha_expiracao'] = date('Y-m-d 00:00:00', strtotime('+1 year'));

		$cliente_monitora = $this->ClientEmpresa->porCnpj( $dados_cliente['Cliente']['codigo_documento'], true, true);        
        if( !empty($cliente_monitora[0]['ClientEmpresa']['codigo']) ){
        	$dados['Usuario']['codigo_usuario_monitora'] = $cliente_monitora[0]['ClientEmpresa']['codigo'];
        }
        
		if($this->Usuario->incluir($dados) || $codigo_produto == 82) {
			if ($codigo_produto == 82) {
				$base_cnpj = substr($dados_cliente['Cliente']['codigo_documento'], 0, 8);
				$cliente_monitora = $this->ClientEmpresa->porBaseCnpj($base_cnpj);
				$codigos_monitora = array_keys($cliente_monitora);
				foreach($codigos_monitora as $codigo) {
					if (!$this->ClientEmpresa->criarSenha($codigo))
						return false;
				}
				return true;
			} elseif (in_array($codigo_produto, array(1,2))) {
				$codigo_usuario = $this->Usuario->getInsertId();
				$senha_usuario  = $this->Usuario->find('first', array('conditions' => array('codigo' => $codigo_usuario), 'fields' => array('senha')));

				$Encriptacao   = new Buonny_Encriptacao();
				$senha_usuario = $Encriptacao->desencriptar($senha_usuario['Usuario']['senha']);
				
				$todos_contatos = '';
				$contato_cliente = $this->ClienteContato->find('all', array('fields' => array('DISTINCT descricao'), 'conditions' => array('codigo_cliente' => $codigo_cliente, 'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL)));
				foreach ($contato_cliente as $contato)
					$todos_contatos .= str_replace(' ', ';', $contato['ClienteContato']['descricao']).';';
				$todos_contatos = substr($todos_contatos, 0, strlen($todos_contatos) - 1);
				
				App::import('Component', array('StringView', 'Mailer.Scheduler'));
				$this->StringView = new StringViewComponent();
				$this->Scheduler  = new SchedulerComponent();

				$this->StringView->reset();
				$this->StringView->set(compact('dados', 'senha_usuario'));
				$content = $this->StringView->renderMail('email_teleconsult_unificado', 'default');
				$options = array(
					'from' => 'portal@buonny.com.br',
					'sent' => null,
					'to'   => $todos_contatos,
					'subject' => 'Conta de Usuario do Portal Buonny',
				);
				return $this->Scheduler->schedule($content, $options) ? true: false;
			}
		} else {
			return false;
		}
	}
	
	function reenviar_senha($codigo_cliente, $codigo_cliente_produto) {
		$this->loadModel('Usuario');
		$this->loadModel('ClienteContato');
		App::import('Vendor', 'encriptacao');
		$dados = $this->Usuario->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));
		$Encriptacao   = new Buonny_Encriptacao();
		$senha_usuario = $Encriptacao->desencriptar($dados['Usuario']['senha']);
		$todos_contatos = '';
		$contatos_cliente = $this->ClienteContato->find('all', array('fields' => array('DISTINCT descricao'), 'conditions' => array('codigo_cliente' => $codigo_cliente, 'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL)));
		foreach ($contatos_cliente as $contato_cliente)
			$todos_contatos .= str_replace(' ', ';', $contato_cliente['ClienteContato']['descricao']).';';
		$todos_contatos = substr($todos_contatos, 0, strlen($todos_contatos) - 1);
		App::import('Component', array('StringView', 'Mailer.Scheduler'));
		$this->StringView = new StringViewComponent();
		$this->Scheduler  = new SchedulerComponent();

		$this->StringView->reset();
		$this->StringView->set(compact('dados', 'senha_usuario'));
		$content = $this->StringView->renderMail('email_teleconsult_unificado', 'default');
		$options = array(
			'from' => 'portal@buonny.com.br',
			'sent' => null,
			'to'   => $todos_contatos,
			'subject' => 'Conta de Usuario do Portal Buonny',
		);
		$retorno = $this->Scheduler->schedule($content, $options) ? true: false;
		return $retorno;
	}
	
	/**
	 * Atualiza o status do ClienteProduto
	 *
	 * @return void
	 */
	public function atualizar_status($codigo_cliente_produto, $codigo_cliente) {
		if (!empty($this->data)) {
			$cliente_produto = $this->ClienteProduto->find('first', array('fields' => array('codigo_produto', 'codigo_motivo_bloqueio'), 'conditions' => array('ClienteProduto.codigo' => $codigo_cliente_produto)));
			$motivo_bloqueio = in_array($cliente_produto['ClienteProduto']['codigo_motivo_bloqueio'], array(3,8,10));
			$produto_teleconsult = in_array($cliente_produto['ClienteProduto']['codigo_produto'], array(1,2));
			
			$result = $this->ClienteProduto->atualizar($this->data, true);
			
			if ($result) {
				if ($motivo_bloqueio && $produto_teleconsult && $this->data['ClienteProduto']['codigo_motivo_bloqueio'] == 1)
					$this->reenviar_senha($codigo_cliente, $codigo_cliente_produto);
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
		$this->data = $this->ClienteProduto->getClienteProdutoByCodigo($codigo_cliente_produto);
		$motivos = $this->MotivoBloqueio->combo();
		$this->set(compact('motivos', 'codigo_cliente'));
	}

	public function editar($codigo_cliente_produto, $codigo_cliente, $visualizar = null, $options = null) {
		$this->loadModel('ClienteProdutoServico2');
		$this->loadModel('MotivoCancelamento');
		$this->loadModel('Usuario');

		$pendencia_comercial  = $this->BAuth->temPermissao($this->authUsuario['Usuario']['codigo_uperfil'], 'obj_pendencia_comercial');
		$pendencia_financeira = $this->BAuth->temPermissao($this->authUsuario['Usuario']['codigo_uperfil'], 'obj_pendencia_financeira');
		$pendencia_juridica   = $this->BAuth->temPermissao($this->authUsuario['Usuario']['codigo_uperfil'], 'obj_pendencia_juridica');
		
		if (!empty($this->data)) {
						$empresa_bloqueada = 8;
			$ok                = 1;
			
			if (isset($this->data['ClienteProduto']['visualizar']) && !empty($this->data['ClienteProduto']['visualizar'])) {
				$visualizar = $this->data['ClienteProduto']['visualizar'];
				unset($this->data['ClienteProduto']['visualizar']);
			}

			$dados = $this->ClienteProduto->getClienteProdutoByCodigo($codigo_cliente_produto);
						$status_pendencia_comercial  = $dados['ClienteProduto']['pendencia_comercial'];
			$status_pendencia_financeira = $dados['ClienteProduto']['pendencia_financeira'];
			$status_pendencia_juridica   = $dados['ClienteProduto']['pendencia_juridica'];

			$this->data['ClienteProduto']['pendencias'] = is_array($this->data['ClienteProduto']['pendencias']) ? $this->data['ClienteProduto']['pendencias']: array();
			$comercial  = array_search('comercial', $this->data['ClienteProduto']['pendencias'])  !== false ? 1: 0;
			$financeira = array_search('financeira', $this->data['ClienteProduto']['pendencias']) !== false ? 1: 0;
			$juridica   = array_search('juridica', $this->data['ClienteProduto']['pendencias'])   !== false ? 1: 0;

			$this->data['ClienteProduto']['pendencia_comercial']  = $pendencia_comercial  ? $comercial:  $status_pendencia_comercial;
			$this->data['ClienteProduto']['pendencia_financeira'] = $pendencia_financeira ? $financeira: $status_pendencia_financeira;
			$this->data['ClienteProduto']['pendencia_juridica']   = $pendencia_juridica   ? $juridica:   $status_pendencia_juridica;
			
			$this->data['ClienteProduto']['pendencias'][] = $this->data['ClienteProduto']['pendencia_comercial'];
			$this->data['ClienteProduto']['pendencias'][] = $this->data['ClienteProduto']['pendencia_financeira'];
			$this->data['ClienteProduto']['pendencias'][] = $this->data['ClienteProduto']['pendencia_juridica'];


			if($this->data['ClienteProduto']['valor_premio_minimo'] == null)
				$this->data['ClienteProduto']['valor_premio_minimo'] = 0;
		
			// if($this->data['ClienteProduto']['qtd_premio_minimo'] == null)
			// 	$this->data['ClienteProduto']['qtd_premio_minimo'] = 0;

			if($this->data['ClienteProduto']['codigo_motivo_bloqueio'] != 17)
				$this->data['ClienteProduto']['codigo_motivo_cancelamento'] = null;

			if($this->data['ClienteProduto']['codigo_motivo_bloqueio'] != 8)
				$this->data['ClienteProduto']['pendencias'] = null;
			try {	

				if(($this->data['ClienteProduto']['pendencia_comercial'] == 1) || ($this->data['ClienteProduto']['pendencia_financeira'] == 1) || ($this->data['ClienteProduto']['pendencia_juridica'] == 1)){
					$this->data['ClienteProduto']['codigo_motivo_bloqueio'] = 8;
					$this->data['ClienteProduto']['codigo_motivo_cancelamento'] = NULL;
				} 
				if($this->data['ClienteProduto']['codigo_motivo_bloqueio'] == 8){
					if(($this->data['ClienteProduto']['pendencia_comercial'] == 0) && ($this->data['ClienteProduto']['pendencia_financeira'] == 0) && ($this->data['ClienteProduto']['pendencia_juridica'] == 0)){
												throw new Exception("");
					}	
				}
				$this->data['ClienteProduto']['codigo_usuario_alteracao'] = $this->authUsuario['Usuario']['codigo'];
			
				if(!$this->ClienteProduto->atualizar($this->data, false)){
										throw new Exception("");
				}
				$this->BSession->setFlash('save_success');
				$this->redirect(array('action'=> $visualizar ? 'assinatura_visualizar':'assinatura'));
			} catch(Exception $e) {
				$this->BSession->setFlash('save_error');
			}

		}
		$validate = $this->ClienteProduto->invalidFields();
		$this->data = $this->ClienteProduto->getClienteProdutoByCodigo($codigo_cliente_produto);
		$this->data['Usuario'] = Set::extract('/Usuario/apelido', $this->Usuario->find('first', array('fields' => array('apelido'), 'conditions' => array('Usuario.codigo' => $this->data['ClienteProduto']['codigo_usuario_alteracao']))));
		$this->data['ClienteProdutoLog'] = $this->ClienteProdutoLog->find('all', array(
			'conditions' => array('codigo_cliente_produto' => $codigo_cliente_produto),
			'limit' => 2,
			'order' => array('ClienteProdutoLog.data_inclusao DESC')
		));

		if(isset($validate['codigo_motivo_cancelamento']))
			$this->data['ClienteProduto']['codigo_motivo_bloqueio'] = 17;


		if(isset($this->data['ClienteProdutoLog'][0]) && isset( $this->data['ClienteProdutoLog'][1]))
			$this->data['ClienteProdutoLog'] = array_diff_assoc($this->data['ClienteProdutoLog'][0]['ClienteProdutoLog'],$this->data['ClienteProdutoLog'][1]['ClienteProdutoLog']);
		if(isset($this->data['ClienteProdutoLog']['codigo_motivo_bloqueio'])) {
			$motivo_bloqueio = $this->MotivoBloqueio->find('first', array('conditions'=>array('codigo' => $this->data['ClienteProdutoLog']['codigo_motivo_bloqueio']),'fields' => array('descricao')));
			$this->data['ClienteProdutoLog']['codigo_motivo_bloqueio'] = $motivo_bloqueio['MotivoBloqueio']['descricao'];
		}

		$desativa_campo_status = 0;
		if( $this->data['ClienteProduto']['pendencia_comercial']) {
			$this->data['ClienteProduto']['pendencias'][] = 'comercial';
			$desativa_campo_status += 1;
		}
		if( $this->data['ClienteProduto']['pendencia_financeira']) {
			$this->data['ClienteProduto']['pendencias'][] = 'financeira';
			$desativa_campo_status += 1;
		}
		if( $this->data['ClienteProduto']['pendencia_juridica']) {
			$this->data['ClienteProduto']['pendencias'][] = 'juridica';
			$desativa_campo_status += 1;
		}

		if ($desativa_campo_status == 1) {
			$x = $this->data['ClienteProduto']['pendencias'][0];
			if (!$this->BAuth->temPermissao($this->authUsuario['Usuario']['codigo_uperfil'], 'obj_pendencia_'.$x))
				$desativa_campo_status += 1;
		}

		$premio_minimo_servico = $this->ClienteProdutoServico2->find('first',array(
			'conditions' => array(
				'codigo_cliente_produto' => $codigo_cliente_produto,
				'valor_premio_minimo <>' => 0)
			)
		);

		if(!empty($premio_minimo_servico)) $premio_minimo_servico = true;
		
		$motivos = $this->MotivoBloqueio->find('list', array('conditions' => array('codigo' => array(1,8,17)), 'order' => 'descricao DESC'));
		$motivos_cancelamentos = $this->MotivoCancelamento->find('list');

		$codigo_produto = $this->data['ClienteProduto']['codigo_produto'];
		$codigo_cliente_pagador = $this->ClienteProdutoServico2->find('first', array('fields' => array('codigo_cliente_pagador'),'conditions' => array('codigo_cliente_produto' => $this->data['ClienteProduto']['codigo'])));
		$codigo_cliente_pagador = $codigo_cliente_pagador['ClienteProdutoServico2']['codigo_cliente_pagador'];
		
		$this->set(compact('motivos_cancelamentos','desativa_campo_status', 'premio_minimo_servico','motivos','codigo_cliente', 'codigo_produto', 'codigo_cliente_pagador', 'pendencia_comercial', 'pendencia_juridica', 'pendencia_financeira', 'visualizar'));
	}

	
	public function atualizar_status_financeiro($codigo_cliente_produto, $codigo_cliente) {	  
		if (!empty($this->data)) {		  
			$result = $this->ClienteProduto->atualizar($this->data, true);
			if ($result) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
		$this->data = $this->ClienteProduto->getClienteProdutoByCodigo($codigo_cliente_produto);
		$motivos = $this->MotivoBloqueio->combo();
		$this->set(compact('motivos', 'codigo_cliente', 'financeiro'));
	}
	
	function index($consulta=NULL) {
		$this->data['ClienteProduto'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProduto->name);
		$produtos = $this->Produto->find('list');
		$status_produto = $this->MotivoBloqueio->find('list', array('order' => 'codigo asc'));
		$status_contrato = $this->StatusContrato->find('list');
		


		$this->set(compact('produtos', 'status_contrato', 'status_produto','consulta'));
	}
	
	function gerenciar_status() {
		$this->data['ClienteProduto'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProduto->name);

		$produtos = $this->Produto->find('list');
		$status_produto = $this->MotivoBloqueio->find('list', array('order' => 'codigo asc'));
		$status_contrato = $this->StatusContrato->find('list');

		$this->set(compact('produtos', 'status_contrato', 'status_produto'));
	}
   
	function listagem_paginate($destino='') {
		$this->layout = 'ajax';

		$filtros	= $this->Filtros->controla_sessao($this->data, $this->ClienteProduto->name);
		$conditions = $this->ClienteProduto->converteFiltroEmCondition($filtros);

		$this->paginate['ClienteProduto'] = array(
			'recursive' => 0,
			'fields' => array(
				'Cliente.codigo',
				'Cliente.data_inclusao',
				'Cliente.razao_social',
				'Produto.descricao',
				'StatusContrato.descricao',
				'MotivoBloqueio.descricao',
				'ClientePC.codigo',
				'ClientePC.numero',
				'ClientePC.codigo_status_contrato',
				'convert(varchar, ClientePC.data_contrato, 103) as data_contrato',
				'convert(varchar, ClientePC.data_envio, 103) as data_envio',
				'convert(varchar, ClientePC.data_vigencia, 103) as data_vigencia',
			),
			'joins' => array(
				array(
					'table' => 'dbbuonny.vendas.cliente',
					'alias' => 'Cliente',
					'type' => 'INNER',
					'conditions' => 'Cliente.codigo = ClienteProduto.codigo_cliente'
				),
				array(
					'table' => 'dbbuonny.vendas.cliente_produto_contrato',
					'alias' => 'ClientePC',
					'type' => 'LEFT',
					'conditions' => 'ClientePC.codigo_cliente_produto = ClienteProduto.codigo'
				),
				array(
					'table' => 'dbbuonny.vendas.status_contrato',
					'alias' => 'StatusContrato',
					'type' => 'LEFT',
					'conditions' => 'StatusContrato.codigo = ClientePC.codigo_status_contrato'
				),
			),
			'conditions' => $conditions,
			'limit' => 50,
			'order' => 'Cliente.codigo asc'
		);

		$clientes_produtos = $this->paginate('ClienteProduto');

		$this->set(compact('clientes_produtos', 'destino'));
	}
	
	/**
	 * Action de listagem de produtos e servicos do cliente
	 * @param $destino
	 * @param $codigo_cliente
	 */
	public function lista_produtos_servicos($destino = '', $codigo_cliente = null) {
		if (empty($codigo_cliente)) 
			return;		
		if($destino == 'consulta'){
			$this->set(compact('destino'));
		}
		$somente_tlc = true;
		$produtos = $this->ClienteProduto->produtosServicosProfissionaisPorClienteTipoProfissional($codigo_cliente, $somente_tlc);
		$this->set(compact('produtos', 'codigo_cliente'));
	}	

	public function lista_produtos_servicos_financeiro($codigo_cliente = null) {
		if (empty($codigo_cliente)) return;
		$produtos = $this->ClienteProduto->produtosServicosProfissionaisPorCliente($codigo_cliente);
		$this->set(compact('produtos', 'codigo_cliente'));
	}
	
	public function listagem_produtos() {
		$this->layout = 'ajax';
		$filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteProduto->name);
		$conditions = $this->ClienteProduto->converteFiltroEmCondition($filtros);
		$fields = array('ClienteProduto.codigo', 'Produto.descricao', 'ClienteProdutoContrato.codigo', 'ClienteProduto.codigo_cliente','ClienteProdutoContrato.numero');
		$this->paginate['ClienteProduto'] = array(
			'conditions' => $conditions,
			'joins' => array(
				array(
					'table' => 'dbbuonny.vendas.cliente_produto_contrato',
					'alias' => 'ClienteProdutoContrato',
					'type' => 'left',
					'conditions' => 'ClienteProduto.codigo = ClienteProdutoContrato.codigo_cliente_produto',
				)
			),
			'fields' => $fields,
			'group' => $fields,
			'limit' => 50,
		);
		$clientes_produtos = $this->paginate('ClienteProduto');

		$this->set(compact('clientes_produtos'));
	}

	public function listagem_data_cadastro($codigo_cliente = null) {
		$produtos_motivo_bloqueios = $this->ClienteProduto->buscaPorCodigoCliente($codigo_cliente);
		$this->set(compact('produtos_motivo_bloqueios'));
	}

	public function gerenciar($codigo_cliente, $financeiro = false) {
		$this->pageTitle = 'SLA de Produtos do Cliente';
		if ($this->RequestHandler->isPost()) {
			if ($this->ClienteProdutoServico2->saveAll(array('ClienteProdutoServico2' => $this->data['ClienteProdutoServico2']))) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
		}
		$cliente = $this->Cliente->carregar($codigo_cliente);
		$this->set(compact('cliente', 'financeiro'));
	}

	/**
	 * [assinatura description]
	 * 
	 * Metodo para exibir a assinatura do cliente e da matriz
	 * 
	 * @return [type] [description]
	 */
	public function assinatura() 
	{
		//seta o titulo da pagina
		$this->pageTitle = 'Assinatura';
		//pega os filtros
		$this->data['ClienteProduto'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProduto->name);
		
		//verifica se tem dados filtrados
		if (!empty($this->data) && !empty($this->data['ClienteProduto']['codigo_cliente'])) {
			//carrega a model do cliente
			$this->loadModel('Cliente');
			//pega os dados do cliente
			$cliente = $this->Cliente->carregar($this->data['ClienteProduto']['codigo_cliente']);
		
			//verifica se tem dados carregados para o cliente pesquisado
			if( $cliente ){
				//pega os dados do produto configurado para este cliente
				$produtos = $this->ClienteProduto->listarPorCodigoCliente($cliente['Cliente']['codigo'], false, true);
				
				############## TRECHO PARA PEGAR AS ASSINATURA DA MATRIZ  #####################
				//pega o grupo economico que esta vinculado
				$grupo_economico = $this->GrupoEconomicoCliente->find('first', array('conditions' => array('GrupoEconomicoCliente.codigo_cliente' => $cliente['Cliente']['codigo'])));
				//codigo da matriz
				$codigo_cliente_matriz = $grupo_economico['GrupoEconomicoCliente']['matriz'];
				
				//verifica se o codigo da matriz é o mesmo codigo do cliente que esta querendo ver a assinatura pois precisa ser diferente
				if($cliente['Cliente']['codigo'] != $codigo_cliente_matriz) {

					//pega os dados do cliente
					$cliente_matriz = $this->Cliente->carregar($codigo_cliente_matriz);

					//array servicos que nao devem ser buscados
					$array_codigos_servicos = false;

					//verifica se existe produto cadastrado
					if(!empty($produtos)) {
						//para nao exibir os dados que ja estao cadastrados no cliente
						foreach ($produtos as $prod) {
							//varre os servicos
							foreach($prod['ClienteProdutoServico2'] as $servico){
								$array_codigos_servicos[] = $servico['Servico']['codigo'];
							}//fim foreach servicos
						}//fim foreach dos produtos
					}//fim verificacao se existe produtos

					//produtos da matriz
					$produto_matriz = $this->ClienteProduto->listarPorCodigoCliente2($codigo_cliente_matriz,$array_codigos_servicos);
				} //fim verifica o codigo da matriz


				############## TRECHO PARA PEGAR OS SERVICOS QUE IRÁ PAGAR #####################
				$produto_pagador = $this->ClienteProduto->listarPorCodigoClientePagador($cliente['Cliente']['codigo']);

				$this->set(compact('cliente', 'produtos','produto_matriz','cliente_matriz', 'produto_pagador'));
			}//fim verificacao do cliente pesquisado

		}//fim dados filtrados

	}//fim assinatura

	public function assinatura_cliente_para_cliente(){

		if ( isset($_SESSION['erros']) && !empty($_SESSION['erros']) ) {
			foreach ($_SESSION['erros'] as $erros) {
				$this->ClienteProduto->validationErrors[$erros['produto']][$erros['servico']] = $erros['erro'];
			}
			unset($_SESSION['erros']);
		}

		//seta o titulo da pagina
		$this->pageTitle = 'Copia de Assinatura';
		//pega os filtros
		$this->data['ClienteProduto'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProduto->name);

		if (!empty($this->data) && !empty($this->data['ClienteProduto']['ClienteDe']) && !empty($this->data['ClienteProduto']['ClientePara'])) {
			$cliente['ClienteDe'] = $this->Cliente->findByCodigo($this->data['ClienteProduto']['ClienteDe']);
			$cliente['ClientePara'] = $this->Cliente->findByCodigo($this->data['ClienteProduto']['ClientePara']);

			if (!empty($cliente['ClienteDe']['Cliente']['codigo']) && !empty($cliente['ClientePara']['Cliente']['codigo'])){
				$cliente_tem_assinatura = $this->ClienteProduto->find('all',array('conditions' => array('codigo_cliente' => $cliente['ClientePara']['Cliente']['codigo'])));

				if (empty($cliente_tem_assinatura)){

					$produtos = $this->ClienteProduto->listarPorCodigoClienteParaCliente($cliente['ClienteDe']['Cliente']['codigo']);

					$this->set(compact('cliente','produtos'));

				} else {
					$this->ClienteProduto->invalidate('ClientePara', 'O cliente selecionado já possui assinatura!');
				}

			} else {
				if(empty($cliente['ClienteDe']['Cliente']['codigo'])){
					$this->ClienteProduto->invalidate('ClienteDe', 'Cliente informado não existe!');
				}
				if(empty($cliente['ClientePara']['Cliente']['codigo'])){
					$this->ClienteProduto->invalidate('ClientePara', 'Cliente informado não existe!');
				}
			}
		}
	}

	public function monta_copia_assinatura($codigo_cliente_pagador){
		$this->ClienteProduto = ClassRegistry::init('ClienteProduto');
		
		$retorno = $this->ClienteProduto->copiarAssinatura($this->data, $codigo_cliente_pagador);
		if (!isset($retorno['erros']) && empty($retorno['erros'])) {
			$this->data['ClienteProduto']['codigo_cliente'] = $codigo_cliente_pagador;
			$this->data['ClienteProduto'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProduto->name);
			$this->BSession->setFlash('save_success');
			$this->redirect(array('controller' => 'clientes_produtos', 'action' => 'assinatura'));
		} else {
			foreach ($retorno['erros'] as $codigo_produto => $servicos) {
				foreach ($servicos as $codigo_servico => $erro) {
					$_SESSION['erros'][] = array('produto' => $codigo_produto,'servico' => $codigo_servico,'erro' => $retorno['erros'][$codigo_produto][$codigo_servico]['valor']);
				}
			}
			$this->BSession->setFlash('save_error');
			$this->redirect(array('controller' => 'clientes_produtos', 'action' => 'assinatura_cliente_para_cliente'));
		}
	}

	public function assinatura_visualizar() {
		$this->pageTitle = 'Assinatura Visualizar';
		$this->data['ClienteProduto'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProduto->name);
		if (!empty($this->data) && !empty($this->data['ClienteProduto']['codigo_cliente'])) {
			$this->loadModel('Cliente');
			$cliente = $this->Cliente->carregar($this->data['ClienteProduto']['codigo_cliente']);
			$produtos = $this->ClienteProduto->listarPorCodigoCliente($cliente['Cliente']['codigo']);
			$this->set(compact('cliente', 'produtos'));
		}
	}

	public function validate() {
		$validations = array();
		foreach ($this->data['ClienteProdutoServico2']['codigo_lista_de_preco_produto_servico'] as $codigo_lista_de_preco_produto_servico => $checked) {
			if ($checked) {
				if (empty($this->data['ClienteProdutoServico2']['codigo_cliente_pagador'][$codigo_lista_de_preco_produto_servico])) {
					$validations['codigo_cliente_pagador'][$codigo_lista_de_preco_produto_servico] = 'Pagador não informado';
				}
				if (empty($this->data['ClienteProdutoServico2']['valor'][$codigo_lista_de_preco_produto_servico])) {
					$validations['valor'][$codigo_lista_de_preco_produto_servico] = 'Valor não informado';
				}
			}
		}
		$this->ClienteProdutoServico2->validationErrors = $validations;
		return count($validations == 0);
	}

	private function initData($produtos) {
		$data = array();
		foreach ($produtos as $produto) {
			foreach ($produto['ListaDePrecoProdutoServico'] as $servico) {
				$tipo_premio_minimo = $produto['ListaDePrecoProduto']['valor_premio_minimo'] > 0 ? 1 : 2;
				$valor_premio_minimo = $tipo_premio_minimo == 1 ? $produto['ListaDePrecoProduto']['valor_premio_minimo'] : $servico['valor_premio_minimo'];
				$qtd_premio_minimo = $tipo_premio_minimo == 1 ? $produto['ListaDePrecoProduto']['qtd_premio_minimo'] : $servico['qtd_premio_minimo'];
				$data['codigo_lista_de_preco_produto_servico'][$servico['codigo']] = 0;
				$data['valor'][$servico['codigo']] = number_format($servico['valor'], 2, '.', '');
				$data['valor_premio_minimo'][$servico['codigo']] = number_format($valor_premio_minimo, 2, '.', '');
				$data['qtd_premio_minimo'][$servico['codigo']] = $qtd_premio_minimo;
				$data['tipo_premio_minimo'][$servico['codigo']] = $tipo_premio_minimo == 1 ? 'PRODUTO' : 'SERVICO';
			}
		}
		$this->data['ClienteProdutoServico2'] = $data;

	}

	public function incluir2($codigo_cliente) {
		$this->pageTitle = 'Incluir Nova Assinatura';
		$this->loadModel('ListaDePreco');
		$this->loadModel('ListaDePrecoProduto');
		$this->loadModel('ListaDePrecoProdutoServico');
		$this->loadModel('ClienteProdutoServico2');

		if (!empty($this->data)) {
			$this->validate();
		}


		if ($this->RequestHandler->isPost()) {

			$this->data['ClienteProdutoServico2']['codigo_cliente'] = $codigo_cliente;
			// $this->data['ClienteProdutoServico2']['codigo_cliente_pagador'] = $codigo_cliente;

			if ($retorno = $this->ClienteProduto->incluirAssinatura($this->data)) {

				$codigo_produto_anterior = 0;
				if(count($retorno['erros']) == 0){
					unset($retorno['erros']);
					foreach ($retorno as $servico) {
						$codigo_produto 		= $servico['ClienteProdutoServico2']['codigo_produto'];
						$codigo_produto_servico = $servico['ClienteProdutoServico2']['codigo_cliente_produto'];

						if($codigo_produto != $codigo_produto_anterior) {
							// $this->criar_usuario_para_cliente($codigo_cliente, $codigo_produto);
							$this->ClienteProduto->alteracoesProdutos 	= null;
							$this->ClienteProduto->inclusaoCliente 		= null;
						}
						$codigo_produto_anterior = $codigo_produto;
					}

	                $this->BSession->setFlash('save_success');
	                $this->redirect(array('controller' => 'clientes_produtos', 'action' => 'assinatura'));
            	}else{
            		$erros = array();
            		foreach($retorno['erros'] as $key => $erro){   
            			if(is_array($erro)){
	            			foreach($erro as $campo => $mensagem){
	            				if(!isset($this->ClienteProdutoServico2->validationErrors[$campo]) || !is_array($this->ClienteProdutoServico2->validationErrors[$campo])){
	            					$this->ClienteProdutoServico2->validationErrors[$campo] = array();
	            				}
	            				$this->ClienteProdutoServico2->validationErrors[$campo][$key] = $mensagem;
	            				$erros[$mensagem] = $mensagem;            					
							}     
						}else{
							$erros[] = $erro;
						}  			
            		}
            		if(count($erros > 0)){
            			$this->BSession->setFlash(array(MSGT_ERROR, implode('<br>',$erros)));
            		}else{
    					$this->BSession->setFlash('save_error');
    				}
            	}
            } else{            	
        		$this->BSession->setFlash('save_error');
            }
        }     

        $cliente = $this->Cliente->carregar($codigo_cliente);
		$cliente_subtipo = $this->Cliente->retornarClienteSubTipo($codigo_cliente);
		
		$lista_de_preco = $this->ListaDePreco->porFornecedor(NULL);
		$produtos = $this->listaDePrecoProdutos($lista_de_preco['ListaDePreco']['codigo'], $cliente['Cliente']['codigo']);

		if (empty($this->data)) {
			$this->initData($produtos);
		}

		$this->set(compact('cliente', 'cliente_subtipo', 'lista_de_preco', 'produtos'));
	}

	private function listaDePrecoProdutos($codigo_lista_de_preco, $codigo_cliente) {
		$produtos = $this->ListaDePrecoProduto->listarPorCodigoListaDePrecoPadrao($codigo_lista_de_preco);
		$cliente_produtos = $this->ClienteProduto->listarPorCodigoCliente($codigo_cliente);		
		foreach ($produtos as $key_produto => $produto) {
			foreach ($produto['ListaDePrecoProdutoServico'] as $key_servico => $servico) {
				if ($this->clienteJaPossui($produto['Produto']['codigo'], $servico['codigo_servico'], $cliente_produtos)) {
					unset($produtos[$key_produto]['ListaDePrecoProdutoServico'][$key_servico]);
				}
			}
			if (count($produto['ListaDePrecoProdutoServico']) == 0) {
				unset($produtos[$key_produto]['ListaDePrecoProdutoServico']);
			}			
			$produtos[$key_produto]['Produto']['quantitativo'] = in_array($produto['Produto']['codigo'], $this->Produto->produtos_quantitativos());
		}		
		return $produtos;
	}

	private function clienteJaPossui($codigo_produto, $codigo_servico, $produtos) {
		foreach ($produtos as $produto) {
			foreach ($produto['ClienteProdutoServico2'] as $servico) {
				if ($codigo_produto == $produto['ClienteProduto']['codigo_produto'] && $codigo_servico == $servico['codigo_servico']) {
					return true;
				}
			}
		}
		return false;
	}

	public function alterar_produto() {
        $this->pageTitle = 'Alterar Produto';
        $this->data['ClienteProduto'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProduto->name);
	}
 
    public function cadastrar_valores_servicos_do_produto ($codigo_produto,$codigo_cliente) {

    	$this->layout ='ajax';
    	
		if (!empty($this->data)) {

			$this->loadModel("Ficha");
			
			$filtros = $this->Filtros->controla_sessao($this->data, 'ClienteProduto');
	        $conditions['conditions'] = $this->Ficha->converteFiltroEmConditions(array('Ficha' => $filtros));
	        $cliente = $this->Cliente->carregar($filtros['codigo_cliente']);

	        $produto = $this->ClienteProduto->listarPorCodigoCliente($filtros['codigo_cliente'],true);

	        if (count($produto) == 1) {
	            $fichas = $this->Ficha->obterFichasParaAlterarProduto('all', $conditions);

	            $codigo_produto_antigo = $produto[0]['Produto']['codigo'];

	          	$dados['Cliente']['codigo'] = $filtros['codigo_cliente'];
				$dados['Cliente']['Produto']['codigo_produto_antigo'] = $codigo_produto_antigo;
				$dados['Cliente']['Produto']['codigo_produto'] = $codigo_produto;
				$dados['Cliente']['Produto']['data_faturamento'] = $produto[0]['ClienteProduto']['data_faturamento'];

				foreach ($this->data['ClienteProduto'] as $key => $valor) {
					$servicosarray['codigo_servico'] = $key;
					$servicosarray['valor'] = ($valor==null) ? 0 : $valor;
					$dados['Cliente']['Servico'][] = $servicosarray;
				}

		  		foreach($fichas as $ficha){
	                $dados['Cliente']['Ficha'][] = $ficha['Ficha'];
	            }

				$result = $this->Ficha->migrarProdutoCliente($dados);
				if ($result) {
					$this->BSession->setFlash('save_success');
				} else {
					$this->BSession->setFlash('save_error');
				}
	        }

		}
    	$servicos = $this->ProdutoServico->listarServicosPorProduto($codigo_produto);
		$servicosvalores = $this->ClienteProdutoServico2->ProdutosEServicos($codigo_cliente);

		foreach ($servicosvalores as $key => $value) {
    		$servicovalordefault[$value['Servico']['codigo']] = $value['ClienteProdutoServico2']['valor'];
    	}

    	$this->set(compact('servicos'));
    	$this->set(compact('servicovalordefault'));
    	$this->set(compact('codigo_produto'));
    	$this->set(compact('codigo_cliente'));
    }

    public function excluir_servico_assinatura($codigo_produto_servico) {
        $this->loadModel('ClienteProdutoServico2');

		if ($this->ClienteProdutoServico2->excluir_servico_assinatura($codigo_produto_servico)) {
			$this->BSession->setFlash('delete_success');
			$this->redirect(array('action' => 'assinatura'));
		} else {
        	$this->BSession->setFlash('delete_error');
		}
    }

	function estatisca_analitico_cliente_cancelamento(){
		$this->layout = 'new_window';
        $this->pageTitle = 'Estatística de Cancelameto';
        $this->Cliente = ClassRegistry::init('Cliente');
        $this->MotivoCancelamento = ClassRegistry::init('MotivoCancelamento');
		$this->Produto = ClassRegistry::init('Produto');
		$filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteProduto->name);
    	$dados = $this->ClienteProduto->preparaDadosListagemAnaliticoCancelamento($filtros);
		$this->paginate['ClienteProduto'] = array(
			'recursive' => -1,
			'joins' => $dados['ClienteProduto']['joins'],
			'fields'=> $dados['ClienteProduto']['fields'],
			'conditions' => $dados['ClienteProduto']['conditions'],
			'limit' => 50,
			'order' => 'Cliente.razao_social',
		);

		$estatistica_analitico = $this->paginate('ClienteProduto');
		$this->set(compact('estatistica_analitico'));

	}
	
	function estatistica_cancelamento() {
		$this->pageTitle = 'Estatística de Cancelamento';
		
		$this->Produto = ClassRegistry::init('Produto');
		$this->MotivoBloqueio = ClassRegistry::init('MotivoBloqueio');
		$this->MotivoCancelamento = ClassRegistry::init('MotivoCancelamento');
		
		if (!empty($this->data)) {
			$filtros = $this->data;

			$ano[] = $filtros['ClienteProduto']['ano'];
			$ano[] = $filtros['ClienteProduto']['ano'] - 1;
			
			$meses = COMUM::listMeses();
			$mes   = !empty($filtros['ClienteProduto']['mes']) ? $meses[$filtros['ClienteProduto']['mes']]: '';
			$dados = $this->ClienteProduto->estatisticaCancelamento($filtros);
			$grupo = $filtros['ClienteProduto']['agrupamento'] == 1 ? 'Produto': 'MotivoCancelamento';
			
			$this->set(compact('dados', 'ano', 'mes'));
			
			if ($dados) {
				$pre_series = array();
				$eixo_x = array(); 
				foreach ($dados as $dado) {
					$eixo_x[] = "'".$dado[$grupo]['descricao']."'";
					$pre_series[$ano[1]][] = $dado[0][$ano[1]];
					$pre_series[$ano[0]][] = $dado[0][$ano[0]];
				}

				$name[] = !empty($mes) ? $mes.' de '.$ano[1]: $ano[1];
				$name[] = !empty($mes) ? $mes.' de '.$ano[0]: $ano[0];
				
				$series = array(
					array('name' => "'{$name[0]}'",
						'values' => $pre_series[$ano[1]],
						'stack'  => 'male'
					),
					array('name' => "'{$name[1]}'",
						'values' => $pre_series[$ano[0]],
						'stack'  => 'female'
					),
				);
				$dadosGrafico = array('eixo_x' => $eixo_x, 'series' => $series);
				$this->set(compact('dadosGrafico'));
			}
			
        } else {
            //$this->data['ClienteProduto']['mes'] = Date('m');
            $this->data['ClienteProduto']['ano'] = Date('Y');
        }
		
        $meses = Comum::listMeses();
        $anos = Comum::listAnos();
		$motivos = $this->MotivoCancelamento->find('list');
		$produtos = $this->Produto->find('list');
		$agrupamento = array(1 => 'Produto', 2 => 'Motivo');
		
		$this->set(compact('meses', 'anos', 'motivos', 'produtos', 'agrupamento'));
	}

	function buscar_assinatura_cliente(){
        $this->layout = 'ajax';
        $this->loadModel('Cliente');
        $codigo_cliente = $this->data['ClienteProduto']['codigo_cliente'];
        $codigo_cliente_transportador = $this->data['ClienteProduto']['codigo_cliente_transportador'];
        $codigo_cliente_embarcador = $this->data['ClienteProduto']['codigo_cliente_embarcador'];
        $codigo_produto = $this->data['ClienteProduto']['codigo_produto'];
        $codigo_servico = $this->data['ClienteProduto']['codigo_servico'];
        $codigo_cliente_pagador = $this->Cliente->carregarClientePagadorSemBloqueio($codigo_cliente_transportador, $codigo_cliente_embarcador, $codigo_cliente, $codigo_produto);
        if(!$codigo_cliente_pagador){
        	die(json_encode(FALSE));
        }
        $servicos = $this->Cliente->clienteTemProdutoAtivo($codigo_cliente_pagador['Cliente']['codigo'], $codigo_produto, $codigo_servico);
        if($servicos > 0)
        	die(json_encode(TRUE));
        else
        	die(json_encode(FALSE));
    }

    function consulta_index(){
    	$this->data['ClienteProduto'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProduto->name);
		$produtos = $this->Produto->find('list');
		$status_produto = $this->MotivoBloqueio->find('list', array('order' => 'codigo asc'));
		$status_contrato = $this->StatusContrato->find('list');
		$this->set(compact('produtos', 'status_contrato', 'status_produto'));
    }

    function configuracoes_mopp($codigo_cliente) {
		$this->loadModel('Cliente');
		if($this->RequestHandler->isPost()) {
			$dados_cliente = $this->data;

			if ($this->Cliente->salvarConfiguracoesMopp($this->data)) {
				$this->BSession->setFlash('save_success');
			} else {
				$this->BSession->setFlash('save_error');
			}
		} else {
			$dados_cliente = $this->Cliente->carregar($codigo_cliente);
			$this->data = $dados_cliente;
		}
		$this->set(compact('codigo_cliente'));
    }



}
