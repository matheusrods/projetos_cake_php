<?php

class FichaPsicossocialPergunta extends AppModel {

    public $name = 'FichaPsicossocialPergunta';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
    public $useTable = 'ficha_psicossocial_perguntas';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure');  

    /*public $hasMany = array(
        'FichaPsicossocialResposta' => array(
            'className' => 'FichaPsicossocialResposta',
            'foreignKey' => 'codigo_ficha_psicossocial_perguntas',
            ),
        );*/
}