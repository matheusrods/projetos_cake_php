<?php
class GpraLog extends AppModel {

	public $name = 'GpraLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'grupos_prevencao_riscos_ambientais_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_grupos_prevencao_riscos_ambientais';
	public $actsAs = array('Secure');
	public $displayField = 'codigo_grupos_prevencao_riscos_ambientais';
    public $validate = array(
        'codigo_grupos_prevencao_riscos_ambientais_log' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}