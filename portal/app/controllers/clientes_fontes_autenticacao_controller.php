<?php

class ClientesFontesAutenticacaoController extends AppController
{

    var $name = "ClientesFontesAutenticacao";

    var $uses = array('ClienteFonteAutenticacao');

    public function __construct()
    {
        parent::__construct();

        $this->loadModel('ClienteFonteAutenticacao');
        $this->loadModel('Cliente');
    }

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow('salvar', 'salvar_ajax');
    } //FINAL FUNCTION beforeFilter

    public function salvar_ajax()
    {

        $this->layout = 'ajax';

        if ($this->RequestHandler->isPost()) {

            try {

                $fonteAutenticacaoArr = $_POST;

                $this->ClienteFonteAutenticacao->save($fonteAutenticacaoArr);

                if (empty($fonteAutenticacaoArr['codigo'])) {

                    $fonteAutenticacaoArr['codigo'] = $this->ClienteFonteAutenticacao->getLastInsertID();
                }

                header('Content-Type: application/json; charset=utf-8');
                $this->set(compact('fonteAutenticacaoArr'));
            } catch (Exception $e) {

                die;
            }
        }
    }
}
