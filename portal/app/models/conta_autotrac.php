<?php
class ContaAutotrac extends AppModel {
	var $name = 'ContaAutotrac';
	var $tableSchema = 'dbo';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'conta_autotrac';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	public function inserir($data){
		
		$conta = array(
				'ContaAutotrac'	=> array(
						'codigo_cliente'			=> $data['codigo'],
						'possui_conta'	 			=> $data['possui_conta'],
						'conta_buonny'				=> $data['conta_buonny'],
						'macro_buonny'				=> $data['macro_buonny'],
						'analista'					=> $data['analista'],
						'telefone_contato'			=> $data['telefone_contato'],
						'data_inclusao'				=> date('Y-m-d H:i:s')
					)
				);

		if($this->save($conta))
			return true;
		else
			return false;
		
	}

	public function alterar($data){
		
		$conta = array(
				'ContaAutotrac'	=> array(
						'codigo'					=> $data['codigo'],
						'possui_conta'	 			=> $data['possui_conta'],
						'conta_buonny'				=> $data['conta_buonny'],
						'macro_buonny'				=> $data['macro_buonny'],
						'analista'					=> $data['analista'],
						'telefone_contato'			=> $data['telefone_contato']
					)
				);

		if($this->save($conta))
			return true;
		else
			return false;
		
	}

}
?>