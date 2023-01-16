<?php
class AutotracParametro extends AppModel {
	var $name = 'AutotracParametro';
	var $primaryKey = 'codigo';
	var $databaseTable = 'dbBuonny';
	var $tableSchema = 'vendas';
	var $useTable = 'autotrac_parametro';
	var $actsAs = array('Secure');	
    var $validate = array(
        'taxa_administrativa' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe a taxa administrativa.',
             ),

        ),
        'percentual_imposto' => array(
            'notEmpty' => array(
                'rule' => 'notEmpty',
                'message' => 'Informe o imposto.',
             ),
        ),
    );

}