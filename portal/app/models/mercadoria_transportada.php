<?php
class MercadoriaTransportada extends AppModel {
	var $name = 'MercadoriaTransportada';
	var $tableSchema = 'dbo';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'mercadorias_transportadas';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	public function inserir($data){

		$mercadorias = array(
				'MercadoriaTransportada'	=> array(
						'codigo_cliente' 			=> $data['codigo'],
						'descricao' 				=> $data['descricao'],
						'representativo'			=> $data['representativo'],
						'data_inclusao'				=> date('Y-m-d H:i:s')
					)
				);

		if($this->save($mercadorias))
			return true;
		else
			return false;

	}

	public function alterar($data){

		$mercadorias = array(
				'MercadoriaTransportada'	=> array(
						'codigo' 					=> $data['codigo'],
						'descricao' 				=> $data['descricao'],
						'representativo'			=> $data['representativo']
					)
				);

		if($this->save($mercadorias))
			return true;
		else
			return false;
		
	}
}
?>