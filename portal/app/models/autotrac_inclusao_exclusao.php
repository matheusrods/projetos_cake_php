<?php
class AutotracInclusaoExclusao extends AppModel {
	var $name = 'AutotracInclusaoExclusao';
	var $primaryKey = 'codigo';
	var $databaseTable = 'dbBuonny';
	var $tableSchema = 'vendas';
	var $useTable = 'autotrac_inclusao_exclusao';
	var $actsAs = array('Secure');	

	public function valida_nome_colunas($linha){
		$valida_titulo = array_fill(2, 16, '');
        $valida_titulo[2]  = utf8_decode('Nome do Veículo');
        $valida_titulo[5]  = utf8_decode('Nome da Conta');
        $valida_titulo[7]  = utf8_decode('Tipo do Serviço');
        $valida_titulo[8]  = utf8_decode('Status');
        $valida_titulo[9]  = utf8_decode('Data do Status');
        $valida_titulo[12] = utf8_decode('Usuário');
        unset($valida_titulo[6]);
		return ($valida_titulo == $linha);
    }

}