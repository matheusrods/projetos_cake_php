<?php

class HospitaisEmergencia extends AppModel {

	public $name		   	= 'HospitaisEmergencia';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'hospitais_emergencia';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure');
	var $validate = array(
		'nome' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o nome do hospital',
		),
		'cep' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o CEP',
		),
		'numero' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o numero',
		),
	);


	public function converteFiltroEmCondition($data) 
	{

        $conditions = array();
        
        if (!empty($data['codigo_cliente'])){
            $conditions['GrupoEconomico.codigo_cliente'] = $data['codigo_cliente'];
        }

         if (!empty($data['codigo_unidade'])){
            $conditions['GrupoEconomicoCliente.codigo_cliente'] = $data['codigo_unidade'];
        }

        return $conditions;
    }
	
}//fim class hospital_emergencia
?>