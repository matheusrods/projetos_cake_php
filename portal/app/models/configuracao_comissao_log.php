<?php

class ConfiguracaoComissaoLog extends AppModel {

	var $name = 'ConfiguracaoComissaoLog';
	var $tableSchema = 'vendas';
	var $databaseTable = 'dbBuonny';
	var $useTable = 'configuracao_comissoes_log';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');
	
	public function convertFiltrosEmConditions( $filtros ){
		$conditions = array();
		if(!empty($filtros['codigo_configuracao_comissoes']))
			$conditions['codigo_configuracao_comissoes'] = $filtros['codigo_configuracao_comissoes'];										
		if(!empty($filtros['codigo_endereco_regiao']))
			$conditions['codigo_endereco_regiao'] = $filtros['codigo_endereco_regiao'];
		if(!empty($filtros['codigo_produto_naveg']))
			$conditions['codigo_produto_naveg'] = $filtros['codigo_produto_naveg'];
		return $conditions;
	}

	public function listaLogFilial($filtro){
		$this->bindModel(array('belongsTo' => array(
			'EnderecoRegiao' => array('foreignKey' => 'codigo_endereco_regiao'),
			'NProduto' => array('foreignKey' => 'codigo_produto_naveg'),
			'UsuarioInclusao' => array('className' => 'Usuario', 'foreignKey' => 'codigo_usuario_inclusao'),
			'UsuarioAlteracao' => array('className' => 'Usuario', 'foreignKey' => 'codigo_usuario_alteracao'),
		)));
		if(!empty($filtro['codigo_configuracao_comissoes']))$conditions['codigo_configuracao_comissoes'] 	= $filtro['codigo_configuracao_comissoes'];										
		if(!empty($filtro['codigo_endereco_regiao']))$conditions['codigo_endereco_regiao'] 					= $filtro['codigo_endereco_regiao'];
		if(!empty($filtro['codigo_produto_naveg']))$conditions['codigo_produto_naveg'] 						= $filtro['codigo_produto_naveg'];

		$limit 	= 50;
		$order 	= array('EnderecoRegiao.descricao');		
		$fields = array(
			"CONVERT(VARCHAR,ConfiguracaoComissaoLog.data_inclusao,103) AS data_inclusao",
			"ConfiguracaoComissaoLog.data_alteracao",
		    "UsuarioInclusao.apelido",
		    "UsuarioAlteracao.apelido",
		    "ConfiguracaoComissaoLog.codigo_endereco_regiao",
		    "ConfiguracaoComissaoLog.codigo_produto_naveg",
		    "CASE	
		      WHEN  ConfiguracaoComissaoLog.acao_sistema = 0 THEN 'Inclusão'
		      WHEN  ConfiguracaoComissaoLog.acao_sistema = 1 THEN 'Altualização'
		      WHEN  ConfiguracaoComissaoLog.acao_sistema = 2 THEN 'Exclusão'
		      End AS acao",		    
		    "EnderecoRegiao.descricao",
		    "NProduto.descricao", 
		    "CASE	 
		     	WHEN ConfiguracaoComissaoLog.regiao_tipo_faturamento = 0 THEN 'Parcial'
		     	WHEN ConfiguracaoComissaoLog.regiao_tipo_faturamento = 1 THEN 'Total'
		    END  AS Faturamento",
		    "ConfiguracaoComissaoLog.percentual AS Comissao"
		);
		return compact('conditions','joins','limit','order','fields');
	}	
}

?>
