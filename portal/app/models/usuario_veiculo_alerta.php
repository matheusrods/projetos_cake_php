<?php
class UsuarioVeiculoAlerta extends AppModel {

	var $name = 'UsuarioVeiculoAlerta';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'usuarios_veiculos_alertas';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $validate = array(
		'codigo_usuario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o usuário',
		),
		'codigo_veiculo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o veículo',
		),
	);

	function bindVeiculo(){
		$this->bindModel(array(
			'belongsTo' => array(
				'Veiculo' => array(
					'foreignKey' => 'codigo_veiculo',
				),
			),
		));
	}

	function bindUsuario(){
		$this->bindModel(array(
			'belongsTo' => array(
				'Usuario' => array(
					'foreignKey' => 'codigo_usuario', 
				),
			),
		));
	}

	function incluirVeiculosAlerta($dados){
		if($dados){
			try{
				$this->query('Begin Transaction');
		 		foreach ($dados as $dado){
		 			$usuario_veiculo_alerta = $this->find('all',array('conditions' => array('codigo_usuario' => $dado['UsuarioVeiculoAlerta']['codigo_usuario'],'codigo_veiculo' => $dado['UsuarioVeiculoAlerta']['codigo_veiculo'])));
		 			if(!$usuario_veiculo_alerta)
			 			if(!$this->incluir($dado))	
			 				throw new Exception();
		 		} 
		 		$this->commit();
		 		return true;
	 		}catch (Exception $e){
	 			$this->rollback();
	 			return false;
	 		}
		
		}
	}

	function listarPorUsuario($codigo_usuario){
		return $this->find('all', array('conditions' => array('codigo_usuario' => $codigo_usuario)));
	}

	function listarPorVeiculo($codigo_veiculo){
		return $this->find('all', array('conditions' => array('codigo_veiculo' => $codigo_veiculo)));
	}
}
?>
