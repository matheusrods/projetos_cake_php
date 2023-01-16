<?php

class CargaTipo extends AppModel {

    var $name = 'CargaTipo';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'carga_tipo';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $displayField = 'descricao';
    
    public function lista() {
    	return $this->find('list',array('order'=>'descricao'));
    }
}