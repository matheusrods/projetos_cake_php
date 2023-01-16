<?php

class LojasNavegController extends AppController {
    public $name = 'LojasNaveg';
    var $uses = array('LojaNaveg');

    function listar($grupo_empresa) {
        $this->layout = 'ajax';
        $this->set('empresas', $this->LojaNaveg->listEmpresas($grupo_empresa));
    }
}