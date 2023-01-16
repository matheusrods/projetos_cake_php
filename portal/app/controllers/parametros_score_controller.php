<?php
class ParametrosScoreController extends AppController {
    var $name = 'ParametrosScore';
    var $uses = array('ParametroScore');

     

    function index() {
        $this->pageTitle = 'Parâmetros para Grid Motorista';
        $parametros = $this->ParametroScore->find('all');
        $this->set(compact('parametros'));
    }

    
    function incluir() {
        $this->pageTitle = 'Incluir Parâmetro Grid';
        
        if (!empty($this->data)){
           
            if ($this->ParametroScore->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
                $this->redirect(array('action' => 'index'));
            } else {
                
                $this->BSession->setFlash('save_error');              
                $parametro= null;
                $this->set(compact('parametro'));
            }
        } else {
            $parametro= null;
            $this->set(compact('parametro'));
            $this->data = array('ParametroScore' => array(
            'nivel' => null,
            'pontos' => 0,
            'valor' => 0,
            ));
        }
    }

         

    function editar($parametros) {
        $this->pageTitle = 'Editar Parâmetro Grid';
       
        if (!empty($this->data)) {
            if ($this->ParametroScore->atualizar($this->data)) {
               $this->BSession->setFlash('save_success');
               $this->redirect(array('action' => 'index'));
           
            }else {
               $this->BSession->setFlash('save_error');                
               $parametro= $this->data = $this->ParametroScore->carregar($parametros);
               $this->set(compact('parametro')); 
            }
        }else {
         $parametro= $this->data = $this->ParametroScore->carregar($parametros);
        }
   
        $this->set(compact('parametro'));
    }

    function excluir($parametros) {
        
        if ($this->ParametroScore->excluir($parametros)) {
            $this->BSession->setFlash('delete_success');
        } else {
            $this->BSession->setFlash('delete_error');
        }
        $this->redirect(array('action' => 'index'));
    }


}
?>