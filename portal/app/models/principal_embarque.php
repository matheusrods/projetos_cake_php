<?php
class PrincipalEmbarque extends AppModel {
	var $name = 'PrincipalEmbarque';
	var $tableSchema = 'dbo';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'principais_embarques';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	public function inserir($data){
		
		$embarque = array(
				'PrincipalEmbarque'	=> array(
						'codigo_cliente'			=> $data['codigo'],
						'cidade_origem' 			=> $data['cidade_origem'],
						'estado_origem'				=> $data['estado_origem'],
						'cidade_destino'			=> $data['cidade_destino'],
						'estado_destino'			=> $data['estado_destino'],
						'percentual'				=> $data['percentual'],
						'data_inclusao'				=> date('Y-m-d H:i:s')
					)
				);

		if($this->save($embarque))
			return true;
		else
			return false;
		
	}

	public function alterar($data){
		
		$embarque = array(
				'PrincipalEmbarque'	=> array(
						'codigo'					=> $data['codigo'],
						'cidade_origem' 			=> $data['cidade_origem'],
						'estado_origem'				=> $data['estado_origem'],
						'cidade_destino'			=> $data['cidade_destino'],
						'estado_destino'			=> $data['estado_destino'],
						'percentual'				=> $data['percentual']
					)
				);

		if($this->save($embarque))
			return true;
		else
			return false;
		
	}

}
?>