<?php
class NotafisComplemento extends AppModel {
    var $name = 'NotafisComplemento';
    var $tableSchema = 'dbo';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'notafis_complemento';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    
    function bindNotafis(){
    	$this->bindModel(array(
    		'hasOne' => array(
    			'Notafis' => array(
	    			'foreignKeys' 	=> false,
	    			'conditions' 	=> array(
	    				'Notafis.empresa = NotafisComplemento.empresa',
	    				'Notafis.seq = NotafisComplemento.seq',
	    				'Notafis.serie = NotafisComplemento.serie',
	    				'Notafis.numero = NotafisComplemento.numero',
	    			)
    			)
    		)
    	));
    }

    function bindNotaite(){
    	$this->bindModel(array(
    		'hasOne' => array(
    			'Notaite' => array(
	    			'foreignKeys' 	=> false,
	    			'conditions' 	=> array(
	    				'Notaite.empresa = NotafisComplemento.empresa',
	    				'Notaite.seq = NotafisComplemento.seq',
	    				'Notaite.serie = NotafisComplemento.serie',
	    				'Notaite.numero = NotafisComplemento.numero',
	    			)
    			)
    		)
    	));
    }

    function bindTranrec(){
    	$this->bindModel(array(
    		'hasOne' => array(
    			'Tranrec' => array(
	    			'foreignKeys' 	=> false,
	    			'conditions' 	=> array(
	    				'Tranrec.empresa = NotafisComplemento.empresa',
	    				'Tranrec.seqn = NotafisComplemento.seq',
	    				'Tranrec.serie = NotafisComplemento.serie',
	    				'Tranrec.numero = NotafisComplemento.numero',
	    			)
    			)
    		)
    	));
    }

    

}

?>