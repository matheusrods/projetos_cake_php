<?php

class CargoLog extends AppModel {

	public $name = 'CargoLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'cargos_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_cargos';
	// public $actsAs = array('Secure', 'Containable');
	public $actsAs = array('Secure');
	public $displayField = 'descricao';
    public $validate = array(
        'codigo_cargos' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}