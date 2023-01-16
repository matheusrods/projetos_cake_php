<?php 
class EmbarcadoresTransportadoresController extends AppController {
    public $name = 'EmbarcadoresTransportadores';
    var $uses = array('EmbarcadorTransportador','Produto','ClienteProdutoPagador', 'Cliente');

    public function index() {
        $this->data['EmbarcadorTransportador'] = $this->Filtros->controla_sessao($this->data, $this->EmbarcadorTransportador->name);
        $this->loadModel('Produto');        
        $produtos = $this->Produto->find('list');
        $this->set(compact('produtos'));
    }

    public function listagem() {
        $filtros = $this->Filtros->controla_sessao($this->data, $this->EmbarcadorTransportador->name);
        $conditions = $this->EmbarcadorTransportador->converteFiltrosEmConditions($filtros);
        $embarcadores_transportadores = array();
        
        $this->paginate['EmbarcadorTransportador'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'order' => array('ClienteEmbarcador.razao_social ASC', 'EmbarcadorTransportador.codigo'),
            'extra' => array('method' => 'listarEmbarcadorTransportadorProdutoPagador'),
        );

        $embarcadores_transportadores = $this->paginate('EmbarcadorTransportador');
        
        $this->set(compact('embarcadores_transportadores'));
    }

    private function _index_validate() {
        return !(empty($this->data['EmbarcadorTransportador']['codigo_cliente_embarcador']) && empty($this->data['EmbarcadorTransportador']['codigo_cliente_transportador']));
    }

    public function incluir($codigo_embarcador_transportador = null, $codigo_cliente_produto_pagador = null) {
    	$this->pageTitle 	= 'Cadastro de Embarcador Transportador';
    	$mensagem = null;

    	$embarcador_transportador = array();
    	$cliente_produto_pagador  = array();

    	$codigo_cliente_pagador   = null;

		if ($codigo_embarcador_transportador) {
    		$embarcador_transportador = $this->EmbarcadorTransportador->find('first',array('conditions' => array('EmbarcadorTransportador.codigo' => $codigo_embarcador_transportador)));
    	}

    	if ($codigo_cliente_produto_pagador) {
    		$cliente_produto_pagador	= $this->ClienteProdutoPagador->find('first',array('conditions' => array('ClienteProdutoPagador.codigo' => $codigo_cliente_produto_pagador)));
    		$codigo_cliente_pagador 	= $cliente_produto_pagador['ClienteProdutoPagador']['codigo_cliente_pagador'];
    	}
    	$produtos = $this->Produto->find('list',array('conditions' => array('ativo' => true)));
    	if ($this->RequestHandler->isPost()) {
            if( ($this->data['EmbarcadorTransportador']['codigo_cliente_embarcador'] == $this->data['EmbarcadorTransportador']['codigo_cliente_transportador']) && (!empty($this->data['ClienteProdutoPagador']['codigo_cliente_pagador']) || !empty($this->data['ClienteProdutoPagador']['codigo_produto']) ) ) {
                if( !empty($this->data['ClienteProdutoPagador']['codigo_cliente_pagador']) )
                    $this->ClienteProdutoPagador->invalidate('codigo_cliente_pagador', 'Não é permitido cadastrar pagador para essa combinação');
                if( !empty($this->data['ClienteProdutoPagador']['codigo_produto']) )
                    $this->ClienteProdutoPagador->invalidate('codigo_produto', 'Não é permitido cadastrar produto para essa combinação');
            } else {
                try{
                    if(!$this->data['ClienteProdutoPagador']['codigo_embarcador_transportador']) {
    					if(!$this->EmbarcadorTransportador->incluir($this->data))
    						throw new Exception();
    					else
    						$this->usuario_cliente_transportador($this->data['EmbarcadorTransportador']['codigo_cliente_transportador']);
    				} else {
    					if(!$this->ClienteProdutoPagador->save($this->data)) {
    						throw new Exception();
    					}
    				}

    				$this->BSession->setFlash('save_success');
    				$this->redirect(array('action' => 'index', 'controller' => 'EmbarcadoresTransportadores'));

    			} catch( Exception $ex ) {
    				$this->BSession->setFlash('save_error');
    				$mensagem = $ex->getMessage();
    			}
            }
    	}
    	$this->set(compact('mensagem','produtos','codigo_embarcador_transportador','embarcador_transportador','cliente_produto_pagador'));
    }
	
