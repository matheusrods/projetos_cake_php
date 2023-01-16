<?php
class ClassificacaoServicosController extends AppController {
    public $name = 'ClassificacaoServicos';
    public $uses = array('ClassificacaoServico');
    
    public function index() {
        $this->pageTitle = 'Planos de Saúde';
    }
    
}