<?php

class GrupoExpRiscoFonteGeraLog extends AppModel {

	public $name = 'GrupoExpRiscoFonteGeraLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'grupos_exposicao_risco_fontes_geradoras_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_grupos_exposicao_risco_fontes_geradoras';
	public $actsAs = array('Secure');
	public $displayField = 'descricao';
    public $validate = array(
        'codigo_grupos_exposicao_risco_fontes_geradoras' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}