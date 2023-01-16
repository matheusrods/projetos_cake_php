<?php
App::import('Model', 'LogIntegracao');
class TeleconsultIntegracao extends AppModel {
    var $name                = 'TeleconsultIntegracao';
    var $useTable            = false;
    var $LogIntegracao       = null;
    var $conteudo            = null;
    var $arquivo             = null;
    var $validationError     = array();
    var $cliente_portal      = null;
    const SUCESSO            = 0;
    const ERRO               = 1;

    public function __construct(){
        $this->LogIntegracao =& ClassRegistry::init('LogIntegracao');
    }


    public function cadastrarLog($data){
        $log_integracao = array('LogIntegracao' => array(
            'arquivo'        => end(explode(DS, $this->arquivo)),
            'conteudo'       => $this->conteudo,
            'retorno'        => $data['mensagem'],
            'sistema_origem' => $this->name,
            'status'         => $data['status'],
            'codigo_cliente' => $this->cliente_portal,
            'descricao'      => $data['descricao'],
            'tipo_operacao'  => isset($data['operacao']) ? $data['operacao'] : NULL,
        ));
        //$this->log(var_export($log_integracao,true),'ws_teleconsult');
        $this->LogIntegracao->incluir($log_integracao);
    }
}