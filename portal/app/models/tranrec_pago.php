<?php
class TranrecPago extends AppModel {
    var $name = 'TranrecPago';
    var $tableSchema = 'vendas';
    var $databaseTable = 'dbBuonny';
    var $useTable = 'tranrec_pago';
    var $primaryKey = 'codigo';

    function analiticoComissoesPorCorretora($findType, $options) {
    	$options['conditions'] = $this->converteConditionsTranrec($options['conditions']);
    	$options['conditions'][] = 'ConfiguracaoComissaoCorre.percentual_comissao >= 0';
    	$this->bindModel(array('belongsTo' => array(
    		'Cliente' => array('foreignKey' => 'codigo_cliente'),
    		'Corretora' => array('foreignKey' => false, 'conditions' => 'Cliente.codigo_corretora = Corretora.codigo'),
    		'Produto' => array('foreignKey' => 'codigo_produto'),
    		'Servico' => array('foreignKey' => 'codigo_servico'),
    		'ConfiguracaoComissaoCorre' => array('foreignKey' => false, 'conditions' => array(
    			'ConfiguracaoComissaoCorre.codigo_corretora = TranrecPago.codigo_corretora',
                'ConfiguracaoComissaoCorre.codigo_produto = TranrecPago.codigo_produto',
                'ConfiguracaoComissaoCorre.codigo_servico = TranrecPago.codigo_servico',
                'OR' => array(
                    'ConfiguracaoComissaoCorre.verificar_preco_unitario = 0',
                    'TranrecPago.valor_unitario BETWEEN ConfiguracaoComissaoCorre.preco_de AND ConfiguracaoComissaoCorre.preco_ate'
                ),
    		)),
    	)));
    	$options['fields'] = array(
            'TranrecPago.codigo_cliente AS cliente_codigo',
            'Cliente.razao_social AS cliente_nome',
            'TranrecPago.codigo_corretora AS codigo_corretora',
            'TranrecPago.codigo_produto AS codigo_produto',
            'Produto.descricao AS produto_nome',
            'Servico.descricao AS servico_nome',
            'TranrecPago.codigo_servico AS codigo_servico',
            'TranrecPago.valor_servico AS valor_servico',
            'TranrecPago.valor_produto AS valor_produto',
            'TranrecPago.quantidade AS quantidade',
            'TranrecPago.valor_unitario AS valor_unitario',
            'Corretora.nome AS corretora_nome',
            'TranrecPago.vlmerc AS vlmerc',
            'ConfiguracaoComissaoCorre.verificar_preco_unitario AS verificar_preco_unitario',
            'ConfiguracaoComissaoCorre.preco_de AS preco_de',
            'ConfiguracaoComissaoCorre.preco_ate AS preco_ate',
            'ConfiguracaoComissaoCorre.percentual_impostos AS percentual_impostos',
            'ConfiguracaoComissaoCorre.percentual_comissao AS percentual_comissao',
            '(TranrecPago.valor_servico * ConfiguracaoComissaoCorre.percentual_impostos / 100) AS valor_impostos',
            '(TranrecPago.valor_servico - (TranrecPago.valor_servico * ConfiguracaoComissaoCorre.percentual_impostos / 100)) AS valor_servico_liquido',
            '(CONVERT(DECIMAL(14,2), TranrecPago.valor_servico) - (CONVERT(DECIMAL(14,2), TranrecPago.valor_servico) * (CONVERT(DECIMAL(14,2), ConfiguracaoComissaoCorre.percentual_impostos) / 100)) ) * CONVERT(DECIMAL(14,2), ConfiguracaoComissaoCorre.percentual_comissao) / 100 AS valor_comissao',
        );
    	return $this->find($findType, $options);
    }

    function converteConditionsTranrec($conditions) {
    	if (isset($conditions['Tranrec.empresa'])) {
    		$conditions['TranrecPago.empresa'] = $conditions['Tranrec.empresa'];
    		unset($conditions['Tranrec.empresa']);
    	}
    	if (isset($conditions['Tranrec.dtpagto BETWEEN ? AND ?'])) {
    		$conditions['TranrecPago.dtpagto BETWEEN ? AND ?'] = $conditions['Tranrec.dtpagto BETWEEN ? AND ?'];
    		unset($conditions['Tranrec.dtpagto BETWEEN ? AND ?']);
    	}
    	if (isset($conditions['Tranrec.dtemiss >='])) {
    		$conditions['TranrecPago.dtemiss >='] = $conditions['Tranrec.dtemiss >='];
    		unset($conditions['Tranrec.dtemiss >=']);
    	}
    	if (isset($conditions['Tranrec.dtemiss <='])) {
    		$conditions['TranrecPago.dtemiss <='] = $conditions['Tranrec.dtemiss <='];
    		unset($conditions['Tranrec.dtemiss <=']);
    	}
    	if (isset($conditions["NOT EXISTS(SELECT TOP 1 pagamento.dtpagto FROM Navegarq.dbo.tranrec AS pagamento WHERE pagamento.numero = Tranrec.numero AND pagamento.seq = '02')"])) {
    		$conditions["NOT EXISTS(SELECT TOP 1 pagamento.dtpagto FROM Navegarq.dbo.tranrec AS pagamento WHERE pagamento.numero = TranrecPago.numero AND pagamento.seq = '02')"] = $conditions["NOT EXISTS(SELECT TOP 1 pagamento.dtpagto FROM Navegarq.dbo.tranrec AS pagamento WHERE pagamento.numero = Tranrec.numero AND pagamento.seq = '02')"];
    		unset($conditions["NOT EXISTS(SELECT TOP 1 pagamento.dtpagto FROM Navegarq.dbo.tranrec AS pagamento WHERE pagamento.numero = Tranrec.numero AND pagamento.seq = '02')"]);
    	}
    	if (isset($conditions["EXISTS(SELECT TOP 1 pagamento.dtpagto FROM Navegarq.dbo.tranrec AS pagamento WHERE pagamento.numero = Tranrec.numero AND pagamento.seq = '02')"])) {
    		$conditions["EXISTS(SELECT TOP 1 pagamento.dtpagto FROM Navegarq.dbo.tranrec AS pagamento WHERE pagamento.numero = TranrecPago.numero AND pagamento.seq = '02')"] = $conditions["EXISTS(SELECT TOP 1 pagamento.dtpagto FROM Navegarq.dbo.tranrec AS pagamento WHERE pagamento.numero = Tranrec.numero AND pagamento.seq = '02')"];
    		unset($conditions["EXISTS(SELECT TOP 1 pagamento.dtpagto FROM Navegarq.dbo.tranrec AS pagamento WHERE pagamento.numero = Tranrec.numero AND pagamento.seq = '02')"]);
    	}
        return $conditions;
    }
}
?>