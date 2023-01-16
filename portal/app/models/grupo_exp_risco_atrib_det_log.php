<?php

class GrupoExpRiscoAtribDetLog extends AppModel {

	public $name = 'GrupoExpRiscoAtribDetLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'grupo_exposicao_riscos_atributos_detalhes_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_grupo_exposicao_riscos_atributos_detalhes';
	public $actsAs = array('Secure');
	public $displayField = 'codigo_grupo_exposicao_riscos_atributos_detalhes';
    public $validate = array(
        'codigo_grupo_exposicao_riscos_atributos_detalhes' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}