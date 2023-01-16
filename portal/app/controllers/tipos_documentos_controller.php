<?php
class TiposDocumentosController extends AppController {
    public $name = 'TiposDocumentos';
	public $helpers = array('BForm', 'Html', 'Ajax');
    var $uses = array(
    	'TipoDocumento', 
    	'Usuario', 
    	'PropostaCredenciamento', 
    	'PropostaCredDocumento', 
    	'PropostaCredExame', 
    	'StatusPropostaCred', 
    	'FornecedorDocumento',
    	'Uperfil'
    );
    
	var $components = array('RequestHandler', 'Session');
	public function beforeFilter() {
		parent::beforeFilter();
		$this->BAuth->allow(array('verifica_documentos_aprovados', 'verifica_proposta_aprovada', 'enviar', 'remove_arquivo', 'aprova_arquivo', 'desfazer_aprovar'));
	}
    
   
    public function incluir() {
        $this->pageTitle = 'Incluir Documento para Proposta';
        
        if($this->RequestHandler->isPost()) {
        	$this->data['TipoDocumento']['status'] = '1';
        	$this->data['TipoDocumento']['codigo_status_proposta_credenciamento'] = '7';
        	$this->data['TipoDocumento']['proximo_status_proposta_credenciamento'] = '8';
        	
            if ($this->TipoDocumento->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
    }
    
    public function excluir($id) {
    	$tipos_documentos = $this->TipoDocumento->read(null, $id);
    	$tipos_documentos['TipoDocumento']['status'] = 0;
		if ($this->TipoDocumento->atualizar($tipos_documentos)) {
			$this->BSession->setFlash('save_success');
		} else {
			$this->BSession->setFlash('save_error');
		}
		$this->redirect(array('action' => 'index'));		
    }    
    
    public function index() {
    	
        // $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, $this->TipoDocumento->name);
        $conditions = $this->TipoDocumento->converteFiltroEmCondition($filtros);
        $this->paginate['TipoDocumento'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'TipoDocumento.ordem_exibicao ASC, TipoDocumento.descricao ASC',
        );
        $tipos_documentos = $this->paginate('TipoDocumento');
        $this->set(compact('tipos_documentos','tipos_documentos'));
    }
    
    /**
     * Ação que lista os documentos a serem enviado para aprovação do credenciamento.
     * @return void
     */    
    function listagem($codigo_proposta = null) {
    	
    	if(!$codigo_proposta) {
			$dadosUsuario = $this->authUsuario['Usuario'];
    	
    		if($codigo_proposta = $dadosUsuario['codigo_proposta_credenciamento']) {
    			$this->redirect(array('controller' => 'propostas_credenciamento', 'action' => 'minha_proposta', $codigo_proposta, 'documentacao'));
    		} else {
				$this->redirect('/');
    		} 
		}
    	$this->pageTitle = 'Proposta de Credenciamento';
    	
    	// quem é o credenciado???
    	$usuario = $this->BAuth->user();
    	
		// Verifica se é Credenciado, lista apenas documentos dele
    	if($usuario['Usuario']['codigo_uperfil'] == Uperfil::CREDENCIANDO) {
    		$usuario_info = $this->Usuario->carregar($usuario['Usuario']['codigo']);
    		$codigo_proposta = $usuario_info['Usuario']['codigo_proposta_credenciamento'];
    	}
    	
    	// qual é o id da proposta???
    	$proposta_info = $this->PropostaCredenciamento->find('first', array('conditions' => array('codigo' => $codigo_proposta)));
    	
    	// retorna lista de documentos
    	$tipos_documentos = $this->TipoDocumento->find('all', array('conditions' => array('status' => 1), 'order' => 'ordem_exibicao ASC, descricao ASC'));
    	
    	foreach($tipos_documentos as $key => $documento) {
    		
			// remove arquivo do contrato se nao ta na fase do contrato ainda!!!
    		if(($documento['TipoDocumento']['codigo_status_proposta_credenciamento'] == '9') && (($proposta_info['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] != '9') && ($proposta_info['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] != '14'))) {
    			unset($tipos_documentos[$key]);
    		} else {
	    		$resultado = $this->PropostaCredDocumento->find('first', array('conditions' => array('codigo_proposta_credenciamento' => $codigo_proposta, 'codigo_tipo_documento' => $documento['TipoDocumento']['codigo'])));
				$tipos_documentos[$key]['PropostaCredDocumento'] = count($resultado['PropostaCredDocumento']) ? $resultado['PropostaCredDocumento'] : array();    			
    		}
    	}
    	// se for perfil credenciado, retira da lista de exibicao documentos que estão na hora de ser enviados.
    	// mostra somente documentos do status momento do envio, e os que já foram enviados!!!
    	if(($usuario['Usuario']['codigo_uperfil'] == Uperfil::CREDENCIANDO) && (($proposta_info['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] < StatusPropostaCred::DOCUMENTACAO_SOLICITADA) || $proposta_info['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::AGUARDANDO_ENVIO_TERMO)) {
    		foreach($tipos_documentos as $key => $campo) {
    			if(($proposta_info['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] != $campo['TipoDocumento']['codigo_status_proposta_credenciamento']) && !count($campo['PropostaCredDocumento'])) {
    				unset($tipos_documentos[$key]);
    			}
    		}
    	}
    	
    	$obrigatorio = $this->verificaObrigatorios($codigo_proposta, $tipos_documentos);
    	$falta_arquivo = $this->verificaDocumentosFaltantes($codigo_proposta, $tipos_documentos);
    	
    	// verifica
    	if(($proposta_info['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == '2') && !$obrigatorio) {
    		
    		// muda status p/ "Aguardando Aprovação"
    		$proposta_info['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] = '3';
    		$this->PropostaCredenciamento->atualizarStatus($proposta_info, '3');
    	}
    	
    	// view
    	$this->set(compact('tipos_documentos'));
    	$this->set('obrigatorio', $obrigatorio);
    	$this->set('proposta', $proposta_info['PropostaCredenciamento']);
    	$this->set('falta_arquivo', $falta_arquivo);
        $this->set('codigo_proposta_credenciamento', $codigo_proposta);
    }
    
    function verificaObrigatorios($codigo_proposta, $tipos_documentos) {
    	foreach($tipos_documentos as $key => $documento) {
    		if(($documento['TipoDocumento']['obrigatorio'] == '1') && !count($documento['PropostaCredDocumento'])) {
				return true;
    		}
    	}
    	return false;
    }
    
    public function verificaDocumentosFaltantes($codigo_proposta, $tipos_documentos) {
    	foreach($tipos_documentos as $key => $documento) {
    		if(!count($documento['PropostaCredDocumento'])) {
				return true;
    		}
    	}
    	return false;    	
    }
    
    /**
     * Via Ajax (verifica se todos os arquivos enviados foram aprovados pelo operador para mostrar o botão gera contrato.
     * 
     * @author: Danilo Borges Pereira
     */
    
    public function verifica_documentos_aprovados() {
		$codigo_proposta = $this->params['form']['codigo_proposta'];
		
        // retorna lista de documentos
    	$tipos_documentos = $this->TipoDocumento->find('all', array('conditions' => array('status' => 1), 'order' => 'ordem_exibicao ASC, descricao ASC'));
    	
    	foreach($tipos_documentos as $key => $documento) {
    		if($documento['TipoDocumento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::APROVADO) {
				unset($tipos_documentos[$key]);    			
    		} else {
	    		$resultado = $this->PropostaCredDocumento->find('first', array('conditions' => array('codigo_proposta_credenciamento' => $codigo_proposta, 'codigo_tipo_documento' => $documento['TipoDocumento']['codigo'])));
				$tipos_documentos[$key]['PropostaCredDocumento'] = count($resultado['PropostaCredDocumento']) ? $resultado['PropostaCredDocumento'] : array();
    		}
    	}
    	
    	$enviados = 0;
    	$aprovados = 0;
        
    	foreach($tipos_documentos as $key => $item) {
			if(isset($item['PropostaCredDocumento']) && count($item['PropostaCredDocumento'])) {
    			$enviados++;
    		}
    		if(isset($item['PropostaCredDocumento']) && count($item['PropostaCredDocumento']) && $item['PropostaCredDocumento']['validado'] == 1) {
    			$aprovados++;
    		}	    			
    	}
    	
		echo (($enviados == $aprovados) && $enviados > 0) ? '1' : '0';
		exit;    	
    }
    
    
    public function verifica_proposta_aprovada() {
		$codigo_proposta = $this->params['form']['codigo_proposta'];
		
        // retorna lista de documentos
    	$tipos_documentos = $this->TipoDocumento->find('all', array('conditions' => array('status' => 1, 'codigo_status_proposta_credenciamento' => '13'), 'order' => 'ordem_exibicao ASC, descricao ASC'));
    	
    	foreach($tipos_documentos as $key => $documento) {
    		$resultado = $this->PropostaCredDocumento->find('first', array('conditions' => array('codigo_proposta_credenciamento' => $codigo_proposta, 'codigo_tipo_documento' => $documento['TipoDocumento']['codigo'])));
    		if(count($resultado)) {
    		    if($resultado['PropostaCredDocumento']['validado'] == 1) {
					echo "1";
					exit; 			
	    		}    			
    		}
    	}
    	echo "0";
		exit;    	
    }    
    
    function enviar() {
    	// codigo da proposta
    	$codigoProposta = $this->data['TiposDocumentos']['codigo_proposta_credenciamento'];
    	
    	$dadosProposta = $this->PropostaCredenciamento->read(null, $codigoProposta);
    	
    	// tira do array, deixando somente os campos de envio
    	unset($this->data['TiposDocumentos']['codigo_proposta_credenciamento']);
    	
    	// array - mensagem erro!
    	$MSG_ERROR = "";
    	
    	// passa por todos os campos
    	foreach($this->data['TiposDocumentos'] as $key => $campo) {
    		
    		// verifica se tem arquivo
    		if(!empty($campo['name']) && $campo['error'] == '0') {
    			
	    		// codigo documento
	    		$codigoDocumento = str_replace('filename_', '', $key);
	    		
	    		// retorna info do documento
	    		$documento_info = $this->TipoDocumento->find('first', array('conditions' => array('codigo' => $codigoDocumento)));
	    		
    			// faz upload
				$retorno = $this->_upload($campo, $codigoProposta, str_replace(" ", "_", strtolower(Comum::trata_nome(str_replace('*', '', $documento_info['TipoDocumento']['descricao'])))));
				
				if($retorno['upload']) {
					
					$dadosDocumento = $this->PropostaCredDocumento->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigoProposta, 'codigo_tipo_documento' => $codigoDocumento)));
					if(count($dadosDocumento)) {
						$this->PropostaCredDocumento->deleteAll(array('codigo_proposta_credenciamento' => $codigoProposta, 'codigo_tipo_documento' => $codigoDocumento));
					}
                    //seta o codigo da empresa na mao
                    $codigo_empresa = 1;
                    //pega os dados da proposta credenciamento
                    $dados_pc = $this->PropostaCredenciamento->find('first', array('conditions' => array('codigo' => $codigoProposta)));
                    //verifica se existe dados
                    if(!empty($dados_pc)) {
                        $codigo_empresa = $dados_pc['PropostaCredenciamento']['codigo_empresa'];
                    }//fim if dados_pc
					// inclui proposta credencimanto documento, na base!
                    $dados_proposta_cred_documento = array(
                        'codigo_proposta_credenciamento' => $codigoProposta, 
                        'codigo_tipo_documento' => $codigoDocumento, 
                        'caminho_arquivo' => $retorno['nome'],
                        'codigo_empresa' => $codigo_empresa
                    );
                    //insere o anexo do documento 
					if($this->PropostaCredDocumento->incluir($dados_proposta_cred_documento)) {
						if(($documento_info['TipoDocumento']['codigo_status_proposta_credenciamento'] == $dadosProposta['PropostaCredenciamento']['codigo_status_proposta_credenciamento']) && !is_null($documento_info['TipoDocumento']['proximo_status_proposta_credenciamento']) && $documento_info['TipoDocumento']['codigo_status_proposta_credenciamento'] != StatusPropostaCred::DOCUMENTACAO_SOLICITADA) {
							$this->PropostaCredenciamento->atualizarStatus($dadosProposta, $documento_info['TipoDocumento']['proximo_status_proposta_credenciamento']);
							if($documento_info['TipoDocumento']['proximo_status_proposta_credenciamento'] == StatusPropostaCred::CONTRATO_ASSINADO_ENVIADO) {
								// $this->PropostaCredenciamento->disparaEmail($dadosProposta, 'Seu Contrato foi enviado com Sucesso.', 'contrato_enviado', $dadosProposta['PropostaCredenciamento']['email'], NULL);
								$desabilita_atualizacao_status_flag = true;
							}
						}
						$this->BSession->setFlash(array(MSGT_SUCCESS, "Arquivo(s) Enviado(s) com Exito!"));
					} else {
						$this->BSession->setFlash('save_error');
					}
				} else {
					$MSG_ERROR .= $retorno['msg'] . "<br />";
				}
    		}
    	}
    	
    	$resultado_documentos = $this->TipoDocumento->_retornaDocsEnviados($codigoProposta);
    	
    	if(($resultado_documentos['qtd_enviados'] == $resultado_documentos['qtd_documentos']) && !isset($desabilita_atualizacao_status_flag)) {
    		if($dadosProposta['PropostaCredenciamento']['codigo_status_proposta_credenciamento'] == StatusPropostaCred::DOCUMENTACAO_SOLICITADA) {
    			$this->PropostaCredenciamento->atualizarStatus($dadosProposta, StatusPropostaCred::AGUARDANDO_ANALISE_DOCUMENTOS);
    		}
    	}
    	
    	// MENSAGEM
    	if(!empty($MSG_ERROR)) {
    		$this->BSession->setFlash(array(MSGT_ERROR, $MSG_ERROR));
    	}
    	// define destino
    	$destino = (strripos($_SERVER['HTTP_REFERER'], 'propostas_credenciamento/editar') && !strripos($_SERVER['HTTP_REFERER'], 'dados_documentacao')) ? $_SERVER['HTTP_REFERER'] . "/dados_documentacao" : $_SERVER['HTTP_REFERER'];
    	
    	// redireciona para onde veio!!!
    	$this->redirect($destino);
    } 
    
    function _upload($file, $proposta, $novo_nome) {
    	
    	// destino do arquivo no servidor
        $destino = APP.'webroot'.DS.'files'.DS.'documentacao'.DS . $proposta;
        // extensoes permitidas
        if( preg_match('@\.(jpg|png|gif|jpeg|bmp|pdf|doc|docx|pdf)$@i', $file['name']) ) {
        	// cria diretorio
	        if(!is_dir($destino))
	        	mkdir($destino, 0777, true); // Cria a parsta recursivamente
	                
	        // upload
	        if(move_uploaded_file($file['tmp_name'], $destino . DS . "proposta_" . $proposta . "_" . $novo_nome . "." . end(explode('.', $file['name'])))) {
	        	return array('upload' => true, 'msg' => 'Arquivo enviado com sucesso!', 'nome' => "proposta_" . $proposta . "_" . $novo_nome . "." . end(explode('.', $file['name'])));
	        } else {
	       		return array('upload' => false, 'msg' => 'Arquivo não Enviado, enviar arquivo com tamanho máximo de 10Mb!');
	        }        	
        } else {
        	return array('upload' => false, 'msg' => 'extensão não permitida, envie jpg, png, gif, jpeg, bmp, pdf, doc, docx ou pdf!');
        }
    }
    /**
     * Remove Registro do Documento Enviado, e Abre campo para reenvio!
     * 
     * @author: Danilo Borges Pereira
     */	
	function remove_arquivo() {
		
		if($this->params['form']['codigo_documento'] == '39') {
			$dadosProposta = $this->PropostaCredenciamento->read(null, $this->params['form']['codigo_proposta']);
			$this->PropostaCredenciamento->atualizarStatus($dadosProposta, StatusPropostaCred::AGUARDANDO_ENVIO_TERMO);
		}
		
		$registro = $this->PropostaCredDocumento->find("all", array("conditions" => array("codigo_proposta_credenciamento = '{$this->params['form']['codigo_proposta']}' AND codigo_tipo_documento = '{$this->params['form']['codigo_documento']}'")));
		
		$return = 0;
		foreach($registro as $k => $item) {
			if($this->PropostaCredDocumento->delete($item['PropostaCredDocumento']['codigo']))
				$return = 1;
		}
		echo $return; exit;
	}	
	/**
     * Aprovao Documento enviado para Credenciamento!
     * 
     * @author: Danilo Borges Pereira
     */	
	function aprova_arquivo() {
		$registro = $this->PropostaCredDocumento->find("all", array("conditions" => array("codigo_proposta_credenciamento = '{$this->params['form']['codigo_proposta']}' AND codigo_tipo_documento = '{$this->params['form']['codigo_documento']}'")));
        if(!empty($this->params['form']['data_validade'])){
    		$data_validade = $this->params['form']['data_validade'];
    		$data_validade = Comum::formataData($data_validade, 'dmy' ,'ymd');
    		
    		$return = 0;
    		foreach($registro as $k => $item) {
    			if($this->PropostaCredDocumento->atualizar(array('PropostaCredDocumento' => array('codigo' => $item['PropostaCredDocumento']['codigo'], 'data_validade' => $data_validade, 'validado' => '1')))) {
    				
    				// retorna informacoes do usuario
    				$infoUsuario = $this->Usuario->find('first', array('conditions' => array('codigo_proposta_credenciamento' => $item['PropostaCredDocumento']['codigo_proposta_credenciamento']), 'fields' => array('codigo_fornecedor')));
    				if($infoUsuario['Usuario']['codigo_fornecedor']) {
    					
    					// inclui arquivo no fornecedor
    					$this->FornecedorDocumento->incluir(
    						array(
    								'codigo_fornecedor' => $infoUsuario['Usuario']['codigo_fornecedor'],
    								'codigo_tipo_documento' => $this->params['form']['codigo_documento'],
    								'caminho_arquivo' => $item['PropostaCredDocumento']['caminho_arquivo'],
    								'data_validade' => $data_validade,
                                    'validado' => 1
    						)
    					);					
    				}
    				
    				$return = 1;
    			}
    				
    		}
    		echo $return; 
    		exit;
        } else{
            $data_validade = null;
            $return = 0;
            foreach($registro as $k => $item) {
                if($this->PropostaCredDocumento->atualizar(array('PropostaCredDocumento' => array('codigo' => $item['PropostaCredDocumento']['codigo'], 'data_validade' => $data_validade, 'validado' => '1')))) {
                    
                    // retorna informacoes do usuario
                    $infoUsuario = $this->Usuario->find('first', array('conditions' => array('codigo_proposta_credenciamento' => $item['PropostaCredDocumento']['codigo_proposta_credenciamento']), 'fields' => array('codigo_fornecedor')));

                    if($infoUsuario['Usuario']['codigo_fornecedor']) {
                        // inclui arquivo no fornecedor
                        $this->FornecedorDocumento->incluir(
                            array(
                                    'codigo_fornecedor' => $infoUsuario['Usuario']['codigo_fornecedor'],
                                    'codigo_tipo_documento' => $this->params['form']['codigo_documento'],
                                    'caminho_arquivo' => $item['PropostaCredDocumento']['caminho_arquivo'],
                                    'data_validade' => $data_validade,
                                    'validado' => 1
                            )
                        );          
                    }
                    
                    $return = 1;
                }
                    
            }
            echo $return;
            exit;
        }
	}
    /**
     * Desfaz Aprova Arquivo (caso a pessoa clique sem querer)
     * 
     * @author: Danilo Borges Pereira
     */	
	function desfazer_aprovar() {
		$registro = $this->PropostaCredDocumento->find("all", array("conditions" => array("codigo_proposta_credenciamento = '{$this->params['form']['codigo_proposta']}' AND codigo_tipo_documento = '{$this->params['form']['codigo_documento']}'")));
		
		$return = 0;
		foreach($registro as $k => $item) {
			if($this->PropostaCredDocumento->atualizar(array('PropostaCredDocumento' => array('codigo' => $item['PropostaCredDocumento']['codigo'], 'data_validade' => null, 'validado' => null)))) {
				
				// retorna informacoes do usuario
				$infoUsuario = $this->Usuario->find('first', array('conditions' => array('codigo_proposta_credenciamento' => $item['PropostaCredDocumento']['codigo_proposta_credenciamento']), 'fields' => array('codigo_fornecedor')));
				
				if($infoUsuario['Usuario']['codigo_fornecedor']) {
					// deleta o arquivo da base do fornecedor
					$this->FornecedorDocumento->deleteAll(array('codigo_fornecedor' => $infoUsuario['Usuario']['codigo_fornecedor'], 'codigo_tipo_documento' => $this->params['form']['codigo_documento']));					
				}
				
				$return = 1;
			}
				
		}
		echo $return; exit;
	}	
}
?>
