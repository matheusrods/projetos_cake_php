<?php
class LogObjetivoExcFat extends AppModel {
	var $name          = 'LogObjetivoExcFat';
	var $tableSchema   = 'vendas';
	var $databaseTable = 'dbBuonny';
	var $useTable      = 'log_objetivo_exc_fat';
	var $primaryKey    = 'codigo';

  	function incluir_log($data,$operacao){
  		$dados = $data['ObjetivoExcecaoFaturamento'];
  		$dados['codigo_objetivo'] = $data['ObjetivoExcecaoFaturamento']['codigo'];
  		$dados['data_operacao'] = date('Y-m-d H:i:s');
  		$dados['operacao'] = $operacao;

  		$incluir['LogObjetivoExcFat'] = $dados;
  		unset($incluir['LogObjetivoExcFat']['codigo']);
  		return parent::incluir($incluir);
  	}
}
?>