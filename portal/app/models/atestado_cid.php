<?php
class AtestadoCid extends AppModel {

	public $name		   	= 'AtestadoCid';
	public $tableSchema   	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable	   	= 'atestados_cid';
	public $primaryKey	   	= 'codigo';
	public $actsAs		   	= array('Secure','Loggable' => array('foreign_key' => 'codigo_atestado_cid'));

	var $validate = array(
		'codigo_atestado' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código de Atestado!'
		),
		'codigo_cid' => array(
			'rule' => 'notEmpty',
			'message' => 'Sem Código do Cid!'
		)
	);

	public function getCID($codigo){
		
		$fields = array('cid.descricao as doenca');

		$joins  = array(
						array('table' => 'cid',
							  'alias' => 'CID',
							  'type' => 'INNER',
							  'conditions' => 'AtestadoCid.codigo_cid = CID.codigo'
						)
		);

		$conditions = array('AtestadoCid.codigo_atestado' => $codigo);

		$retorno = $this->find('all', array('fields' => $fields, 'joins' => $joins, 'conditions' => $conditions));

		return Set::extract( '{n}.0', $retorno);

	}//FINAL FUNCTION getCID
	
}//FINAL CLASS AtestadoCid