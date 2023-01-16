<?php
class ClientesProdutosContratosController extends AppController {

    public $name = 'ClientesProdutosContratos';
    public $components = array('Filtros', 'RequestHandler');
    public $helpers = array('Html', 'Ajax');
    public $uses = array('Cliente', 'ClienteProdutoContrato', 'ClienteTipo', 'Corretora', 'Seguradora', 'Gestor', 'StatusContrato', 'ClienteProduto', 'ContratoModelo', 'ContratoModeloLog', 'ClienteEndereco', 'StatusContrato');
    public $ClienteProdutoContrato;

    public function index() {
        $this->pageTitle = 'Contratos dos Clientes';
        $this->carrega_combos();
        $this->data['ClienteProdutoContrato'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProdutoContrato->name);
    }

    public function carrega_combos() {
        $clientes_tipos = $this->ClienteTipo->find('list', array('order' => 'descricao'));
        $clientes_sub_tipos = array();
        $corretoras = $this->Corretora->find('list', array('order' => 'nome'));
        $seguradoras = $this->Seguradora->find('list', array('order' => 'nome'));
        $gestores = $this->Gestor->listarNomesGestoresAtivos();
        $this->set(compact('clientes_tipos', 'clientes_sub_tipos', 'corretoras', 'seguradoras', 'gestores'));
    }
    
    public function gerenciar($codigo_cliente) {
        $this->pageTitle = 'Contratos de Produtos do Cliente';
        $cliente = $this->Cliente->carregar($codigo_cliente);
        $this->set(compact('cliente'));
    }
    
