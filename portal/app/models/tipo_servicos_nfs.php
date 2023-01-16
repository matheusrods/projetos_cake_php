<?php
class TipoServicosNfs extends AppModel {

	public $name		   	= 'TipoServicosNfs';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'tipo_servicos_nfs';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure', 'Loggable' => array('foreign_key' => 'codigo_tipo_servicos_nfs'), 'Containable');

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
			$conditions ['TipoServicosNfs.codigo'] = $data ['codigo'];
	
		if (! empty ( $data ['descricao'] ))
			$conditions ['TipoServicosNfs.descricao LIKE'] = '%' . $data ['descricao'] . '%';
	
		if (isset($data['ativo']) && $data['ativo'] != "")
			$conditions ['TipoServicosNfs.ativo'] = $data['ativo'];
        
		return $conditions;
	}
	
}