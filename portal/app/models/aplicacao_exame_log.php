<?php

class AplicacaoExameLog extends AppModel {

	public $name = 'AplicacaoExameLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'aplicacao_exames_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_aplicacao_exames';
	public $actsAs = array('Secure');
	public $displayField = 'codigo_aplicacao_exames';
    public $validate = array(
        'codigo_aplicacao_exames' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}