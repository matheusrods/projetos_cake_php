<?php

class GrupoHomDetalheLog extends AppModel {

	public $name = 'GrupoHomDetalheLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'grupos_homogeneos_exposicao_detalhes_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_grupos_homogeneos_exposicao_detalhes';
	public $actsAs = array('Secure');
	public $displayField = 'codigo_grupos_homogeneos_exposicao_detalhes';
    public $validate = array(
        'codigo_grupos_homogeneos_exposicao_detalhes' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}