	private function usuario_cliente_transportador($codigo_cliente) {
		$this->loadModel('Usuario');
		$this->loadModel('Cliente');
		$this->loadModel('TipoRetorno');
		$this->loadModel('ClienteContato');
		
		require_once(ROOT . DS . 'app' . DS . 'vendors' . DS . 'buonny' . DS . 'encriptacao.php');
		$usuario = $this->Usuario->find('first', array('conditions' => array('codigo_cliente' => $codigo_cliente)));
		 
		if(!$usuario) {
			$dados = array();
			$Encriptador = new Buonny_Encriptacao();
			$dados_cliente = $this->Cliente->carregar($codigo_cliente);
			$dados['Usuario']['ativo'] = 1;
			$dados['Usuario']['codigo_uperfil'] = 21;
			$dados['Usuario']['codigo_departamento'] = 11;
			$dados['Usuario']['apelido'] = $dados_cliente['Cliente']['codigo'];
			$dados['Usuario']['nome'] = $dados_cliente['Cliente']['razao_social'];
			$dados['Usuario']['codigo_cliente'] = $dados_cliente['Cliente']['codigo'];
			$dados['Usuario']['codigo_documento'] = $dados_cliente['Cliente']['codigo_documento'];
			
			$senha = str_pad((string)mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $dados['Usuario']['senha'] = $senha;
			$this->Usuario->incluir($dados);
			$codigo_usuario = $this->Usuario->getInsertId();
			$usuario = $this->Usuario->find('first', array('conditions' => array('codigo' => $codigo_usuario)));

			$todos_contatos = '';
			$contato_cliente = $this->ClienteContato->find('all', array('fields' => array('DISTINCT descricao'), 'conditions' => array('codigo_cliente' => $codigo_cliente, 'codigo_tipo_retorno' => TipoRetorno::TIPO_RETORNO_EMAIL)));
			if(!$contato_cliente)
				return false;
			
			foreach ($contato_cliente as $contato)
				$todos_contatos .= str_replace(' ', ';', $contato['ClienteContato']['descricao']).';';
			$todos_contatos = substr($todos_contatos, 0, strlen($todos_contatos) - 1);

			App::import('Component', array('StringView', 'Mailer.Scheduler'));
			$this->StringView = new StringViewComponent();
			$this->Scheduler  = new SchedulerComponent();

			$mensagens = array('Seu login Portal Buonny: '.$dados['Usuario']['apelido'].' Sua senha: '.$senha);
			$nome_cliente = $usuario['Usuario']['nome'];

			$this->StringView->reset();
			$this->StringView->set(compact('mensagens', 'nome_cliente'));
			$content = $this->StringView->renderMail('envio_senha_email', 'default');
			$options = array(
				'from' => 'portal@buonny.com.br',
				'sent' => null,
				'to'   => $todos_contatos,
				'subject' => 'Conta de Usuario do Portal Buonny',
			);
			return $this->Scheduler->schedule($content, $options) ? true: false;
		}
	}

    public function remover($codigo)
    {
    	try {
    		if(!$this->EmbarcadorTransportador->excluir($codigo))
    		    throw new Exception();
    		
    		$this->BSession->setFlash('delete_success');
    	} catch (Exception $e) {
    		$this->BSession->setFlash('delete_error');
    	}

 		$this->redirect(array('action' => 'index', 'controller' => 'EmbarcadoresTransportadores'));
 		exit;
    }

    public function consultar_pagador_preco() {
    	$this->pageTitle 	= 'Consulta de Pagador e Preço dos Serviços';
	   	$this->data['EmbarcadorTransportador'] = $this->Filtros->controla_sessao($this->data, $this->EmbarcadorTransportador->name);

        $this->loadModel('Produto');
        $produtos = $this->Produto->find('list', array('conditions' => array('Produto.ativo' => 1)));
        $this->set(compact('produtos'));
    }

    public function listagem_pagador_preco() {
    	$filtros 		= $this->Filtros->controla_sessao($this->data, $this->EmbarcadorTransportador->name);
    	$conditions 	= $this->EmbarcadorTransportador->converteFiltrosEmConditions($filtros);

		$this->loadModel('ClienteProdutoServico2');
		$this->loadModel('Cliente');
		$this->loadModel('Produto');

        $dados_preco 	= array();
        $embarcador 	= null;
        $transportador 	= null;
        $produto 		= null;

        if(isset($conditions['EmbarcadorTransportador.codigo_cliente_embarcador']))
        	$embarcador 	= $this->Cliente->carregar($conditions['EmbarcadorTransportador.codigo_cliente_embarcador']);
        if(isset($conditions['EmbarcadorTransportador.codigo_cliente_transportador']))
			$transportador 	= $this->Cliente->carregar($conditions['EmbarcadorTransportador.codigo_cliente_transportador']);
		if(isset($conditions['ClienteProdutoPagador.codigo_produto']))
			$produto 		= $this->Produto->carregar($conditions['ClienteProdutoPagador.codigo_produto']);

		$embarcador_transportador = $this->EmbarcadorTransportador->consultaPagadorProdutoPreco($conditions);
		if (isset($embarcador_transportador[0]['ClientePagador']['codigo']) && !empty($embarcador_transportador[0]['ClientePagador']['codigo'])) {
			$dados_preco = $this->ClienteProdutoServico2->listarProdutoServicoClientePagador($embarcador_transportador[0]['ClientePagador']['codigo'], $embarcador_transportador[0]['Produto']['codigo']);

   		} else {
   			$dados_preco = $this->ClienteProdutoServico2->listarProdutoServicoClientePagador($filtros['codigo_cliente_transportador'], $filtros['codigo_produto']);
   		}

        $this->set(compact('dados_preco','embarcador','transportador','produto'));
    }

    function listar_por_cliente($codigo_cliente) {
        $this->loadModel('Cliente');
        $this->loadModel('ClienteSubTipo');
        $cliente = $this->Cliente->carregar($codigo_cliente);
        $result = null;
        if ($cliente) {
            $result = array(
                'codigo' => $cliente['Cliente']['codigo'],
                'razao_social' => $cliente['Cliente']['razao_social'],
                'tipo' => (ClienteSubTipo::subTipo($cliente['Cliente']['codigo_cliente_sub_tipo']) == ClienteSubTipo::SUBTIPO_TRANSPORTADOR) ? 'T' : 'E',
                'clientes' => array(),
            );
            $fields = array(
                'Embarcador.codigo',
                'Embarcador.razao_social',
                'Transportador.codigo',
                'Transportador.razao_social',
            );
            if ($result['tipo'] == 'T') {
                $conditions = array('codigo_cliente_transportador' => $codigo_cliente);
                $order = array('Embarcador.razao_social');
            } else {
                $conditions = array('codigo_cliente_embarcador' => $codigo_cliente);
                $order = array('Transportador.razao_social');
            }
            $this->EmbarcadorTransportador->bindModel(array('belongsTo' => array(
                'Embarcador' => array('className' => 'Cliente', 'foreignKey' => 'codigo_cliente_embarcador', 'conditions' => array('Embarcador.ativo' => 'S'), 'fields' => array('Embarcador.codigo', 'Embarcador.razao_social'), 'type' => 'INNER'),
                'Transportador' => array('className' => 'Cliente', 'foreignKey' => 'codigo_cliente_transportador', 'conditions' => array('Transportador.ativo' => 'S'), 'fields' => array('Transportador.codigo', 'Transportador.razao_social'), 'type' => 'INNER'),
            )));
            $clientes = $this->EmbarcadorTransportador->find('all', compact('conditions', 'order', 'fields'));
            if ($clientes) {
                if ($result['tipo'] == 'T') {
                    $tipo = 'Embarcador';
                } else {
                    $tipo = 'Transportador';
                }
                foreach ($clientes as $cliente) {
                    $result['clientes'][] = array('codigo' => $cliente[$tipo]['codigo'], 'razao_social' => $cliente[$tipo]['razao_social']);
                }
            }
            echo json_encode($result);
        }
        exit;
    }

    function listar_assinaturas($codigo_cliente = null) {
        $this->pageTitle = false;
        $dados = array();
        if($codigo_cliente)
            $dados = $this->EmbarcadorTransportador->listarAssinaturas($codigo_cliente);
        $this->set(compact('dados'));
    }



    function carrega_combos() {
        $this->loadModel('ClienteTipo');
        $this->loadModel('Corretora');
        $this->loadModel('Seguradora');
        $this->loadModel('Gestor');
        $this->loadModel('EnderecoRegiao');
        $clientes_tipos = $this->ClienteTipo->find('list', array('order' => 'descricao', 'cache' => 'cliente_tipo_list', 'cacheConfig' => 'short'));
        $clientes_sub_tipos = array();
        $corretoras = $this->Corretora->find('list', array('order' => 'nome'));
        $seguradoras = $this->Seguradora->find('list', array('order' => 'nome'));
        $gestores = $this->Gestor->listarNomesGestoresAtivos();
        $filiais = $this->EnderecoRegiao->listarRegioes();
        $ativo = 'Ativos';
        $this->set(compact('clientes_tipos', 'clientes_sub_tipos', 'corretoras', 'seguradoras', 'gestores','ativo','filiais'));
    }


    function carrega_combos_formulario() {
        $this->loadModel('Corretora');
        $this->loadModel('Seguradora');
        $this->loadModel('Gestor');
        $this->loadModel('EnderecoRegiao');        
        $this->loadModel('ClienteTipo');
        $this->loadModel('ClienteSubTipo');
        $this->loadModel('Corporacao');
        $this->loadModel('TipoContato');
        $this->loadModel('VEndereco');        
        
        $cliente_sub_tipo = $this->ClienteSubTipo->read('codigo_cliente_tipo', $this->data['Cliente']['codigo_cliente_sub_tipo']);
        $this->data['Cliente']['codigo_cliente_tipo'] = $cliente_sub_tipo['ClienteSubTipo']['codigo_cliente_tipo'];
        $clientes_tipos = $this->ClienteTipo->find('list', array('order' => 'descricao'));
        $clientes_sub_tipos = $this->ClienteSubTipo->listaPorTipo($this->data['Cliente']['codigo_cliente_tipo']);
        $corretoras = $this->Corretora->find('list', array('order' => 'nome'));
        $seguradoras = $this->Seguradora->find('list', array('order' => 'nome'));
        $filiais = $this->EnderecoRegiao->listarRegioes();
        $corporacoes = $this->Corporacao->find('list', array('order' => 'descricao'));
        $gestores = $this->Gestor->listarNomesGestoresAtivos();
        $tipos_contato = $this->TipoContato->listarExcetoComercial();
        
        if (isset($this->data['VEndereco']['endereco_cep'])) {
            $enderecos = $this->VEndereco->listarParaComboPorCep($this->data['VEndereco']['endereco_cep']);
        } else {
            $enderecos = array();
        }

        $this->set(compact('clientes_tipos', 'clientes_sub_tipos', 'corretoras', 'seguradoras',
            'filiais', 'corporacoes', 'gestores', 'enderecos', 'tipos_contato'));
    }


    function embarcador_transportador( ) {
        $this->pageTitle = 'Embarcadores e Transportadores';
        $usuario = $this->BAuth->user();
        if (!empty($usuario['Usuario']['codigo_cliente'])){
            $this->Filtros->limpa_sessao('EmbarcadorTransportador');
            $this->data['EmbarcadorTransportador']['codigo_cliente'] = $usuario['Usuario']['codigo_cliente'];
            $this->Filtros->controla_sessao($this->data, 'EmbarcadorTransportador');            
        } else {
            $filtros = $this->Filtros->controla_sessao($this->data, 'EmbarcadorTransportador');
            $this->Filtros->limpa_sessao('EmbarcadorTransportador');
            $this->data['EmbarcadorTransportador']['codigo_cliente'] = $filtros['codigo_cliente'];
            $this->Filtros->controla_sessao($this->data, 'EmbarcadorTransportador');            
        }        
        $this->set(compact('usuario'));
    }

 
    function listagem_embarcador_transportador() {
        $authUsuario    = $this->BAuth->user(); 
        $this->loadModel('EmbarcadorTransportador');
        $this->loadModel('ClienteProduto');
        $this->layout   = 'ajax'; 
        $filtros        = $this->Filtros->controla_sessao( $this->data, $this->EmbarcadorTransportador->name );
        $clientes       = NULL;
        $codigo_cliente 	= isset($filtros['codigo_cliente']) 	? $filtros['codigo_cliente'] 	: NULL;        
        $razao_social 		= isset($filtros['razao_social']) 		? $filtros['razao_social'] 		: NULL;
        $codigo_documento 	= isset($filtros['codigo_documento']) 	? $filtros['codigo_documento'] 	: NULL;
        if (!empty($authUsuario['Usuario']['codigo_cliente']))
            $codigo_cliente = $authUsuario['Usuario']['codigo_cliente'];        
        if( $codigo_cliente ){
            $dados_cliente = $this->Cliente->carregar($codigo_cliente);
            if( $dados_cliente ){
            	$conditions = array();
                $transportador = in_array($dados_cliente['Cliente']['codigo_cliente_sub_tipo'], array(1,7,13,19) );
                if( $transportador ){
                    $conditions['EmbarcadorTransportador.codigo_cliente_transportador'] = $codigo_cliente;
					if( $razao_social )
						$conditions['ClienteEmbarcador.razao_social like'] = '%'.$razao_social.'%';
					if( $codigo_documento )
						$conditions['ClienteEmbarcador.codigo_documento like'] = '%' . str_replace(array('.','/','-',''), '', $codigo_documento) . '%';
                    $join_conditions = array('EmbarcadorTransportador.codigo_cliente_transportador = Cliente.codigo');
                    $sub_query  = $this->ClienteProduto->find('sql', array('conditions'=>array('codigo_cliente=ClienteEmbarcador.codigo'), 'fields' =>array('codigo'), 'limit' => 1 ));            
                } else {
                    $conditions['EmbarcadorTransportador.codigo_cliente_embarcador'] = $codigo_cliente;
					if( $razao_social )
						$conditions['ClienteTransportador.razao_social like ']  = '%'.$razao_social.'%';
					if($codigo_documento)
						$conditions['ClienteTransportador.codigo_documento like'] = '%' . str_replace(array('.','/','-',''), '', $codigo_documento) . '%';
                    $join_conditions = array('EmbarcadorTransportador.codigo_cliente_embarcador = Cliente.codigo');
                    $sub_query  = $this->ClienteProduto->find('sql', array('conditions'=>array('codigo_cliente=ClienteTransportador.codigo'), 'fields' =>array('codigo'), 'limit' => 1 ));            
                }
                $fields = array(
                    'EmbarcadorTransportador.codigo',
                    'ClienteEmbarcador.codigo',
                    'ClienteEmbarcador.razao_social',
                    'ClienteEmbarcador.codigo_documento',
                    'ClienteTransportador.codigo',
                    'ClienteTransportador.razao_social',
                    'ClienteTransportador.codigo_documento',
                    "(CASE WHEN ($sub_query) > 0 THEN 0 ELSE 1 END) AS permite_edicao"
                );
                $this->paginate['EmbarcadorTransportador'] = array(
                    'conditions' => $conditions,
                    'fields'     => $fields,
                    'limit' => 50,
                    'order' => array('ClienteEmbarcador.razao_social ASC', 'EmbarcadorTransportador.codigo'),
                    'group' => $fields,
                );
                $clientes = $this->paginate('EmbarcadorTransportador');
            } else {
                $codigo_cliente = NULL;
            }
        }        
        $this->set(compact('clientes', 'transportador', 'codigo_cliente')); 
    } 

    function consulta_para_incluir( ) { 
        $usuario = $this->BAuth->user();
        if (!empty($usuario['Usuario']['codigo_cliente'])){
            $codigo_cliente = $usuario['Usuario']['codigo_cliente'];
        } else {
            $filtros = $this->Filtros->controla_sessao( $this->data, 'EmbarcadorTransportador' );
            $codigo_cliente = $filtros['codigo_cliente'];
        }
        $dados_cadastrante  = $this->Cliente->carregar( $codigo_cliente );
        $transportador      = in_array($dados_cadastrante['Cliente']['codigo_cliente_sub_tipo'], array(1,7,13,19) );        
        $this->pageTitle    = 'Vincular '. ( !$transportador ? 'Transportador':'Embarcador');
        if ( $this->RequestHandler->isPost()) {
            $this->Filtros->controla_sessao( $this->data, 'Cliente' );
            $this->redirect( array( 'action' => 'incluir_embarcador_transportador' ));
        }
    }       

    function incluir_embarcador_transportador( ) {
        $this->loadModel('Cnae');
        $this->ClienteProduto = ClassRegistry::init('ClienteProduto');
        if ( $this->data ) {
            $codigo_cliente_cadastrante = $this->data['Cliente']['codigo_cliente_cadastrante'];
            $result = $this->Cliente->incluir_embarcador_transportador( $this->data );
            if( !empty($result['sucess']) ){
                $this->BSession->setFlash('save_success');
                $this->Filtros->limpa_sessao($this->Cliente->name);
                $this->data['Cliente']['codigo_cliente'] = $codigo_cliente_cadastrante;
                $this->Filtros->controla_sessao( $this->data, 'EmbarcadorTransportador' );              
                $this->redirect( array( 'action' => 'embarcador_transportador' ));
            } else {
                $edit_mode = FALSE;
                $this->BSession->setFlash(array(MSGT_ERROR, $result['errors'][0] ));                
                if( !empty($this->data['Cliente']['codigo']) ){
                    if( $this->ClienteProduto->find('count', array('conditions'=>array('codigo_cliente'=> $this->data['Cliente']['codigo'] ))) ){
                        $edit_mode = TRUE;
                    }
                }   
            }           
        } else {
            $filtros = $this->Filtros->controla_sessao( $this->data, 'EmbarcadorTransportador' );
            $dados_consulta_inclusao = $this->Filtros->controla_sessao( $this->data, 'Cliente' );
            $codigo_documento = isset($dados_consulta_inclusao['codigo_documento'])? str_replace(array('.','/','-',''), '', $dados_consulta_inclusao['codigo_documento']):NULL;
            $authUsuario = $this->BAuth->user();
            $edit_mode   = FALSE;
            if(!empty($authUsuario['Usuario']['codigo_cliente'])){
                $codigo_cliente_cadastrante = $authUsuario['Usuario']['codigo_cliente'];
            } else {
                $codigo_cliente_cadastrante = $filtros['codigo_cliente'];
            }
            if( !$codigo_cliente_cadastrante ){
                $this->BSession->setFlash(array(MSGT_ERROR, 'Erro ao identificar o Cliente.'));
                $this->redirect( array( 'action' => 'embarcador_transportador' ));
            }
            $dados_cliente = $this->Cliente->find('first', array('conditions'=>array('codigo_documento' =>  $codigo_documento )));      
            if( !empty( $dados_cliente )){
                if( $this->ClienteProduto->find('count', array('conditions'=>array('codigo_cliente'=> $dados_cliente['Cliente']['codigo'] ))) ){
                    $edit_mode = TRUE;
                }
                $this->data = $this->Cliente->carregarParaEdicao($dados_cliente['Cliente']['codigo']);
                $cnae = $this->Cnae->find('first', array('conditions'=>array('cnae'=>$this->data['Cliente']['cnae'])));
                if ($cnae )
                    $this->data = array_merge($this->data, $cnae);
            } else {//Inclusao 
                $this->data['Cliente']['codigo_cliente_sub_tipo']   = NULL;
                $this->data['Cliente']['codigo_documento']          = $codigo_documento;
            }
            $this->data['Cliente']['codigo_cliente_cadastrante'] = $codigo_cliente_cadastrante;
        }        
        $dados_cadastrante  = $this->Cliente->carregar( $codigo_cliente_cadastrante );
        $transportador      = in_array($dados_cadastrante['Cliente']['codigo_cliente_sub_tipo'], array(1,7,13,19) );        
        $this->pageTitle    = 'Vincular '. ( !$transportador ? 'Transportador':'Embarcador');

        $this->data['Cliente']['codigo_cliente_sub_tipo']   = NULL;
        $this->set(compact('edit_mode'));
        $this->carrega_combos_formulario();
    }

    function editar_embarcador_transportador( $codigo_cliente ) {
        $this->loadModel('Cnae');
        $this->pageTitle = 'Atualizar Cliente';
        $this->ClienteProduto = ClassRegistry::init('ClienteProduto');
        $authUsuario = $this->BAuth->user();    
        //Se o cliente tiver o produto nao pode editar
        if( $this->ClienteProduto->find('count', array('conditions'=>array('codigo_cliente'=> $codigo_cliente ))) ){
            $this->BSession->setFlash(array(MSGT_ERROR, 'Sem autorização para alterar os dados do Cliente.'));
            $this->redirect( array( 'action' => 'embarcador_transportador' ));
        }
        if ( $this->data ) {
            if ($this->Cliente->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect( array( 'action' => 'embarcador_transportador' ));
            } else {
                $this->BSession->setFlash('save_error');
            }
        } else {
            if( !empty($authUsuario['Usuario']['codigo_cliente']) ){
                $codigo_cliente_cadastrante = $authUsuario['Usuario']['codigo_cliente'];
            } else {
                $filtro = $this->Filtros->controla_sessao( $this->data, 'EmbarcadorTransportador' );
                $codigo_cliente_cadastrante = $filtro['codigo_cliente'];
            }
            $dados_cadastrante  = $this->Cliente->carregar( $codigo_cliente_cadastrante );
            $transportador      = in_array($dados_cadastrante['Cliente']['codigo_cliente_sub_tipo'], array(1,7,13,19) );
            $conditions         = array();
            if( $transportador ){
                $conditions['codigo_cliente_transportador'] = $codigo_cliente_cadastrante;
                $conditions['codigo_cliente_embarcador']    = $codigo_cliente;
            } else {
                $conditions['codigo_cliente_transportador'] = $codigo_cliente;
                $conditions['codigo_cliente_embarcador']    = $codigo_cliente_cadastrante;
            }
            if( !$this->EmbarcadorTransportador->find('count', compact('conditions')) ){
                $this->BSession->setFlash(array(MSGT_ERROR, 'Erro ao identificar o Cliente.'));
                $this->redirect( array( 'action' => 'embarcador_transportador' ));
            }
            $this->data = $this->Cliente->carregarParaEdicao( $codigo_cliente );
        }
        $cnae = $this->Cnae->find('first', array('conditions'=>array('cnae'=>$this->data['Cliente']['cnae'])));
        if ($cnae )
            $this->data = array_merge($this->data, $cnae);
        $edit_mode=false;
        $this->set(compact('edit_mode'));
        $this->carrega_combos_formulario();     
    }

    function excluir_embarcador_transportador( $codigo_cliente ){
        $filtro = $this->Filtros->controla_sessao( $this->data, 'EmbarcadorTransportador' );
        $codigo_cliente_cadastrante = $filtro['codigo_cliente'];
        $dados_cadastrante  = $this->Cliente->carregar( $filtro['codigo_cliente'] );
        $transportador      = in_array($dados_cadastrante['Cliente']['codigo_cliente_sub_tipo'], array(1,7,13,19) );
        $conditions         = array();
        if( $transportador ){
            $conditions['codigo_cliente_transportador'] = $codigo_cliente_cadastrante;
            $conditions['codigo_cliente_embarcador']    = $codigo_cliente;
        } else {
            $conditions['codigo_cliente_transportador'] = $codigo_cliente;
            $conditions['codigo_cliente_embarcador']    = $codigo_cliente_cadastrante;
        }
        $dadosEmbTransp = $this->EmbarcadorTransportador->find('first', compact('conditions'));
        if( !empty($dadosEmbTransp['EmbarcadorTransportador']['codigo'])){
            if( $this->EmbarcadorTransportador->excluir( $dadosEmbTransp['EmbarcadorTransportador']['codigo'] ) ){
                $this->BSession->setFlash('delete_success');
            } else {
                $this->BSession->setFlash('delete_error');
            }
        } else {
            $this->BSession->setFlash('delete_error');
        }
        $this->redirect( array( 'action' => 'embarcador_transportador' ));
    }

}

?>