<?php

class TipoGlosasLog extends AppModel {

    public $databaseTable = 'RHHealth';
    public $tableSchema = 'dbo';
    public $name = 'TipoGlosasLog';
    public $useTable = 'tipo_glosas_log';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');
    public $foreignKeyLog = 'codigo_tipo_glosas';
	public $displayField = 'codigo_tipo_glosas';
    public $validate = array(
        'codigo_tipo_glosas' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	
}
