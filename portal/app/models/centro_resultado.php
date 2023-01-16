<?php
class CentroResultado extends AppModel
{
	public $name          = 'CentroResultado';
	public $tableSchema   = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable      = 'centro_resultado';
	public $primaryKey    = 'codigo';
	public $slugedTable   = "Centro resultado";
	public $actsAs		  = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_centro_resultado'));
	
	public function getCentroResultadoBu($codigo_cliente)
	{

		$fields = array(			
			'ClienteBu.codigo',
			'ClienteBu.descricao'
		);

		$joins = array(
			array(
				'table' => "cliente_bu",
				'alias' => 'ClienteBu',
				'conditions' => 'CentroResultado.codigo_cliente_bu = ClienteBu.codigo',
				'type' => 'INNER',
			)
		);

		$conditions = array(
			'CentroResultado.codigo_cliente_alocacao' => $codigo_cliente,
			'CentroResultado.ativo' => 1,
			'ClienteBu.ativo' => 1			
		);

		$groupBy = array(			
			'ClienteBu.codigo',
			'ClienteBu.descricao'
		);

		$bu = $this->find('list', array(
				'fields' => $fields,
				'joins' => $joins,
				'conditions' => $conditions,
				'order' => array(
					'ClienteBu.descricao ASC'
				),
				'group' => $groupBy
			));

		return $bu;			
	}

	public function getCentroResultadoOpco($codigo_cliente, $codigo_cliente_bu)
	{

		$fields = array(			
			'ClienteOpco.codigo',
			'ClienteOpco.descricao'
		);

		$joins = array(
			array(
				'table' => "cliente_opco",
				'alias' => 'ClienteOpco',
				'conditions' => 'CentroResultado.codigo_cliente_opco = ClienteOpco.codigo',
				'type' => 'INNER',
			)
		);

		$conditions = array(
			'CentroResultado.codigo_cliente_alocacao' => $codigo_cliente,
			'CentroResultado.codigo_cliente_bu' => $codigo_cliente_bu,
			'CentroResultado.ativo' => 1,
			'ClienteOpco.ativo' => 1			
		);

		$groupBy = array(			
			'ClienteOpco.codigo',
			'ClienteOpco.descricao'
		);

		$opco = $this->find('list', array(
				'fields' => $fields,
				'joins' => $joins,
				'conditions' => $conditions,
				'order' => array(
					'ClienteOpco.descricao ASC'
				),
				'group' => $groupBy
			));

		return $opco;			
	}
	
}
