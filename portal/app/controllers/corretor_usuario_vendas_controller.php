<?php
class CorretorUsuarioVendasController extends AppController {
    public $name = 'CorretorUsuarioVendas';
    var $uses = array('CorretorUsuarioVendas');
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('criar_acesso'));
    }

    function criar_acesso( $codigo_corretora ){

        /*
        if($this->RequestHandler->isPost()) {
            $this->loadModel('CorretorUsuarioVendas');

            $post = $this->data['CorretorUsuarioVendas'];

            if( !$this->CorretorUsuarioVendas->validates( $this->data ) ){
                $this->trata_invalidation();
                $this->BSession->setFlash('save_error');
            } else {
                if ($this->CorretorUsuarioVendas->exportarCorretor( $this->data )) {
                    $this->BSession->setFlash('save_success');
                    //$this->redirect(array('action' => 'editar', $this->Corretora->id));
                } else {
                    $this->trata_invalidation();
                    $this->BSession->setFlash('save_error');
                }
            }

            
        }

        $dados = $this->CorretorUsuarioVendas->find('first', array(
            'conditions' => array( 'codigo' => $codigo_corretora )
        ));   
        $this->set( compact('dados') );
        */
    }
}