<?php

class NPedido extends AppModel {

	var $name = 'NPedido';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbNavegarqNatec';
	var $useTable	  = 'pedido';
	var $primaryKey	= 'numero';

	function bindIntegfat(){
    	$this->bindModel(array(
    		'hasOne' => array(
    			'Integfat' => array(
	    			'foreignKeys' 	=> false,
	    			'conditions' 	=> array(
	    				'Integfat.npedido = Pedido.numero',
	    				'Integfat.empresa = Pedido.empresa',
	    			)
    			)
    		)
    	));
    }

}