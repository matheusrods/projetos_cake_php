<?php

class GrupoExposicaoRiscoEpcLog extends AppModel {

	public $name = 'GrupoExposicaoRiscoEpcLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'grupos_exposicao_risco_epc_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_grupos_exposicao_risco_epc';
	public $actsAs = array('Secure');
	public $displayField = 'codigo_grupos_exposicao_risco_epc';
    public $validate = array(
        'codigo_grupos_exposicao_risco_epc' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}