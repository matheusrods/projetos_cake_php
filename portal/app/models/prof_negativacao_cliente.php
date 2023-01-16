<?php
class ProfNegativacaoCliente extends AppModel {
	var $name          = 'ProfNegativacaoCliente';
	var $databaseTable = 'dbTeleconsult';
	var $tableSchema   = 'informacoes';
	var $useTable      = 'profissional_negativacao_cliente';
	var $primaryKey    = 'codigo';
	var $actsAs = array('Secure', 'Loggable' => array('foreign_key' => 'codigo_profissional_negativacao_cliente'));
	var $validate      = array(
		'codigo_profissional' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'message' => 'Informe o código profissional',
			),    
		),
		'observacao' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Favor preencha o campo Observação',
			)
		),
		'codigo_negativacao' => array(
			array(
				'rule' => 'notEmpty',
				'message' => 'Favor preencha o tipo de negativação',
			)
		)    
	);

	const PROFISSIONAL_DIVERGENTE_PELO_CLIENTE = 132;

	public function converteFiltroEmCondition( $filtros ) {
		$condition = array();
		if (isset($filtros['codigo_documento']) && !empty($filtros["codigo_documento"])) {
			$condition["Profissional.codigo_documento LIKE"] = "%".COMUM::soNumero($filtros["codigo_documento"])."%";
		}
		if (isset($filtros['codigo_cliente']) && !empty($filtros["codigo_cliente"])) {
			$condition["ProfNegativacaoCliente.codigo_cliente"] = $filtros["codigo_cliente"];
		}
		if (isset($filtros['codigo_negativacao']) && !empty($filtros["codigo_negativacao"])) {
			$condition["ProfNegativacaoCliente.codigo_negativacao"] = $filtros["codigo_negativacao"];
		}        
		if (isset($filtros['data_inicial']) && !empty($filtros["data_inicial"]) &&  isset($filtros['data_final']) && !empty($filtros["data_final"]) ) {
			array_push($condition, array( 'ProfNegativacaoCliente.data_inclusao BETWEEN ? AND ? '=> array(
				AppModel::dateToDbDate($filtros['data_inicial'].' 00:00'), AppModel::dateToDbDate($filtros['data_final'].' 23:59') 
				)
			)
			);
		} 
		return $condition; 
	}

	function verificaProfissional($codigo_profissional,$codigo_cliente){
		$retorno = $this->find('count',array('conditions' => array(
			'OR' => array(
				'codigo_cliente IS NULL',
				'codigo_cliente' => $codigo_cliente,
			),
			'codigo_profissional' => $codigo_profissional
		)));

		return ($retorno == 0);
	}

	function incluirLogFaturamento($log_faturamento){
		$Cliente = ClassRegistry::init('Cliente');
		$LogFaturamentoTeleconsult = ClassRegistry::init('LogFaturamentoTeleconsult');

		$cliente_pagador = $Cliente->carregarClientePagadorSemBloqueio($log_faturamento['codigo_cliente_transportador'],$log_faturamento['codigo_cliente_embarcador'],$log_faturamento['codigo_cliente'],$log_faturamento['codigo_produto']);

		if($cliente_pagador){
			$produto_ativo = $Cliente->clienteTemProdutoAtivo($cliente_pagador['Cliente']['codigo'],$log_faturamento['codigo_produto'],3);
			if($produto_ativo){
				$log_faturamento['codigo_cliente_pagador'] = $cliente_pagador['Cliente']['codigo'];
				$log_faturamento['codigo_tipo_operacao'] = self::PROFISSIONAL_DIVERGENTE_PELO_CLIENTE;
				$log_faturamento = array('LogFaturamentoTeleconsult' => $log_faturamento);

				return $LogFaturamentoTeleconsult->incluir($log_faturamento);
			}
		}
		return FALSE;
	}

	public function historicoOcorrenciaPorCliente( $codigo_profissional ){      
		$this->bindModel(array('belongsTo' => array(
			'Profissional' => array('foreignKey' => false, 'conditions'=>array('Profissional.codigo = ProfNegativacaoCliente.codigo_profissional')),  
			'TipoNegativacao' => array('foreignKey' => false, 'conditions'=>array('TipoNegativacao.codigo = ProfNegativacaoCliente.codigo_negativacao')),
			'Usuario' => array('foreignKey' => false, 'conditions' => array('Usuario.codigo =  ProfNegativacaoCliente.codigo_usuario_inclusao')),
		)));      
		$fields = array(
			"ProfNegativacaoCliente.data_inclusao", "TipoNegativacao.descricao","Profissional.nome", "Profissional.codigo_documento",
			"ProfNegativacaoCliente.observacao","ProfNegativacaoCliente.data_inclusao", "Usuario.apelido"
		);
		$conditions = array( 'Profissional.codigo'=> $codigo_profissional );
		return $this->find('all', compact('conditions', 'fields'));    
	}


}
?>