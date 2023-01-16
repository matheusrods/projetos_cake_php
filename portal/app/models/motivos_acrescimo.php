<?php
class MotivosAcrescimo extends AppModel {

	public $name		   	= 'MotivosAcrescimo';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'motivos_acrescimo';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure');

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
			$conditions ['MotivosAcrescimo.codigo'] = $data ['codigo'];
	
		if (! empty ( $data ['descricao'] ))
			$conditions ['MotivosAcrescimo.descricao LIKE'] = '%' . $data ['descricao'] . '%';
	
		if (isset($data['ativo']) && $data['ativo'] != "")
			$conditions ['MotivosAcrescimo.ativo'] = $data['ativo'];
        
		return $conditions;
	}
	
}