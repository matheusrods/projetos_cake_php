<?php
class TiposNegativacoesController extends AppController {
    public $name = 'TiposNegativacoes';
    var $uses = array('TipoNegativacao');

    function index() {
        $this->pageTitle ='Tipos de Negativação';
    }
    function listagem() {
        $this->layout = 'ajax';
        $filtros = $this->Filtros->controla_sessao($this->data, 'TipoNegativacao');
        $conditions = $this->TipoNegativacao->converteFiltroEmCondition($filtros);
        $this->paginate['TipoNegativacao'] = array(
            'conditions' => $conditions,
            'limit' => 50,
            'order' => 'TipoNegativacao.descricao',
        );

        $resultado = $this->paginate('TipoNegativacao');
        $this->set('dados',$resultado);
    }
    function incluir() {
    	if(!empty($this->data)) {
    		if($this->TipoNegativacao->incluir($this->data)) {
    			$this->BSession->setFlash('save_success');	
    			$this->redirect(array('controller' => 'TiposNegativacoes','action' => 'index'));
    		} else {
    			$this->BSession->setFlash('save_error');
    		}
    	}
    }
    function editar($codigo) {
    	if(!empty($this->data)) {
    		if($this->TipoNegativacao->atualizar($this->data)) {
    			$this->BSession->setFlash('save_success');	
    			$this->redirect(array('controller' => 'TiposNegativacoes','action' => 'index'));
    		} else {
    			$this->BSession->setFlash('save_error');
    		}
    	}
 	    $this->data = $this->TipoNegativacao->carregar($codigo);
    }
    function excluir($codigo) {
        if( $this->TipoNegativacao->excluir($codigo) ){
            $this->BSession->setFlash('delete_success');
            $this->redirect( array( 'action'=>'index' ) );
        }else{
            $this->BSession->setFlash('delete_error_dependencies');
            $this->redirect( array( 'action'=>'index' ) );
        }
    }
}
?>
