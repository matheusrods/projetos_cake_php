<?php
class PdaConfigRegraCondicao extends AppModel {
	var $name = 'PdaConfigRegraCondicao';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'pda_config_regra_condicao';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure', 'Containable', 'Loggable' => array('foreign_key' => 'codigo_pda_config_regra_condicao'));

	public function get_pos_configuracoes($codigo_cliente)
	{

		$query = "SELECT TOP 1 * from RHHealth.dbo.pos_configuracoes WHERE chave = 'TEMPOTRATATIVAOBSERVACAO' AND ativo = 1 AND codigo_cliente = {$codigo_cliente} ORDER BY codigo DESC";
		$dados = $this->query($query);
		// debug($dados);exit;

		if(!empty($dados)) {
			return $dados;
		}

		return false;

	}

	
}
