<?php

class AnexoAssinaturaEletronica extends AppModel {
	public $name		   	= 'AnexoAssinaturaEletronica';
	public $databaseTable 	= 'RHHealth';
	public $tableSchema   	= 'dbo';
	public $useTable	   	= 'anexos_assinatura_eletronica';
    public $primaryKey	   	= 'codigo';
	public $actsAs          = array('Secure');
	
	public $validate = array(
        'codigo_medico' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o medico',
            'required' => true
		),
		'caminho_arquivo' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o caminho do arquivo',
            'required' => true
        ),
	);
}