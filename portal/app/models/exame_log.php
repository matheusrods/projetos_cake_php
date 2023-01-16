<?php

class ExameLog extends AppModel {

	public $name = 'ExameLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'exames_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_exames';
	public $actsAs = array('Secure');
	public $displayField = 'codigo_exames';
    public $validate = array(
        'codigo_exames' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}