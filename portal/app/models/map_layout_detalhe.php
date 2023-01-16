<?php
class MapLayoutDetalhe extends AppModel
{

	var $name          = 'MapLayoutDetalhe';
	var $tableSchema   = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable      = 'map_layout_detalhe';
	var $primaryKey    = 'codigo';
	var $actsAs        = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_map_layout'));
	
	protected $numbers = array(
		'codigo_empresa',
		'codigo_cliente',
		'tipo_layout',
		'ativo'
	);

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
}
