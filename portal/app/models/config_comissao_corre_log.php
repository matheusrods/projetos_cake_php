<?php

class ConfigComissaoCorreLog extends AppModel {

	var $name = 'ConfigComissaoCorreLog';
	var $tableSchema = 'vendas';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'configuracao_comissoes_corretora_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	
	public function listaPorCorretoraLog($filtro){
		$Corretora =& ClassRegistry::Init('Corretora');
		$Produto =& ClassRegistry::Init('Produto');
		$Servico =& ClassRegistry::Init('Servico');
		$Usuario =& ClassRegistry::Init('Usuario');

		$joins = array(
			array(
				'table' 	=> $Corretora->databaseTable.'.'.$Corretora->tableSchema.'.'.$Corretora->useTable,
				'alias'		=> 'Corretora',
				'conditions'=> 'Corretora.codigo = ConfigComissaoCorreLog.codigo_corretora',
			),
			array(
				'table' 	=> $Produto->databaseTable.'.'.$Produto->tableSchema.'.'.$Produto->useTable,
				'alias'		=> 'Produto',
				'conditions'=> 'Produto.codigo = ConfigComissaoCorreLog.codigo_produto',
			),
			array(
				'table' 	=> $Servico->databaseTable.'.'.$Servico->tableSchema.'.'.$Servico->useTable,
				'alias'		=> 'Servico',
				'conditions'=> 'Servico.codigo = ConfigComissaoCorreLog.codigo_servico',
			),
			array(
				'table' 	=> $Usuario->databaseTable.'.'.$Usuario->tableSchema.'.'.$Usuario->useTable,
				'alias'		=> 'UsuarioInclusao',
				'conditions'=> 'UsuarioInclusao.codigo = ConfigComissaoCorreLog.codigo_usuario_inclusao',
			),
			array(
				'table' 	=> $Usuario->databaseTable.'.'.$Usuario->tableSchema.'.'.$Usuario->useTable,
				'alias'		=> 'UsuarioAlterou',
				'conditions'=> 'UsuarioAlterou.codigo = ConfigComissaoCorreLog.codigo_usuario_alteracao ',
			),
		);
		
		if(!empty($filtro['codigo_conf_comissoes_corretora']))$conditions['codigo_conf_comissoes_corretora'] = $filtro['codigo_conf_comissoes_corretora'];
		if(!empty($filtro['codigo_produto']))$conditions['codigo_produto'] 	   = $filtro['codigo_produto'];
		//if(!empty($filtro['codigo_servico']))$conditions['codigo_servico'] 	   = $filtro['codigo_servico'];
		
		
		$limit 		= 50;
		$order 		= array('ConfigComissaoCorreLog.data_alteracao desc');
		$fields 	= array(
			'UsuarioAlterou.nome',
			'UsuarioInclusao.nome',
			'ConfigComissaoCorreLog.*',
			"CASE	
		      WHEN  ConfigComissaoCorreLog.acao_sistema = 0 THEN 'Inclusão'
		      WHEN  ConfigComissaoCorreLog.acao_sistema = 1 THEN 'Altualização'
		      WHEN  ConfigComissaoCorreLog.acao_sistema = 2 THEN 'Exclusão'
		      End AS acao",	
			"Corretora.nome",
			"Produto.descricao",
			"Servico.descricao",
		);
		
		return compact('conditions','joins','limit','order','fields');
	}

	
	
}

?>
