<?php

class Absenteismo extends AppModel {

	public $name 			= 'Absenteismo'; 
	public $tableSchema 	= 'dbo';
	public $databaseTable 	= 'RHHealth';
	public $useTable 		= false;
	public $actsAs = array('Secure');



	public function subquery_relatorio($conditions = array()){

		$this->ClienteFuncionario =& ClassRegistry::init('ClienteFuncionario');

		$conditions_subquery = array();
		$conditions_atestado = array('Atestado.codigo_cliente_funcionario = ClienteFuncionario.codigo');

		foreach($conditions as $key => $condi){
			if(strpos($key,'Atestado.') !== false){
				$conditions_atestado[$key] = $condi; 
			}else{
				$conditions_subquery[$key] =  $condi; 

			}

		}


		$joins = array(
			array(
				'table' => 'funcionario_setores_cargos',
				'alias'	=> 'FuncionarioSetorCargo',
				'type'  => 'INNER',
				'conditions' => array("FuncionarioSetorCargo.codigo = (Select TOP 1 codigo from funcionario_setores_cargos Where codigo_cliente_funcionario = ClienteFuncionario.codigo AND ((data_fim = '' OR data_fim IS NULL) OR (data_fim is not null AND ClienteFuncionario.ativo = 0)) ORDER by codigo DESC) ")
			),			
			array(
				'table' => 'funcionarios',
				'alias'	=> 'Funcionario',
				'type'  => 'INNER',
				'conditions' => array('Funcionario.codigo = ClienteFuncionario.codigo_funcionario')
			),				
			array(
				'table' => 'atestados',
				'alias'	=> 'Atestado',
				'type'  => 'LEFT',
				'conditions' => $conditions_atestado
			)
			);

		$fields = array('DISTINCT Funcionario.codigo as funcionario ',
						'(CASE WHEN Atestado.codigo IS NOT NULL THEN 1 ELSE NULL END) as atestado ');


		$subquery = $this->ClienteFuncionario->find('sql',array(
			'conditions' => $conditions_subquery,
			'fields' => $fields,
			'joins' => $joins,
			'recursive' => -1
			));

		return $subquery;
	}

	public function relatorio_absenteismo_analitico($conditions = array()) {

		$subquery_relatorio = $this->subquery_relatorio($conditions);
		$dbo = $this->getDataSource();
		$fields = array('COUNT(CASE WHEN atestado IS NULL THEN 1 END) as sem_atestado',
						'COUNT(CASE WHEN atestado IS NOT NULL THEN 1 END) as com_atestado',
						'COUNT(*) as total_funcionarios'
			);

		$query = $dbo->buildStatement(
			array(
				'fields' => $fields,
				'table' => "({$subquery_relatorio})",
				'alias' => 'analitico',
				'schema' => null,
				'alias' => 'absenteismo',
				'limit' => null,
				'offset' => null,
				'joins' => array(),
				'conditions' => null,
				'order' => null,
				//'group' => $group
				), $this
			);

		return $this->query($query);

	}


}