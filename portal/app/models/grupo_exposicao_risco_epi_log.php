<?php

class GrupoExposicaoRiscoEpiLog extends AppModel {

	public $name = 'GrupoExposicaoRiscoEpiLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'grupos_exposicao_risco_epi_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_grupos_exposicao_risco_epi';
	public $actsAs = array('Secure');
	public $displayField = 'codigo_grupos_exposicao_risco_epi';
    public $validate = array(
        'codigo_grupos_exposicao_risco_epi' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}