<?php
class MensagensDeAcessosController extends AppController {

	var $name = 'MensagensDeAcessos';
	
	var $uses = array('MensagemDeAcesso', 'TipoPerfil', 'Modulo');

	function index() {
		
		$this->pageTitle = 'Cadastro de Mensagens';
		$this->data['MensagemDeAcesso'] = $this->Filtros->controla_sessao($this->data, $this->MensagemDeAcesso->name);		
	}

	function listagem() {

		$filtros['MensagemDeAcesso'] = $this->Filtros->controla_sessao($this->data, $this->MensagemDeAcesso->name);        

        if( $filtros['MensagemDeAcesso'] ) {        
            
            $dados = array();
        	$conditions = $this->MensagemDeAcesso->converterFiltrosEmConditions($filtros);
            $dados      = $this->MensagemDeAcesso->listagem($conditions);

            $this->set(compact('dados'));
        }  
	}

    private function carregaCombos(){
        $lista_perfis = $this->TipoPerfil->find('list',array('fields' => 'descricao'));
        $modulos = $this->Modulo->modulosToOptions();
        $this->set(compact('lista_perfis', 'modulos'));
    }

	public function incluir(){

        $this->pageTitle = 'Incluir Mensagem';

        if( isset($this->data) && !empty($this->data) ){ 
            $this->data['MensagemDeAcesso']['data_final'] .= ' 23:59:59';
            if($this->MensagemDeAcesso->incluir($this->data)){
                $this->BSession->setFlash('save_success');
                $this->redirect( array( 'action'=>'index' ) );
            }else{
                $this->BSession->setFlash('save_error');                
            }
        }
        $this->carregaCombos();
    }

    public function editar($codigo){

        $this->pageTitle = 'Editar Mensagem';

        if( isset($this->data) && !empty($this->data) ){            
            if($this->MensagemDeAcesso->atualizar($this->data)){
                $this->BSession->setFlash('save_success');
                $this->redirect( array( 'action'=>'index') );
            }else{
                $this->BSession->setFlash('save_error');                
            }
        } else {
            $this->data = $this->MensagemDeAcesso->findByCodigo($codigo);            
        }
        $this->carregaCombos();
        
    }

    public function excluir($codigo){

        if( isset($codigo) && !is_null($codigo) ){
            if( $this->MensagemDeAcesso->excluir($codigo) ){
                $this->BSession->setFlash('delete_success');
                $this->redirect( array( 'action'=>'index' ) );
            }else{
                $this->BSession->setFlash('delete_error');
            }
        }else{
            $this->BSession->setFlash('delete_error');
        }
        die;
    }

    public function upload_imagem(){
        $this->pageTitle = 'Upload de Imagem de Mensagens de Acesso';
        if($this->RequestHandler->isPost()) {
            if(!isset($this->data['MensagemDeAcesso']['titulo']) || empty($this->data['MensagemDeAcesso']['titulo'])){
                $this->MensagemDeAcesso->invalidate('titulo', 'Informe o titulo');
            }
            $imagem = $this->MensagemDeAcesso->vefifica_imagem($this->data);
            if($imagem === TRUE){
                $this->BSession->setFlash('save_success');
                $this->redirect( array( 'action'=>'visualizar_imagem' ) );
            }else{
                $this->BSession->setFlash(array(MSGT_ERROR, $imagem));
            }
        }    
    }

    public function visualizar_imagem(){
        $this->pageTitle = 'Imagens de Mensagens de Acesso';
    }

    public function excluir_imagem($imagem){
        $this->layout = false;
      
        $retorno = $this->MensagemDeAcesso->permite_excluir($imagem);

        if(empty($retorno)){
            $pasta = APP . "webroot".DS."img".DS.'mensagens'; 
            unlink($pasta.'/'.$imagem);
            $this->BSession->setFlash('delete_success');
            $this->redirect( array( 'action'=>'visualizar_imagem' ) );
        }else{
            $this->BSession->setFlash(array(MSGT_ERROR, "Não foi possível excluir a imagem. A mesma está em uso pela Mensagem de Acesso <b><i>".$retorno[0]['MensagemDeAcesso']['titulo'].'</i></b>'));
            $this->redirect( array( 'action'=>'visualizar_imagem' ) );
        }    
    }
}