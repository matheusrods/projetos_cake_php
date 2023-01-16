<?php
class Grflux extends AppModel {
    var $name = 'Grflux';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'grflux';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    public $virtualFields = array(
    		'codigo_descricao' => 'codigo + \' - \' + descricao'
    );
    
    public function listar(){
    	return $this->find('list', array('fields'=>array('codigo', 'codigo_descricao'), 'order'=>'codigo'));
    }
}
?>