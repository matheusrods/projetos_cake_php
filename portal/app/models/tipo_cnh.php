<?php

class TipoCnh extends AppModel {

	public $name = 'TipoCnh';
	public $tableSchema = 'publico';
	public $databaseTable = 'dbBuonny';
	public $useTable = 'tipo_cnh';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');
	

   public function combo() {
        $cnh = $this->find('list');
        return $cnh;
   }
}