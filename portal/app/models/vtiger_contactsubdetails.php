<?php
class VtigerContactsubdetails extends AppModel {

    var $name = 'VtigerContactsubdetails';
    var $useDbConfig = 'crm';
    var $databaseTable = 'crm521';
    var $useTable = 'vtiger_contactsubdetails';
    var $primaryKey = 'contactsubscriptionid';

    public function buscaAniversarianteDoDia( $month, $day )
    {
    	$this->bindModel(array(

    		'belongsTo' => array(
    			'VtigerContactdetails' => array(
    				'foreignKey' => false,
                    'conditions' => array( 'VtigerContactsubdetails.contactsubscriptionid = VtigerContactdetails.contactid' )
    			)
    		)
    	));

    	$result = $this->find( 'all', 
            array( 
                'fields'     => array( 'VtigerContactdetails.firstname', 'VtigerContactdetails.email' ),
                'conditions' => array( "MONTH(VtigerContactsubdetails.birthday) = '{$month}' AND DAY(VtigerContactsubdetails.birthday) = '{$day}'" ) 
        ));

    	return $result;
    }


} 
