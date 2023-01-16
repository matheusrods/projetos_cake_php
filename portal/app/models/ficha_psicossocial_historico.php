<?php

class FichaPsicossocialHistorico extends AppModel {

    public $name = 'FichaPsicossocialHistorico';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
   	public $useTable = 'ficha_psicossocial_historico';
    public $primaryKey = 'codigo';
    public $actsAs = array('Secure', 'Containable');

	public $validate = array(
        'codigo_ficha_psicossocial' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o codigo da ficha psicossocial!'
        ),
        'link' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe o link!'
        ),
        'data_inclusao' => array(
            'rule' => 'notEmpty',
            'message' => 'Informe a data inclusao!'
        ),
    );
}
