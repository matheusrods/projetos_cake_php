<?php
class Sbflux extends AppModel {
    var $name = 'Sbflux';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
    var $useTable = 'sbflux';
    var $primaryKey = null;
    var $actsAs = array('Secure');

    public $virtualFields = array(
    		'codigo_descricao' => 'codigo + \' - \' + descricao'
    );
    
    public function listar($grflux){
    	return $this->find('list', array('fields'=>array('codigo', 'codigo_descricao'), 'conditions'=>array('grflux'=>$grflux), 'order'=>'cast(codigo AS INTEGER)'));
    }
}
?>