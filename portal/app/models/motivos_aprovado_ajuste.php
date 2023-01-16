<?php
class MotivosAprovadoAjuste extends AppModel {

	public $name		   	= 'MotivosAprovadoAjuste';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'motivos_aprovado_ajuste';
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
			$conditions ['MotivosAprovadoAjuste.codigo'] = $data ['codigo'];
	
		if (! empty ( $data ['descricao'] ))
			$conditions ['MotivosAprovadoAjuste.descricao LIKE'] = '%' . $data ['descricao'] . '%';
	
		if (isset($data['ativo']) && $data['ativo'] != "")
			$conditions ['MotivosAprovadoAjuste.ativo'] = $data['ativo'];
        
		return $conditions;
	}
}