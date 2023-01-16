<?php
class CnaeSecao extends AppModel {

    var $name = 'CnaeSecao';
    var $tableSchema = 'dbo';
    var $databaseTable = 'RHHealth';
    var $useTable = 'cnae_secao';
    var $primaryKey = 'secao';
    //var $actsAs = array('Secure');
    
    var $belongsTo = array(
        'Cnae' => array(
            'className' => 'Cnae',
            'foreignKey' => false
        )
    ); 

}

