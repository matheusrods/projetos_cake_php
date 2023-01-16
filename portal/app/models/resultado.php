<?php
class Resultado extends AppModel {
	
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');
	// valor que virá como padrão no select utilizando o paramento "list" no metodo "find"
	public $displayField = 'descricao';

	public $belongsTo = array(
		'Questionario' => array(
			'className' => 'Questionario',
			'foreignKey' => 'codigo_questionario',
			)
		);

	// public $validate = array(
	// 	'descricao' => array(
	// 		'rule' => 'notEmpty',
	// 		'message' => 'Este campo é obrigatório',
	// 		'allowEmpty' => false
	// 		),
	// 	'valor' => array(
	// 		'rule' => 'notEmpty',
	// 		'message' => 'Este campo é obrigatório',
	// 		'allowEmpty' => false
	// 		)
	// 	);

	public function incluir($data)
	{
		$codigo_questionario = $data['codigo_questionario'];
		unset($data['codigo_questionario']);

		if(empty($data)) {
			return $this->deleteAll(array('codigo_questionario' => $codigo_questionario));
		}

		$this->query('BEGIN TRANSACTION');
		if($this->deleteAll(array('codigo_questionario' => $codigo_questionario))) {
			if(parent::incluirTodos($data)) {
			 	$this->commit();
				return true;
			} else {
				$this->rollback();
				return false;
			}
		} else {
			$this->rollback();
			return false;
		}
	}

}