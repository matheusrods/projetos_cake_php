<?php
class Ccusto extends AppModel {
    var $name = 'Ccusto';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'ccusto';
    var $primaryKey = null;
    var $actsAs = array('Secure');
    
    public $virtualFields = array(
    	'codigo_descricao' => 'codigo + \' - \' + descricao'	
    );
    
    public function listar(){
    	return $this->find('list', array('fields'=>array('codigo', 'codigo_descricao'), 'order'=>'codigo'));
    }
}
?>