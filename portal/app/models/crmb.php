<?php
class Crmb extends AppModel {
	public $name = 'Crmb';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
	public $useTable = 'clientes_responsaveis_monitoracao_biologicas';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

	public $belongsTo = array(
		'Medico' => array(
			'className' => 'Medico',
			'foreignKey' => 'codigo_medico' 
			),
		'Cliente' => array(
			'className' => 'Cliente',
			'foreignKey' => 'codigo_cliente'
			)
		);

	/**
	 * beforeFilter callback
	 *
	 * @return void
	 */

	

	public function converteFiltroEmCondition($data) {
		$conditions = array();
		
		if (!empty($data['codigo']))
			$conditions['Crmb.codigo'] = $data['codigo'];

		if (! empty ( $data ['nome'] ))
			$conditions ['Medico.nome LIKE'] = '%' . $data ['nome'] . '%';

		return $conditions;
	}  

	public function incluir($data)
	{
		return parent::incluir($data);
	}

	public function atualizar($data)
	{
		return parent::atualizar($data);
	}


}