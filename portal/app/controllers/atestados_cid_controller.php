<?php
class AtestadosCidController extends AppController {
    public $name = 'AtestadosCid';
    var $uses = array('AtestadoCid',
        'Cid');
    
    function beforeFilter() {
    	parent::beforeFilter();
    	$this->BAuth->allow('listagem', 'incluir', 'excluir');
    }
    
    function listagem($codigo_atestado){
        $this->layout = 'ajax';
        
        $this->AtestadoCid->bindModel(array(
           'belongsTo' => array(
               'Cid' => array(
                   'alias' => 'Cid',
                   'foreignKey' => FALSE,
                   'type' => 'LEFT',
                   'conditions' => 'Cid.codigo = AtestadoCid.codigo_cid'
               )
           )
        ));
       $cids = $this->AtestadoCid->find('all', array(
          'conditions' => array('codigo_atestado' => $codigo_atestado), 
          'order' => 'Cid.descricao',
          'limit' => 10)
        );

        $this->set(compact('cids','codigo_atestado'));
    }
    

    function incluir() {
        
        if($this->RequestHandler->isPost()) {
        	
            $dados = array(
                'AtestadoCid' => array(
                    'codigo_atestado' => $_POST['codigo_atestado'],
                    'codigo_cid' => $_POST['codigo_cid']
                    )
                );

            if ($this->AtestadoCid->incluir($dados)) {
                $this->BSession->setFlash('save_success');
                echo 1;
            } 
            else {
                $this->BSession->setFlash('save_error');
                echo 0;
            }
        }
        exit;
    }
    public function excluir($codigo) {
        
        if ($this->AtestadoCid->excluir($codigo)) {
            $this->BSession->setFlash('save_success');
            echo 1;
        } else {
            $this->BSession->setFlash('save_error');
            echo 0;
        }

        exit;
    }
}