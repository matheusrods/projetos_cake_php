<?php

class FichaPsicossocialResposta extends AppModel {

    public $name            = 'FichaPsicossocialResposta';
    public $tableSchema     = 'dbo';
    public $databaseTable   = 'RHHealth';
   	public $useTable        = 'ficha_psicossocial_respostas';
    public $primaryKey      = 'codigo';
    public $actsAs		   	= array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_ficha_psicossocial_resposta'));
}