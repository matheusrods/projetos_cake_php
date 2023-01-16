<?php
class Crra extends AppModel {
	public $name = 'Crra';
    public $tableSchema = 'dbo';
    public $databaseTable = 'RHHealth';
	public $useTable = 'clientes_responsaveis_registros_ambientais';
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

	public function converteFiltroEmCondition($data) {
		$conditions = array();

		if (!empty($data['codigo']))
			$conditions['Crra.codigo'] = $data['codigo'];

		if (!empty($data['codigo_cliente']))
			$conditions['Crra.codigo_cliente'] = $data['codigo_cliente'];

		if (! empty ( $data ['medico'] ))
			$conditions ['Medico.nome LIKE'] = '%' . $data ['medico'] . '%';

		if(isset($data['codigo_conselho_profissional']) && isset($data['numero_conselho'])){
			if(!empty($data['codigo_conselho_profissional']) && !empty($data['numero_conselho'])){
				$conditions['Medico.codigo_conselho_profissional'] = $data['codigo_conselho_profissional'];
				$conditions['Medico.numero_conselho'] = $data['numero_conselho'];
				
			}
		}

		if(isset($data['data_inicial']) && !empty($data['data_inicial']))   
			$conditions["Crra.data_inclusao >= "] = Comum::dateToDb($data['data_inicial']) . " 23:59:59";

		if(isset($data['data_final']) && !empty($data['data_final']))
			$conditions["Crra.data_inclusao <= "] = Comum::dateToDb($data['data_final']) . " 00:00:00";

		return $conditions;
	} 
}