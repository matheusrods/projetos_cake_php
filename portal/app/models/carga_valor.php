<?php

class CargaValor extends AppModel {

    var $name = 'CargaValor';
    var $tableSchema = 'informacoes';
    var $databaseTable = 'dbTeleconsult';
    var $useTable = 'carga_valor';
    var $primaryKey = 'codigo';
    var $actsAs = array('Secure');
    var $displayField = 'descricao';

    var $virtualFields = array(
    	'descricao' => "'De ' + publico.ufn_formata_moeda(CargaValor.valor_de,1) + ' a ' + publico.ufn_formata_moeda(CargaValor.valor_ate,1)"
    );

    public function __construct() {
        if ($this->useDbConfig == 'test_suite') {
            unset($this->virtualFields);
        }
        parent::__construct();

    }

    public function lista() {
    	return $this->find('list',array('order'=>'codigo'));
    }
}