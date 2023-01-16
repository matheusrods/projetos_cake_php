<?php

class PrevencaoRiscoAmbientalLog extends AppModel {

	public $name = 'PrevencaoRiscoAmbientalLog';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'prevencao_riscos_ambientais_log';
	public $primaryKey = 'codigo';
	public $foreignKeyLog = 'codigo_prevencao_riscos_ambientais';
	public $actsAs = array('Secure');
	public $displayField = 'codigo_prevencao_riscos_ambientais';
    public $validate = array(
        'codigo_prevencao_riscos_ambientais' => array(
            'rule' => 'notEmpty',
            'message' => 'Campo Obrigatório'
        )
    );	

}