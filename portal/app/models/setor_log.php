<?php

class SetorLog extends AppModel {

	public $name = 'SetorLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'setores_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_setores';
	public $actsAs = array('Secure', 'Containable');
	public $displayField = 'descricao';
    public $validate = array(
        'codigo_setores' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	
}
?>