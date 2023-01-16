<?php

class GrupoExposicaoRiscoLog extends AppModel {

	public $name = 'GrupoExposicaoRiscoLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'grupos_exposicao_risco_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_grupos_exposicao_risco';
	public $actsAs = array('Secure');
	public $displayField = 'codigo_grupos_exposicao_risco';
    public $validate = array(
        'codigo_grupos_exposicao_risco' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}