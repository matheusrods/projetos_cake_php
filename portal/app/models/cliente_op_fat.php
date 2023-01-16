<?php
class ClienteOpFat extends AppModel {
    var $name = 'ClienteOpFat';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'cliente_opcao_fat';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    function incluir($dados) {
        if ($this->optante($dados['ClienteOpFat']['codigo_cliente']))
            return true;
        $this->create();
        return $this->save($dados);
    }
    
    function optante($codigo_cliente) {
        return ($this->find('count', array('conditions' => array('codigo_cliente' => $codigo_cliente))) > 0);
    }
}

?>