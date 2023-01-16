<?php

class MonitoraFuncionariosController extends AppController {

    public $name = 'MonitoraFuncionarios';
    public $layout = 'default';
    public $uses = array("MonitoraFuncionario");

    function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('buscar'));
    }

    function buscar() {
        $nome = $this->params['form']['nome'];
        $usuarios = $this->MonitoraFuncionario->query("select top 100 Codigo, nome from Monitora.dbo.Funcionarios where nome like '$nome%';");
        $usuarios = array_values($usuarios);
        $usuarios = array_map(create_function('$usuario', 'return array($usuario[0]["Codigo"], utf8_decode($usuario[0]["nome"]));'), $usuarios);

        echo json_encode($usuarios);
        exit;
    }
}

?>
 