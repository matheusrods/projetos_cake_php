<?php
class ClienteContrato extends AppModel {
    var $name = 'ClienteContrato';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'cliente_contrato';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');

   public function listar($type = 'all', array $conditions=null) {
       $result = $this->find($type, array(
           'conditions' => $conditions
       ));
       return $result;
   }
}