    public function listagem_contratos($destino){
    	$this->layout = 'ajax'; 
		$clientes;
		$this->ClienteProdutoContrato 		  = ClassRegistry::init('ClienteProdutoContrato');
		$this->data['ClienteProdutoContrato'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProdutoContrato->name);
		$filtros 							  = $this->data['ClienteProdutoContrato'];
        $conditions         = $this->Cliente->converteFiltroEmCondition($filtros);

		if(!isset($filtros['contratos']) || $filtros['contratos'] == 0 ){
			$joins 				= $this->Cliente->subQueryParaUltimaAtualizacao($filtros);
			$total_contratos	= $this->Cliente->find('count',compact('joins','conditions'));
			$this->paginate['Cliente'] = array(
				'recursive' 	=> 1,
				'contain' 		=> array('ClienteTipo', 'ClienteSubTipo'),
				'joins' 		=> $joins,
				'conditions' 	=> $conditions,
				'limit' 		=> 50,
				'order' 		=> 'Cliente.codigo',
				'group by' 		=> 'ClienteLog.codigo_cliente'
			);
			
			$clientes = $this->paginate('Cliente');
			$this->set(compact('clientes', 'destino','total_contratos')); 
		}
		if(isset($filtros['contratos']) && $filtros['contratos']==1){
            $conditions = array_merge($conditions, array('Cliente.codigo NOT IN 
                (SELECT Distinct codigo_cliente FROM RHHealth.dbo.cliente_produto_contrato AS ClienteProdutoContrato
                INNER JOIN RHHealth.dbo.cliente_produto AS ClienteProduto 
                    ON ClienteProdutoContrato.codigo_cliente_produto = ClienteProduto.codigo
                INNER JOIN RHHealth.dbo.cliente AS Cliente 
                    ON ClienteProduto.codigo_cliente = Cliente.codigo)'));	
            $total_contratos = $this->Cliente->find('count',array('conditions' => $conditions));

	    	$this->paginate['Cliente'] = array(
	    		'contratos' 	=> 1,
	    		'conditions' 	=> $conditions,
		   		'limit' => 50,
		   		'order'	=> 'Cliente.codigo',
	    	);
	    	$clientes = $this->paginate('Cliente');
	    	$this->set(compact('clientes','destino','total_contratos'));
		}
    }

    public function listagem($codigo_cliente) {
        $VtigerAccount = ClassRegistry::init('VtigerAccount');
        $this->Cliente->recursive = -1;
        $cnpj = $this->Cliente->read('codigo_documento', $codigo_cliente);
        $cnpj = $cnpj['Cliente']['codigo_documento'];
        
        $documentos_cliente = $VtigerAccount->obterDocumentos($cnpj);
        

        $clientes_produtos = $this->ClienteProduto->carregarClienteProduto($codigo_cliente);
        $this->set(compact('clientes_produtos', 'documentos_cliente'));
    }

    public function listagem_contratos_por_codigo(){
        $this->pageTitle = 'Contratos de Produtos do Cliente';
        $this->data['ClienteProdutoContrato'] = $this->Filtros->controla_sessao($this->data, $this->ClienteProdutoContrato->name);
        if (!empty($this->data) && !empty($this->data['ClienteProdutoContrato']['codigo_cliente'])) {
            $this->loadModel('Cliente');
            $cliente = $this->Cliente->carregar($this->data['ClienteProdutoContrato']['codigo_cliente']);

            if( $cliente ){
                $codigo_cliente = $cliente['Cliente']['codigo'];
                // $VtigerAccount = ClassRegistry::init('VtigerAccount');
                $cnpj = $this->Cliente->read('codigo_documento', $codigo_cliente);
                $cnpj = $cnpj['Cliente']['codigo_documento'];
        
                $clientes_produtos = $this->ClienteProduto->carregarClienteProduto($codigo_cliente);
                $this->set(compact('clientes_produtos','cliente'));
           }
        }
    }
    
    // *esta associada ao modulo Cliente na vizualiação de Contratos
    public function visualiza_cliente($codigo_cliente) {
        $VtigerAccount = ClassRegistry::init('VtigerAccount');
        $this->Produto = ClassRegistry::init('Produto');
        $this->Cliente->recursive = -1;
        $cnpj = $this->Cliente->read('codigo_documento', $codigo_cliente);
        $cnpj = $cnpj['Cliente']['codigo_documento'];
        
        $documentos_cliente = $VtigerAccount->obterDocumentos($cnpj);
        // adicionado  para vizualizar os produtos do cliente  
        
        if (empty($codigo_cliente)) return;
        
        $produtos = $this->ClienteProduto->produtosServicosProfissionaisPorCliente($codigo_cliente);
        $this->set(compact('produtos', 'codigo_cliente'));
        //fim

        $clientes_produtos = $this->ClienteProduto->carregarClienteProduto($codigo_cliente);
        $this->set(compact('clientes_produtos', 'documentos_cliente'));
    	$diretorio 		= DIR_CONTRATOS;
    	$lista_arquivos = array();
    	$nome_produto 	= array();
    	$i = 0;
    	
    	foreach ($produtos as $key => $value) {
    		$i++;
    		$codigo 			= $produtos[$key]['Produto']['codigo'];
    		$nome_produto[$i] 	= $produtos[$key]['Produto']['descricao'];
    		$arquivos			= glob( DIR_CONTRATOS.$codigo_cliente.'_'.$codigo.'_arquivo_contrato.*');
    	    foreach ($arquivos as $arquivo) {
    	    	$separa 		= explode(DS,$arquivo);
    	    	$localiza 		= array_pop($separa);
				$lista_arquivos[$i]	= $localiza;
    	    	$this->set(compact('nome_produto', 'lista_arquivos'));
    		}
	   	}
    }

    public function incluir($codigo_cliente_produto) {
        $this->pageTitle = 'Criar Associação Modelo de Contrato';

        if ($this->RequestHandler->isPost()) {
            if ($this->ClienteProdutoContrato->incluir($this->data))
                $this->BSession->setFlash('save_success');
            else
                $this->BSession->setFlash('save_error');
        } else {
            $numero_contrato = $this->ClienteProdutoContrato->gerarNumeroContrato($codigo_cliente_produto);
            $this->data['ClienteProdutoContrato']['numero'] = $numero_contrato;
            $this->data['ClienteProdutoContrato']['codigo_cliente_produto'] = $codigo_cliente_produto;
        }
        $status_contrato = $this->StatusContrato->find('list');
        $modelo_contrato = $this->ContratoModelo->find('list');
        $this->ClienteProduto->bindLazyCliente();
        $cliente_produto = $this->ClienteProduto->find('first', array(
            'conditions' => array(
                'ClienteProduto.codigo' => $codigo_cliente_produto
            )
                ));
        $this->set(compact('cliente_produto', 'status_contrato', 'modelo_contrato'));
    }

    public function editar($codigo) {
        $this->pageTitle = 'Editar Associação Modelo de Contrato';

        if ($this->RequestHandler->isPut()) {
            $result = $this->ClienteProdutoContrato->atualizarregistro($this->data);
            if ($result) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }

        $cliente_produto_contrato = $this->ClienteProdutoContrato->find('first', array(
            'conditions' => array(
                'ClienteProdutoContrato.codigo' => $codigo
            )
                ));

        $contrato = $this->carregaValorComboModeloContrato($cliente_produto_contrato['ClienteProdutoContrato']['codigo_contrato_modelo_log']);

        $this->ClienteProduto->bindLazyCliente();
        $cliente_produto = $this->ClienteProduto->find('first', array(
            'conditions' => array(
                'ClienteProduto.codigo' => $cliente_produto_contrato['ClienteProduto']['codigo']
            )
                ));

        $status_contrato = $this->StatusContrato->find('list');
        $modelo_contrato = $this->ContratoModelo->find('list');

        $this->set(compact('cliente_produto_contrato', 'cliente_produto', 'status_contrato', 'modelo_contrato'));
        $this->data = $cliente_produto_contrato;
        $this->data['ClienteProdutoContrato']['codigo_contrato_modelo_log'] = $contrato;
    }

    public function carregaValorComboModeloContrato($codigo_modelo_log) {
        $codigo = $this->ContratoModeloLog->find('first', array('fields' => 'ContratoModeloLog.codigo_contrato_modelo',
            'conditions' => array('ContratoModeloLog.codigo' => $codigo_modelo_log)));

        $codigo = $codigo['ContratoModeloLog']['codigo_contrato_modelo'];

        return $codigo;
    }
    
    public function atualizar($codigo_cliente_produto = null) {
        $this->ClienteProduto->bindLazyCliente();
        $this->MotivoBloqueio = ClassRegistry::init('MotivoBloqueio');
        
        $motivos_bloqueio = $this->MotivoBloqueio->find('list', array('order' => 'codigo'));
        $cliente_produto = $this->ClienteProduto->findByCodigo($codigo_cliente_produto);
        $codigo_cliente = $cliente_produto['Cliente']['codigo'];
        $codigo_produto = $cliente_produto['Produto']['codigo'];
        $this->set(compact('codigo_cliente', 'codigo_cliente_produto', 'cliente_produto', 'motivos_bloqueio'));
        
        if(!empty($this->data)) {
            $dados = $this->data;
            unset($dados['ClienteProdutoContrato']['arquivo_contrato']);
            unset($dados['ClienteProdutoContrato']['arquivo_contrato_social']);
            unset($dados['ClienteProdutoContrato']['codigo_motivo_bloqueio']);
            $dados['ClienteProdutoContrato']['codigo_cliente_produto'] = $cliente_produto['ClienteProduto']['codigo'];
            $this->ClienteProdutoContrato->set($dados);
            if($this->ClienteProdutoContrato->validates()) {
                $upload = true;
                if (!empty($this->data['ClienteProdutoContrato']['arquivo_contrato']['name'])) {
                    $arquivo_contrato_nome = strtolower($this->data['ClienteProdutoContrato']['arquivo_contrato']['name']);
                    if (strpos($arquivo_contrato_nome, ".jpg") > 0 || strpos($arquivo_contrato_nome, ".pdf") > 0) {
                        preg_match("/(\..*){1}$/i", $this->data['ClienteProdutoContrato']['arquivo_contrato']['name'], $ext);
                        $nome_arquivo = $codigo_cliente . '_' . $codigo_produto . '_arquivo_contrato' . $ext[0];
                        if(!is_dir(DIR_CONTRATOS_PRODUTOS.$codigo_cliente.DS))
                            mkdir(DIR_CONTRATOS_PRODUTOS.$codigo_cliente.DS);
                        $destino = DIR_CONTRATOS_PRODUTOS .$codigo_cliente.DS. $nome_arquivo;
                        if (!move_uploaded_file($_FILES['data']['tmp_name']['ClienteProdutoContrato']['arquivo_contrato'], $destino)) {
                            $upload = false;
                            $this->ClienteProdutoContrato->invalidate('arquivo_contrato', 'Informe arquivo .jpg ou .pdf de até 10MB');
                        }
                    } else {
                        $upload = false;
                        $this->ClienteProdutoContrato->invalidate('arquivo_contrato', 'Informe arquivo .jpg ou .pdf de até 10MB');
                    }
                }
                if (!empty($this->data['ClienteProdutoContrato']['arquivo_contrato_social']['name'])) {
                    $arquivo_contrato_social_nome = strtolower($this->data['ClienteProdutoContrato']['arquivo_contrato_social']['name']);
                    if (strpos($arquivo_contrato_social_nome, ".jpg") > 0 || strpos($arquivo_contrato_social_nome, ".pdf") > 0) {
                        preg_match("/(\..*){1}$/i", $this->data['ClienteProdutoContrato']['arquivo_contrato_social']['name'], $ext);
                        $nome_arquivo = $codigo_cliente . '_' . $codigo_produto . '_arquivo_contrato_social' . $ext[0];
                         if(!is_dir(DIR_CONTRATOS_PRODUTOS.$codigo_cliente.DS))
                            mkdir(DIR_CONTRATOS_PRODUTOS.$codigo_cliente.DS);
                        $destino = DIR_CONTRATOS_PRODUTOS .$codigo_cliente.DS. $nome_arquivo;
                        if (!move_uploaded_file($_FILES['data']['tmp_name']['ClienteProdutoContrato']['arquivo_contrato_social'], $destino)) {
                            $upload = false;
                            $this->ClienteProdutoContrato->invalidate('arquivo_contrato_social', 'Informe arquivo .jpg ou .pdf de até 10MB');
                        }
                    } else {
                        $upload = false;
                        $this->ClienteProdutoContrato->invalidate('arquivo_contrato_social', 'Informe arquivo .jpg ou .pdf de até 10MB');
                    }
                }
                if($upload) {
                    $salva_dados = true;
                    if(isset($this->data['ClienteProdutoContrato']['codigo_motivo_bloqueio']) && !empty($this->data['ClienteProdutoContrato']['codigo_motivo_bloqueio'])) {
                        if($this->data['ClienteProdutoContrato']['codigo_motivo_bloqueio'] != $cliente_produto['ClienteProduto']['codigo_motivo_bloqueio']) {
                            $cliente_produto_atualizado['ClienteProduto']['codigo'] = $cliente_produto['ClienteProduto']['codigo'];
                            $cliente_produto_atualizado['ClienteProduto']['codigo_motivo_bloqueio'] = $this->data['ClienteProdutoContrato']['codigo_motivo_bloqueio'];
                            $cliente_produto_atualizado['ClienteProduto']['data_faturamento'] = $cliente_produto['ClienteProduto']['data_faturamento'];
                            $cliente_produto_atualizado['ClienteProduto']['codigo_cliente'] = $cliente_produto['ClienteProduto']['codigo_cliente'];
                            if(!$this->ClienteProduto->save($cliente_produto_atualizado)) {
                                $salva_dados = false;
                                $this->ClienteProduto->invalidate('codigo_motivo_bloqueio', 'Não foi possível alterar o status do produto');
                            }
                        }
                    }
                    if($salva_dados) {
                        if (!empty($dados['ClienteProdutoContrato']['codigo'])) {
                            $result = $this->ClienteProdutoContrato->atualizarRegistro($dados);
                        } else {
                            $result = $this->ClienteProdutoContrato->incluir($dados);
                        }
                        if($result) {
                            $this->BSession->setFlash('save_success');
                            $this->redirect(array('action' => 'gerenciar', $codigo_cliente));
                        } else {
                            $this->BSession->setFlash('save_error');
                        }
                    } else {
                        $this->BSession->setFlash('save_error');
                    }
                } else {
                   $this->BSession->setFlash('save_error');
                }
            } else {
                if (!empty($this->data['ClienteProdutoContrato']['codigo'])) {
                    $result = $this->ClienteProdutoContrato->atualizarRegistro($dados);
                } else {
                    $result = $this->ClienteProdutoContrato->incluir($dados);
                }
                if (!empty($this->data['ClienteProdutoContrato']['arquivo_contrato']['name']) && !(strpos($this->data['ClienteProdutoContrato']['arquivo_contrato']['name'], ".jpg") > 0 || strpos($this->data['ClienteProdutoContrato']['arquivo_contrato']['name'], ".pdf") > 0)) {
                    $this->ClienteProdutoContrato->invalidate('arquivo_contrato', 'Informe arquivo .jpg ou .pdf');
                }
                if (!empty($this->data['ClienteProdutoContrato']['arquivo_contrato_social']['name']) && !(strpos($this->data['ClienteProdutoContrato']['arquivo_contrato_social']['name'], ".jpg") > 0 || strpos($this->data['ClienteProdutoContrato']['arquivo_contrato_social']['name'], ".pdf") > 0)) {
                    $this->ClienteProdutoContrato->invalidate('arquivo_contrato_social', 'Informe arquivo .jpg ou .pdf');
                }
            }
        } else {
            $this->data = $this->ClienteProdutoContrato->find('first', array('fields' => array('ClienteProdutoContrato.*', 'Cliente.codigo', 'Cliente.razao_social', 'Produto.descricao'),  'conditions' => array('ClienteProdutoContrato.codigo_cliente_produto' => $codigo_cliente_produto)));
            $this->data['ClienteProdutoContrato']['codigo_cliente_produto'] = $codigo_cliente_produto;
            $this->data['ClienteProdutoContrato']['numero'] = $this->ClienteProdutoContrato->gerarNumeroContrato($codigo_cliente_produto);
        }
    }

    public function download($codigo_cliente_produto, $chave = 'arquivo_contrato') {
        $cliente_produto = $this->ClienteProduto->findByCodigo($codigo_cliente_produto);
        $codigo_cliente = $cliente_produto['ClienteProduto']['codigo_cliente'];
        $codigo_produto = $cliente_produto['ClienteProduto']['codigo_produto'];

        $arquivo = end(glob(DIR_CONTRATOS . $codigo_cliente . '_' . $codigo_produto . '_' . $chave . '.*'));
        if (!file_exists($arquivo)) {
            exit;
        }

        header(sprintf('Content-Disposition: attachment; filename="%s"', basename($arquivo)));
        header("Content-type: ".mime_content_type($arquivo));
        header('Content-Transfer-Encoding: binary');
        header('Pragma: no-cache');

        ob_clean();
        flush();
        echo file_get_contents($arquivo);
        exit;
    }
  
    public function tratarModeloDoContrato() {
        $this->layout = false;
        $this->autoRender = false;
        if(!empty($this->data)) {
            $dados = $this->ClienteProdutoContrato->obterDadosParaContrato($this->data);
            if(empty($dados['ClienteProdutoContrato']['data_contrato'])) {
                $this->BSession->setFlash(array(MSGT_ERROR, 'Não foi possível gerar o PDF do contrato - Data do contrato não informada'));
                $this->redirect(array('controller' => 'clientes_produtos_contratos', 'action' => 'gerenciar', $this->data['ClienteProdutoContrato']['codigo']));
            }
            if(empty($dados['ClienteProdutoContrato']['codigo_contrato_modelo_log'])) {
                $this->BSession->setFlash(array(MSGT_ERROR, 'Não foi possível gerar o PDF do contrato - Modelo não informado'));
                $this->redirect(array('controller' => 'clientes_produtos_contratos', 'action' => 'gerenciar', $this->data['ClienteProdutoContrato']['codigo']));
            }
            if(!empty($dados)) {
                $endereco = $this->ClienteProduto->obterEnderecoDoClientePorCodigoClienteProduto($dados['ClienteProdutoContrato']['codigo_cliente_produto']);
                if(empty($endereco)) {
                    $this->BSession->setFlash(array(MSGT_ERROR, 'Não foi possível gerar o PDF do contrato - Endereco faltando'));
                    $this->redirect(array('controller' => 'clientes_produtos_contratos', 'action' => 'gerenciar', $this->data['ClienteProdutoContrato']['codigo']));
                }
            }
        }
        
        if (isset($dados) && !empty($dados)) {
            $codigo_modelo = $dados['ClienteProdutoContrato']['codigo_contrato_modelo_log'];
            $modelo = $this->ContratoModeloLog->find('first', array('conditions' => array('codigo_contrato_modelo' => $codigo_modelo), 'fields' => array('ContratoModeloLog.modelo')));
            $numero_contrato = $dados['ClienteProdutoContrato']['numero'];
            
            $padrao = array(
                'razao_social' => '/##razao_social##/',
                'endereco_tipo' => '/##endereco_tipo##/',
                'endereco_logradouro' => '/##endereco_logradouro##/',
                'numero' => '/##numero##/',
                'endereco_bairro' => '/##endereco_bairro##/',
                'endereco_cidade' => '/##endereco_cidade##/',
                'endereco_estado' => '/##endereco_estado##/',
                'cnpj' => '/##cnpj##/',
                'data_contrato' => '/##data_contrato##/'
            );
            
            $valores = array(
                'razao_social' => $dados['Cliente']['razao_social'],
                'endereco_tipo' => $endereco['ClienteEndereco']['codigo_tipo_contato'],
                'endereco_logradouro' => $endereco['ClienteEndereco']['logradouro'],
                'numero' => $endereco['ClienteEndereco']['numero'],
                'endereco_bairro' => $endereco['ClienteEndereco']['bairro'],
                'endereco_cidade' => $endereco['ClienteEndereco']['cidade'],
                'endereco_estado' => $endereco['ClienteEndereco']['estado_descricao'],
                'cnpj' => Comum::formatarDocumento($dados['Cliente']['codigo_documento']),
                'data_contrato' => Comum::dataPorExtenso($dados['ClienteProdutoContrato']['data_contrato'])
            );
            
            $contrato = preg_replace($padrao, $valores, $modelo['ContratoModeloLog']['modelo']);
            $this->gerarPDFdoContrato($contrato, $numero_contrato);
        } else {
            return false;
        }
    }

    public function gerarPDFdoContrato($contrato = null, $numero_contrato = null) {
        require_once APP . 'vendors' . DS . 'dompdf' . DS . 'dompdf_config.inc.php';
            
        $titulo_do_pdf = $numero_contrato ? 'contrato_' . $numero_contrato . '.pdf' : 'contrato.pdf';
        
        $dompdf = new DOMPDF();
        $dompdf->load_html($contrato);
        $dompdf->set_paper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($titulo_do_pdf, array('Attachment' => 0));
    }

    public function atualizacao_contratos() {
        $this->pageTitle = 'Atualização de Contratos';
		$this->loadModel('Produto');
		$this->loadModel('Igpm');
		$igpm_acumulado = $this->Igpm->ultimoIGPM();
		$produtos = $this->Produto->listar();
		$produtos[0] = 'TODOS';
		ksort($produtos);
		
		$filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteProdutoContrato->name);

		if($filtros) {
			$this->data['ClienteProdutoContrato'] = $filtros;
		} else {
			$ano = date('Y');
			$mes = date('m');
			$this->data['ClienteProdutoContrato']['data_inicial']	= date('01/m/Y H:i:s', strtotime($ano.'-'.$mes.'-01 00:00:00'));
			$this->data['ClienteProdutoContrato']['data_final']		= date(cal_days_in_month(CAL_GREGORIAN, $mes, $ano).'/m/Y H:i:s', strtotime($ano.'-'.$mes.'-01 23:59:59'));
			$this->data['ClienteProdutoContrato']['igpm'] 			= $igpm_acumulado;
			$this->data['ClienteProdutoContrato']['codigo_produto'] = 0;
		}
		
		$this->set(compact('produtos', 'igpm_acumulado'));
    }
	
	public function atualizacao_contratos_listagem() {
        $this->layout = 'ajax';
		$this->loadModel('Produto');
        $filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteProdutoContrato->name);
		$produtos = $this->Produto->listar();
		$produtos[0] = 'TODOS';
		ksort($produtos);
		
        $conditions = $this->ClienteProdutoContrato->converteFiltroEmCondition($filtros);

        $this->paginate['ClienteProdutoContrato'] = array('conditions' => $conditions);
        $contratos = $this->paginate('ClienteProdutoContrato');
		
		//if(!empty($filtros)) {
			$total_contratos	 = count($contratos);
			$igpm_acumulado		 = $filtros['igpm'];
			$codigo_produto		 = $filtros['codigo_produto'];
			$produto_selecionado = (!empty($produtos[$codigo_produto]))? $produtos[$codigo_produto] : null;
			
			$this->set(compact('filtros', 'produto_selecionado', 'total_contratos', 'igpm_acumulado', 'contratos'));
     //   }
	}
	
	public function atualizar_contratos() {
		$this->loadModel('ClienteProdutoContrato');
		
		$filtros = $this->Filtros->controla_sessao($this->data, $this->ClienteProdutoContrato->name);
		$this->BSession->setFlash('save_error');
		if (!empty($filtros)) {
			if ($this->ClienteProdutoContrato->atualizarContratos($filtros)) 
				$this->BSession->setFlash('save_success');
		} 
		$this->redirect(array('action' => 'atualizacao_contratos'));
		$this->render(false);
	}
    
    public function excluir() {
        $this->render(false);
        if($this->RequestHandler->isPost()) {
            $arquivo = $_POST['arquivo'];
            $codigo_cliente = $_POST['codigo_cliente'];
            $caminho_completo = end(glob(DIR_CONTRATOS_PRODUTOS.DS.$codigo_cliente.DS.$arquivo));
            if (is_file($caminho_completo)) {
                if(unlink($caminho_completo)) {
                    $this->BSession->setFlash('delete_success');
                    return 1;
                } else {
                    $this->BSession->setFlash('delete_error');
                    return 0;
                }
            }
         }
    }
}
