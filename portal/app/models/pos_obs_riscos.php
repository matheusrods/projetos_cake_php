<?php
class PosObsRiscos extends AppModel
{

	public $name          = 'PosObsRiscos';
	var    $tableSchema   = 'dbo';
	var    $databaseTable = 'RHHealth';
	var    $useTable      = 'pos_obs_riscos';
	var    $primaryKey    = 'codigo';
	var    $recursive     = 2;

	function bindRiscos()
	{
		$this->bindModel(array(
			'hasOne' => array(
				'RiscosImpactos' => array(
					'className'  => 'RiscosImpactos',
					'foreignKey' => false,
					'conditions' => array('PosObsRiscos.codigo_arrtpa_ri = RiscosImpactos.codigo')
				),
				'PerigosAspectos' => array(
					'className'   => 'PerigosAspectos',
					'foreignKey' => false,
					'conditions' => array('PosObsRiscos.codigo_arrt_pa = PerigosAspectos.codigo')
				),
				'RiscosTipo' 	 => array(
					'className'  => 'RiscosTipo',
					'foreignKey' => false,
					'conditions' => array('PosObsRiscos.codigo_arrt = RiscosTipo.codigo')
				)
			)
		));
	}

	function unbindRiscos()
	{
		$this->unbindModel(array(
			'hasOne' => array(
				'RiscosImpactos',
				'PerigosAspectos',
				'RiscosTipo'
			)
		));
	}

	public function obterRiscos($codigo_observacao)
	{
		$conditions = array();
		$conditions['codigo_pos_obs_observacao'] = $codigo_observacao;

		return $this->find('all', array(
			//'fields' => $fields,
			'joins'      => array(),
			'conditions' => $conditions,
			'limit'      => 1,
			'recursive'  => 2
		));
	}
}
