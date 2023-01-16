<?php

class ProfissionalTipo extends AppModel {

	var $name = 'ProfissionalTipo';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'profissional_tipo';
	var $primaryKey = 'codigo';
    var $displayField = 'descricao';
	var $actsAs = array('Secure');
	
	const OUTROS = -1;
	const CARRETEIRO = 1;
	const AGREGADO = 2;
	const FUNCIONARIO_MOTORISTA = 3;
	const PROPRIETARIO = 4;

	
	public function precisaDadosCNH($profissional_tipo){
		return $profissional_tipo == ProfissionalTipo::CARRETEIRO ||
			$profissional_tipo == ProfissionalTipo::AGREGADO ||
			$profissional_tipo == ProfissionalTipo::FUNCIONARIO_MOTORISTA;
	}
	
	public function getProfissionalTipoByCodigo($codigo_profissional_tipo) {
		try {
			if(empty($codigo_profissional_tipo))
				throw new Exception('Código obrigatório!');

			$profissional_tipo = $this->find('first', array(
					'conditions' => array(
							'codigo' => $codigo_profissional_tipo
					)
			));

			return $profissional_tipo;
		} catch(Exception $e) {
			return false;
		}
	}
}

?>
