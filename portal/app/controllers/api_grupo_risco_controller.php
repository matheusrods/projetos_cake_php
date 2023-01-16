<?php
class ApiGrupoRiscoController extends AppController {
    public $name = 'ApiGrupoRisco';
    var $uses = array('GrupoRisco');
    var $components = array('RequestHandler');

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow('index');
        $this->RequestHandler->setContent("json", "application/json");  
        Configure::write('debug', 0);    
    }
    
    function index($id_cliente) {
        $this->autoRender = false;
        $dados = $this->GrupoRisco->retorna_grupo();
        $response = json_encode($dados);
        header('Content-type: application/json; charset=UTF-8');
        die($response);
    }   
  
}