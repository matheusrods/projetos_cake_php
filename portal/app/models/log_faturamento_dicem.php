<?php

class LogFaturamentoDicem extends AppModel {

	var $name = 'LogFaturamentoDicem';
	var $tableSchema = 'dicem';
	var $databaseTable = 'dbDicem';
	var $useTable = 'log_faturamento';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	const SOMATORIA_VALOR_TOTAL = 1;
	const SOMATORIA_VALOR_UNITARIO = 2;

	public function converteParametroEmCondition($link) {
		$parametros = explode('|' , $link);
		$codigo_cliente = $parametros[1];
		$ano_mes = $parametros[2];
		$periodo = Comum::periodo($ano_mes);
		$condition = array(
			'FILTROS'		=> Date('d/m/Y', strtotime($periodo[0])).' até '.Date('d/m/Y', strtotime($periodo[1])),
			'DATA_INICIAL'   => $periodo[0],
			'DATA_FINAL'	 => $periodo[1],
			'CODIGO_CLIENTE' => $codigo_cliente
		);
		return $condition;
	}

	function bindServico() {
		$this->bindModel(array(
			'belongsTo' => array(
				'Servico' => array(
					'class' => 'Servico',
					'type' => 'INNER',
					'foreignKey' => 'codigo_servico'
				)
			)
		));
	}

	function unbindServico() {
		$this->unbindModel(array(
			'belongsTo' => array(
				'Servico'
			)
		));
	}

	public function geraEstatisticasAno($ano, $codigo_cliente = null) {
		$this->bindServico();
		$options = array(
			'fields' => array(
				'left(convert(varchar, '. $this->name . '.data_inclusao, 102),7) as ano_mes',
				'COUNT(Servico.descricao) AS numero_consultas',
				'Servico.descricao AS nome_servico'
			),
			'conditions' => array(
				$this->name.'.data_inclusao BETWEEN ? AND ?' => array($ano.'-01-01 00:00:00', $ano.'-12-31 23:59:59')
			),
			'group' => array(
				'left(convert(varchar, '. $this->name . '.data_inclusao, 102),7)',
				'Servico.descricao'
			),
		);
		if ($codigo_cliente != null)
			$options['conditions'][$this->name.'.codigo_cliente_pagador'] = $codigo_cliente;

		$count = $this->find('all', $options);
		$this->unbindServico();
		return $count;
	}

	function atualizaItensPedidos($filtros, $data_inclusao) {
        $filtros['data_inicial'] = AppModel::dateToDbDate($filtros['data_inicial'].' 00:00:00');
        $filtros['data_final'] = AppModel::dateToDbDate($filtros['data_final'].' 00:00:00');
        $this->Pedido =& ClassRegistry::init('Pedido');
        $this->ItemPedido =& ClassRegistry::init('ItemPedido');
        $query_atualizacao = "UPDATE {$this->databaseTable}.{$this->tableSchema}.{$this->useTable}
            SET
                codigo_item_pedido = itens_pedidos.codigo 
            FROM
                {$this->databaseTable}.{$this->tableSchema}.{$this->useTable} AS log_faturamento
            INNER JOIN {$this->Pedido->databaseTable}.{$this->Pedido->tableSchema}.{$this->Pedido->useTable} 
                ON pedidos.codigo_cliente_pagador = log_faturamento.codigo_cliente_pagador 
                 AND pedidos.data_inclusao BETWEEN '".$data_inclusao.".000"."' AND '".$data_inclusao.".999"."' 
                 AND pedidos.codigo_servico = '03085'
            INNER JOIN {$this->ItemPedido->databaseTable}.{$this->ItemPedido->tableSchema}.{$this->ItemPedido->useTable} 
                ON itens_pedidos.codigo_pedido = pedidos.codigo AND itens_pedidos.codigo_produto = 30 
            WHERE
                log_faturamento.data_inclusao BETWEEN '{$filtros['data_inicial']}' AND '{$filtros['data_final']}'";
        return ($this->query($query_atualizacao) !== false);
    }
}

class LogFaturamentoDicemTest extends LogFaturamentoDicem {
	var $name = 'LogFaturamentoDicemTest';
	var $useDbConfig = 'test';
	var $useTable = 'log_faturamento_dicem';
}

?>