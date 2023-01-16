<?php

class AutotracParametrosController extends AppController {

    public $name = 'AutotracParametros';
    public $layout = 'default';
    public $components = array('RequestHandler');
    public $helpers = array('Html', 'Ajax', 'Buonny');
    public $uses = array('AutotracParametro');
 
    // function beforeFilter(){
    //     parent::beforeFilter();
    //     $this->BAuth->allow('editar');
    // }
    function editar() {        
        if(!empty($this->data)) { 
            $taxa = $this->data['AutotracParametro']['taxa_administrativa'];
            $taxa = str_replace('.', '', $taxa);
            $taxa = str_replace(',', '.', $taxa);
            $this->data['AutotracParametro']['taxa_administrativa'] = $taxa;

            $imposto = $this->data['AutotracParametro']['percentual_imposto'];
            $imposto = str_replace('.', '', $imposto);
            $imposto = str_replace(',', '.', $imposto);
            $this->data['AutotracParametro']['percentual_imposto'] = $imposto;

            if($this->AutotracParametro->incluir($this->data)) {
                $this->BSession->setFlash('save_success');
            } else {
                $this->BSession->setFlash('save_error');
            }
        }
        $parametros= $this->AutotracParametro->find('first', array('order' => array('AutotracParametro.codigo DESC')));
        $parametros['AutotracParametro']['taxa_administrativa'] = !empty($parametros['AutotracParametro']['taxa_administrativa']) ? number_format($parametros['AutotracParametro']['taxa_administrativa'],'2',',','.') : 0;
        $parametros['AutotracParametro']['percentual_imposto'] = !empty($parametros['AutotracParametro']['percentual_imposto']) ? number_format($parametros['AutotracParametro']['percentual_imposto'],'2',',','.') : 0;
        $this->data = $parametros;
    }
  
}
