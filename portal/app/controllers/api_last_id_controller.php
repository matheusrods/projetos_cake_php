<?php
class ApiLastIdController extends AppController {
    
    public $name = '';

    var $uses = array();

    public $dados = array();
    
    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));
    }

    /**
     * Metodo para retornar os dados para a API codigo, logradouro, bairro, cidade, estado
     * 
     * Return:
     *  codigos
     * 0 => Sucesso
     * 1 => erro: A tabela nao foi especificada.
     * 2 => erro: A tabela especificada nao existe.
     * 
     * indice endereco => retorna os dados do endereco codigo, logradouro, bairro, cidade, estado
     * 
     */

    public function retorna_last_id(){ //http://rhhealth.localhost/portal/api/last_id?tabela=Cliente
        if(isset($_GET['tabela'])){
            $this->loadModel('LastId');
            $retorno = $this->LastId->last_id($_GET['tabela']);
            if (empty($retorno)){
                $retorno["status"] = "2";
            }
        } else {
            $retorno["status"] = "1";
        }

        $retorno["status"] = '0';

        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($retorno);
        exit;
    }

}
?>