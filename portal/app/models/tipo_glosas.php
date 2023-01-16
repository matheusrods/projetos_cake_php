<?php
class TipoGlosas extends AppModel {

	public $name		   	= 'TipoGlosas';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'tipo_glosas';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Loggable' => array('foreign_key' => 'codigo_tipo_glosas'), 'Containable');

	public $validate = array(
		'descricao' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe a descrição',
				'required' => true
			 ),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'Descrição já existe'
			)
		)			
	);
	
	public function converteFiltroEmCondition($data) {
		$conditions = array();

		if (! empty ( $data ['codigo'] ))
			$conditions ['TipoGlosas.codigo'] = $data ['codigo'];
	
		if (! empty ( $data ['visualizacao_do_cliente'] ))
			$conditions ['TipoGlosas.visualizacao_do_cliente LIKE'] = '%' . $data ['visualizacao_do_cliente'] . '%';

		if (! empty ( $data ['descricao'] ))
		$conditions ['TipoGlosas.descricao LIKE'] = '%' . $data ['descricao'] . '%';
	
		if (isset($data['ativo']) && $data['ativo'] != "")
			$conditions ['TipoGlosas.ativo'] = $data['ativo'];
        
		return $conditions;
	}
	
}