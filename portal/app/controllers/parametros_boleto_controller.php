<?php

class ParametrosBoletoController extends AppController {

    public $name = 'ParametrosBoleto';
    public $layout = 'cliente';
    public $components = array('RequestHandler');
    public $helpers = array('Html', 'Ajax');
    public $uses = array('ParametroBoleto', 'Usuario');
 
    function parametros_para_boleto_bb() {
        if(!empty($this->data)) {
            if($this->ParametroBoleto->atualizar($this->data)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $parametros_boleto_bb = $this->ParametroBoleto->find('first');
        $this->data = $parametros_boleto_bb;
    }
  
}
