<?php

class GrupoExposicaoLog extends AppModel {

	public $name = 'GrupoExposicaoLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'grupo_exposicao_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_grupo_exposicao';
	public $actsAs = array('Secure');
	public $displayField = 'codigo_grupo_exposicao';
    public $validate = array(
        'codigo_grupo_exposicao' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}