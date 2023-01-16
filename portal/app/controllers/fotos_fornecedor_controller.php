<?php
class FotosFornecedorController extends AppController {
    public $name = 'FotosFornecedor';
	var $uses = array('FornecedorFoto', 'Usuario');
	
	public $components = array(
		'Upload'
	);
	
    public function beforeFilter() {
    	parent::beforeFilter();
		$this->BAuth->allow(array('enviar', 'remove_arquivo', 'listagem'));
		// configurando fileserver
		//$this->FileServer->setOption('prefix', 'portal');
	}
    
    public function index() {
		$this->redirect(array('action' => 'listagem'));
    }
    
    /**
     * 
     * @return void
     */    
    function listagem($codigo_fornecedor = null) {
    
		$this->pageTitle = '';
		
		$url_data = $this->params['url'];
		if(empty($codigo_fornecedor)){
			$this->BSession->setFlash(array(MSGT_ERROR, 'Codigo fornecedor não encontrado'));
		}
		$url_retorno = null;
		if(isset($url_data['url_retorno'])){
			$url_retorno = $url_data['url_retorno'];
		}
    	// quem é o credenciado???
    	$usuario = $this->BAuth->user();
    	
		// Verifica se é Credenciado, lista apenas documentos dele
    	// if($usuario['Usuario']['codigo_uperfil'] == Uperfil::CREDENCIANDO) {
    	// 	$usuario_info = $this->Usuario->carregar($usuario['Usuario']['codigo']);
    	// 	$codigo_proposta = $usuario_info['Usuario']['codigo_proposta_credenciamento'];
    	// }
    	
		// busca fotos ja enviadas
		$lista_enviadas = $this->FornecedorFoto->find('all', array('conditions' => array('codigo_fornecedor' => $codigo_fornecedor)));
	
    	// view
    	$this->set(compact('lista_enviadas', 'url_retorno'));
        $this->set('codigo_fornecedor', $codigo_fornecedor);
    }
    
    public function enviar() {


    	$this->layout = false;

		$post_data = (($this->RequestHandler->isPost()) || !isset($this->params['data']) )? $this->data : $this->params['data'];	
		$url_redirect = isset($post_data['Fotos']['url_redirect']) ? $post_data['Fotos']['url_redirect'] : $_SERVER['HTTP_REFERER']; 
		
		if(!isset($post_data)){
			header('Location:'.$url_redirect);
			exit;
		}
		if(!isset($this->params['data']['Fotos'])){
			$this->BSession->setFlash(array(MSGT_ERROR, 'Dados da imagem não encontrado'));
			return;
		}
		$file = array();
		$codigo_fornecedor = $post_data['Fotos']['codigo_fornecedor'];
    	// tira do array, deixando somente os campos de envio
    	unset($post_data['Fotos']['codigo_fornecedor']);
		
    	// passa por todos os campos
    	//foreach($post_data['Fotos'] as $key => $campo) {
    		// Array
			// 	(
			// 		[descricao] => ccc
			// 		[caminho_arquivo] => Array
			// 			(
			// 				[name] => Frame.png
			// 				[type] => image/png
			// 				[tmp_name] => C:\xampp\tmp\php9B61.tmp
			// 				[error] => 0
			// 				[size] => 2630
			// 			)
			// 	)
		
		$error = false;
		// verifica se tem arquivo
		foreach ($post_data['Fotos'] as $k => $v) {

			if(empty($file) && is_array($v)){
				
				$file = $v;
				// faz upload
				$nome = str_replace(" ", "_", strtolower(Comum::trata_nome($v['descricao'])));

				$nome_arquivo = $v['caminho_arquivo']['name'];
				$envia_foto['file'] = $v['caminho_arquivo'];

				//$retorno = $this->_upload($v['caminho_arquivo'], $codigo_fornecedor, $nome, '1200');
				$retorno = $this->Upload->fileServer($envia_foto);
				
				// debug($retorno);
				// debug($retorno['data'][$nome_arquivo]['path']);
				// exit;

				if (isset($retorno['error']) && !empty($retorno['error']) ){
					$this->BSession->setFlash(array(MSGT_ERROR, $retorno['error']));
					$error = true;
				}
				if(!isset($retorno['data'][$nome_arquivo]['path'])){
					$this->BSession->setFlash(array(MSGT_ERROR, 'Não foi posivel enviar imagem para o servidor'));					
					return;
					$error = true;
				}
				
				if(!$error) {
					$dadosFornecedorFoto = array(
						'FornecedorFoto' => array(
							'codigo_fornecedor' => $codigo_fornecedor, 
							'descricao' => $v['descricao'], 
							'caminho_arquivo' => $retorno['data'][$nome_arquivo]['path'], 
							'status' => '1'
						)
					);

					if($this->FornecedorFoto->incluir($dadosFornecedorFoto)) {
						$this->BSession->setFlash(array(MSGT_SUCCESS, "Arquivo(s) Enviado(s) com Exito!"));
						
						//$this->redirect($_SERVER['HTTP_REFERER']);
						echo $this->redirect(array('controller' => 'fotos_fornecedor', 'action' => 'listagem', $codigo_fornecedor));
						exit;
						
					} else {
						$this->BSession->setFlash('save_error');
					}
				}
			}
			
		}
		$file = array();
		// redireciona para onde veio!!!
		header('Location:'.$url_redirect);
		// $uo = Comum::UrlOrigem();
		// if( $uo ){
		// 	$this->redirect( Comum::UrlOrigem()->data );						
		// } else {
		// 	$this->redirect(array('controller' => 'fornecedores', 'action' => 'index'));
		// }
    } 
    
    // function _upload($file, $proposta, $novo_nome) {
    	
    // 	// destino do arquivo no servidor
    //     $destino = APP.'webroot'.DS.'files'.DS.'fotos'.DS . $proposta;
    //     // extensoes permitidas
    //     if( preg_match('@\.(jpg|png|gif|jpeg|bmp|pdf)$@i', $file['name']) ) {
        	
    //     	// cria diretorio
	//         if(!is_dir($destino))
	//         	mkdir($destino);
	                
	//         // upload
	//         if(move_uploaded_file($file['tmp_name'], $destino . DS . "proposta_" . $proposta . "_" . $novo_nome . "." . end(explode('.', $file['name'])))) {
	//         	return array('upload' => true, 'msg' => 'Arquivo enviado com sucesso!', 'nome' => "proposta_" . $proposta . "_" . $novo_nome . "." . end(explode('.', $file['name'])));
	//         } else {
	//        		return array('upload' => false, 'msg' => 'Arquivo não Enviado, enviar arquivo com tamanho máximo de 10Mb!');
	//         }        	
    //     } else {
    //     	return array('upload' => false, 'msg' => 'extensão não permitida, envie jpg, png, gif, jpeg, bmp, pdf, doc, docx ou pdf!');
    //     }
    // }
    /**
     * Remove Foto!
     * 
     * @author: Danilo Borges Pereira
     */	
	function remove_arquivo() {
		echo $this->FornecedorFoto->delete($this->params['form']['codigo']) ? 1 : 0; exit;
	}
}
?>