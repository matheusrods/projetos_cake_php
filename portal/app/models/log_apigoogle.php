<?php
class LogApigoogle extends AppModel {

	public $name = 'LogApigoogle';
	public $tableSchema = 'dbo';
	public $databaseTable = 'RHHealth';
	public $useTable = 'log_apigoogle';
	public $primaryKey = 'codigo';
	public $actsAs = array('Secure');

	public function verificaLog($url) {
		
		$resultado = $this->find('first', array('conditions' => array('CONVERT(VARCHAR(MAX),url_chamada)' => $url)));
		
		if(count($resultado)) {
			return json_decode($resultado['LogApigoogle']['retorno']);
		}
		
		return false;
	}
}
?>
