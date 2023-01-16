<?php
class MotivosDesconto extends AppModel {

	public $name		   	= 'MotivosDesconto';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'motivos_desconto';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Loggable' => array('foreign_key' => 'codigo_motivos_desconto'), 'Containable');

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
			$conditions ['MotivosDesconto.codigo'] = $data ['codigo'];
	
		if (! empty ( $data ['descricao'] ))
			$conditions ['MotivosDesconto.descricao LIKE'] = '%' . $data ['descricao'] . '%';
	
		if (isset($data['ativo']) && $data['ativo'] != "")
			$conditions ['MotivosDesconto.ativo'] = $data['ativo'];
        
		return $conditions;
	}
	
}