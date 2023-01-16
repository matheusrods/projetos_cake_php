<?php

class RiscoLog extends AppModel {

	public $name = 'RiscoLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'riscos_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_riscos';
	public $actsAs = array('Secure');
    public $validate = array(
        'codigo_riscos' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}