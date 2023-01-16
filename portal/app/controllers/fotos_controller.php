<?php
class FotosController extends AppController {
    public $name = 'Fotos';
    var $uses = array('PropostaCredFoto', 'Usuario', 'PropostaCredenciamento', 'Uperfil');
    
    public function beforeFilter() {
    	parent::beforeFilter();
    	$this->BAuth->allow(array('enviar', 'remove_arquivo', 'listagem'));
    }
    
    
    public function index() {
		$this->redirect(array('action' => 'listagem'));
    }
    
    /**
     * Ação que lista os documentos a serem enviado para aprovação do credenciamento.
     * @return void
     */    
    function listagem($codigo_proposta = null) {
    
    	$this->pageTitle = '';
    	
    	// quem é o credenciado???
    	$usuario = $this->BAuth->user();
    	
		// Verifica se é Credenciado, lista apenas documentos dele
    	if($usuario['Usuario']['codigo_uperfil'] == Uperfil::CREDENCIANDO) {
    		$usuario_info = $this->Usuario->carregar($usuario['Usuario']['codigo']);
    		$codigo_proposta = $usuario_info['Usuario']['codigo_proposta_credenciamento'];
    	}
    	
    	// qual é o id da proposta???
    	$proposta_info = $this->PropostaCredenciamento->find('first', array('conditions' => array('codigo' => $codigo_proposta)));
    	
    	if($proposta_info) {
		    // busca fotos ja enviadas
		    $lista_enviadas = $this->PropostaCredFoto->find('all', array('conditions' => array('codigo_proposta_credenciamento' => $codigo_proposta)));
    	}
    	
    	// view
    	$this->set(compact('lista_enviadas'));
    	$this->set('proposta', $proposta_info['PropostaCredenciamento']);
        $this->set('codigo_proposta_credenciamento', $codigo_proposta);
    }
    
    function enviar() {

    	// codigo da proposta
    	$codigoProposta = $this->data['Fotos']['codigo_proposta_credenciamento'];
    	
    	// tira do array, deixando somente os campos de envio
    	unset($this->data['Fotos']['codigo_proposta_credenciamento']);
    	
    	// array - mensagem erro!
    	$MSG_ERROR = "";
    	
    	// passa por todos os campos
    	foreach($this->data['Fotos'] as $key => $campo) {
    		
    		// verifica se tem arquivo
    		if(!empty($campo['caminho_arquivo']['name']) && $campo['caminho_arquivo']['error'] == '0') {
    			
    			// faz upload
				$retorno = $this->_upload($campo['caminho_arquivo'], $codigoProposta, str_replace(" ", "_", strtolower(Comum::trata_nome($campo['descricao']))));
				
				if($retorno['upload']) {
					
					// inclui proposta credencimanto documento, na base!
					if($this->PropostaCredFoto->incluir(array('codigo_proposta_credenciamento' => $codigoProposta, 'descricao' => $campo['descricao'], 'caminho_arquivo' => $retorno['nome'], 'status' => '1'))) {
						$this->BSession->setFlash(array(MSGT_SUCCESS, "Arquivo(s) Enviado(s) com Exito!"));
					} else {
						$this->BSession->setFlash('save_error');
					}
				} else {
					$MSG_ERROR .= $retorno['msg'] . "<br />";
					
					pr($MSG_ERROR);
					exit;
					
				}
    		}
    	}
    	
    	// MENSAGEM
    	if(!empty($MSG_ERROR)) {
    		$this->BSession->setFlash(array(MSGT_ERROR, $MSG_ERROR));
    	}

    	// redireciona para onde veio!!!
    	$this->redirect($_SERVER['HTTP_REFERER']);
    } 
    
    function _upload($file, $proposta, $novo_nome) {
    	
    	// destino do arquivo no servidor
        $destino = APP.'webroot'.DS.'files'.DS.'fotos'.DS . $proposta;

        // extensoes permitidas
        if( preg_match('@\.(jpg|png|gif|jpeg|bmp|pdf)$@i', $file['name']) ) {
        	
        	// cria diretorio
	        if(!is_dir($destino))
	        	mkdir($destino);
	                
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
     * Remove Foto!
     * 
     * @author: Danilo Borges Pereira
     */	
	function remove_arquivo() {
		echo $this->PropostaCredFoto->delete($this->params['form']['codigo']) ? 1 : 0; exit;
	}
}
?>
