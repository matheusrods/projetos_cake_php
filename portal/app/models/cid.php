<?php
class Cid extends AppModel {

	public $name		   	= 'Cid';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'cid';
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
		),
		'codigo_cid10' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o CID10',
				'required' => true
			 ),
			'isUnique' => array(
				'rule' => 'isUnique',
				'message' => 'CID10 já existe'
			)
		)			
	);
	
	function converteFiltroEmCondition($data) {
		$conditions = array();
		if (!empty($data['codigo_cid10']))
			$conditions['Cid.codigo_cid10'] =  $data['codigo_cid10'];
	
		if (! empty ( $data ['descricao'] ))
			$conditions ['Cid.descricao LIKE'] = '%' . $data ['descricao'] . '%';
	
		if (isset($data['ativo']) && $data['ativo'] != "")
			$conditions ['Cid.ativo'] = $data['ativo'];
        
		return $conditions;
	}
	
}