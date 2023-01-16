<?php
class UsuarioAlertaTipo extends AppModel {

	var $name = 'UsuarioAlertaTipo';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'usuarios_alertas_tipos';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	var $validate = array(
		'codigo_usuario' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o usuário',
		),
		'codigo_alerta_tipo' => array(
			'rule' => 'notEmpty',
			'message' => 'Informe o tipo de alerta',
		),
	);

	function bindAlertaTipo(){
		$this->bindModel(array(
			'belongsTo' => array(
				'AlertaTipo' => array(
					'foreignKey' => 'codigo_alerta_tipo',
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

	function incluirAlertasTipos($dados){
		if($dados){
			try{
				$this->query('Begin Transaction');
		 		foreach ($dados as $dado){
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

	function listarTiposPorUsuario($codigo_usuario){
		return $this->find('all', array('conditions' => array('codigo_usuario' => $codigo_usuario)));
	}

	function listarPorTipo($codigo_alerta_tipo){
		return $this->find('all', array('conditions' => array('codigo_alerta_tipo' => $codigo_alerta_tipo)));
	}	

	function listarPorClienteTipoAlerta($codigo_cliente, $codigo_alerta_tipo){
		$this->bindUsuario();
		$conditions = Array(
			'Usuario.codigo_cliente' => $codigo_cliente,
			'UsuarioAlertaTipo.codigo_alerta_tipo' => $codigo_alerta_tipo
		);

		return $this->find('all', compact('conditions'));
	}	

	function excluirPorUsuario($codigo_usuario){
		$usuarios_alertas_tipos = $this->listarTiposPorUsuario($codigo_usuario); 

		try{
			$this->query('Begin Transaction');
	 		foreach ($usuarios_alertas_tipos as $usuario_alerta_tipo){
	 			if(!$this->excluir($usuario_alerta_tipo['UsuarioAlertaTipo']['codigo']))	
	 				throw new Exception();
	 		} 
	 		$this->commit();
	 		return true;
 		}catch (Exception $e){
 			$this->rollback();
 			return false;
 		}
	}

	function excluirPorTipoAlerta($codigo_alerta_tipo, $in_transaction = false){
		$usuarios_alertas_tipos = $this->listarPorTipo($codigo_alerta_tipo); 

		try{
			if(!$in_transaction) $this->query('Begin Transaction');
	 		foreach ($usuarios_alertas_tipos as $usuario_alerta_tipo){
	 			if(!$this->excluir($usuario_alerta_tipo['UsuarioAlertaTipo']['codigo']))	
	 				throw new Exception();
	 		} 
	 		if(!$in_transaction) $this->commit();
	 		return true;
 		}catch (Exception $e){
 			if(!$in_transaction) $this->rollback();
 			return false;
 		}
	}
}
?>
