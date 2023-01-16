<?php

class ServicoLog extends AppModel {

	public $name = 'ServicoLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'servico_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_servico';
	public $actsAs = array('Secure');
	public $displayField = 'codigo_servico';
    public $validate = array(
        'codigo_servico' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}