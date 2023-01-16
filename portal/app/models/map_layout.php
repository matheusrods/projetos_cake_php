<?php
class MapLayout extends AppModel
{

	public $name          = 'MapLayout';
	public $tableSchema   = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable      = 'map_layout';
	public $primaryKey    = 'codigo';
	public $actsAs        = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_map_layout'));

	protected $numbers = array(
		'codigo_empresa',
		'codigo_cliente',
		'tipo_layout',
		'ativo',
		'ignora_primeira_linha'
	);

	protected $filtrosValidos = array(
		'codigo_cliente' => "",
		'nome' => "LIKE",
		'dsname' => "LIKE",
		'apelido' => "LIKE"
	);

	/**
	 * Obter model com seus binds
	 * 
	 * @param int $codigo
	 * @return array|null
	 */
	public function with_bind($codigo)
	{
		try {
			$this->bindDetalhe();
			$data = $this->carregar($codigo);
			$this->unbindDetalhe();
			if (isset($data['MapLayoutDetalhe'])) {
				foreach ($data['MapLayoutDetalhe'] as $key => $field) {
					if ($field['ativo'] == 0) {
						unset($data['MapLayoutDetalhe'][$key]);
					}
				}
			}

			return $data;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Obtêm registros com seus binds
	 * 
	 * @return array
	 */
	public function with_all_binds($codigo_cliente) {
		$this->bindDetalhe();
		$data = $this->find('all', array('conditions' => array('MapLayout.ativo' => 1,'MapLayout.codigo_cliente' => $codigo_cliente)));
		// debug($data);exit;
		$this->unbindDetalhe();

		return $data;
	}

	/**
	 * Convert model fillable
	 * 
	 * @param array $data
	 * @param array $attributes
	 * @param \Closure $onValidate
	 * 
	 * @return array
	 */
	public function withConversion($data, $attributes, $convert)
	{
		$parsed = array();
		foreach ($data as $key => $value) {
			if (in_array($key, $attributes)) {
				$value = $convert($value);
			}
			$parsed[$key] = $value;
		}

		return $parsed;
	}

	/**
	 * Parse all internal numbers to integer
	 * 
	 * @param array $data
	 * @return array
	 */
	private function parseNumbers($data)
	{
		return $this->withConversion($data, $this->numbers, function ($value) {
			return (int) $value;
		});
	}

	/**
	 * Save resource
	 * 
	 * @param array $data
	 * @param bool $validate
	 * @param array $fieldList
	 * @return boolean
	 */
	public function incluir($data = null, $validate = true, $fieldList = array())
	{
		$fillable = $this->parseNumbers($data);
		return parent::incluir($fillable, $validate, $fieldList);
	}

	/**
	 * @param array $filtros
	 * @return array
	 */
	public function converteFiltroEmCondition($filtros) {
		$conditions = array();
		$filtrosValidos = array_keys($this->filtrosValidos);
		$_filtros = is_array($filtros) ? $filtros : array();
		foreach($_filtros as $key => $filtro) {
			if(empty($filtro) || in_array($key, $filtrosValidos) == false) {
				continue;
			}
			$operator = $this->filtrosValidos[$key];

			$conditions["{$this->name}.{$key} {$operator}"] = $operator == "LIKE" ? "%{$filtro}%" : $filtro;
		}
		
		return $conditions;
	}


	function bindDetalhe()
	{
		$this->bindModel(array(
			'hasMany' => array(
				'MapLayoutDetalhe' => array('foreignKey' => 'codigo_map_layout','conditions' => array('ativo' => 1)),
			)
		));
	}

	function unbindDetalhe()
	{
		$this->unbindModel(array(
			'hasMany' => array(
				'MapLayoutDetalhe'
			)
		));
	}
}
