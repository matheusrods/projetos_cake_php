<?php

class ParametroBoleto extends AppModel {

    var $name = 'ParametroBoleto';
    var $tableSchema = 'vendas';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'parametros_boleto';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

    function atualizar($dados) {
        if (!isset($dados[$this->name]['codigo']) || $dados[$this->name]['codigo'] == null)
            return false;
        return $this->save($dados);
    }
    
}
