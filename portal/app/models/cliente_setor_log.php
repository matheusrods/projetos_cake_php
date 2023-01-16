<?php

class ClienteSetorLog extends AppModel {

	public $name = 'ClienteSetorLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'clientes_setores_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_clientes_setores';
	public $actsAs = array('Secure');
	public $displayField = 'codigo_clientes_setores';
    public $validate = array(
        'codigo_clientes_setores' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}