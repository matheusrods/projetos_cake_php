<?php

class Tecnologia extends AppModel {

    var $name = 'Tecnologia';
    var $tableSchema = 'publico';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'tecnologia';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $displayField = 'descricao';
    
    public function lista() {
    	return $this->find('list',array('order'=>'descricao'));
    }
}