<?php
class TranrecShell extends Shell {
	var $uses = array("Tranrec", "TranrecPago",'MonitoraCron');

	function carregar_pagamentos() {
		$ano = empty($this->args[0]) ? date('Y') : $this->args[0];
		$mes = empty($this->args[1]) ? date('m') : $this->args[1];
        $periodo = array(
    		"{$ano}{$mes}01 00:00:00", 
    		date('Ymt 23:59:59', strtotime("{$ano}-{$mes}-01"))
    	);
		$analitico = $this->query_analitico($periodo);
        $tranrec_pago = "{$this->TranrecPago->databaseTable}.{$this->TranrecPago->tableSchema}.{$this->TranrecPago->useTable}";
        $query_delete = "DELETE FROM {$tranrec_pago} WHERE dtpagto BETWEEN '{$periodo[0]}' AND '{$periodo[1]}'";
        $query_carga = "INSERT INTO {$tranrec_pago} (codigo_cliente,codigo_corretora,codigo_produto,codigo_servico,valor_servico,quantidade,valor_unitario,valor_produto,vlmerc,empresa,numero,seqn,seq,serie,ordem,dtpagto,dtemiss) $analitico";
		try {
			$this->Tranrec->query('BEGIN TRANSACTION');
			if ($this->Tranrec->query($query_delete) === false) throw new Exception("Erro ao limpar dados do período");
			if ($this->Tranrec->query($query_carga) === false) throw new Exception("Erro ao carregar dados");
			$this->Tranrec->commit();
		} catch (Exception $ex) {
			$this->Tranrec->rollback();
		}
        $this->MonitoraCron->execucao('tranrec');
	}

	function query_analitico($periodo) {
		$this->Tranrec->bindModel(array('belongsTo' => array(
            '[Notaite] WITH(NOLOCK, INDEX(notaite_pk))' => array('className' => 'Notaite', 'foreignKey' => false, 'conditions' => array('Tranrec.empresa = Notaite.empresa', 'Tranrec.seqn = Notaite.seq', 'Tranrec.serie = Notaite.serie', 'Tranrec.numero = Notaite.nnotafis')),
            '[Notafis] WITH(NOLOCK)' => array('className' => 'Notafis', 'foreignKey' => false, 'conditions' => array('Tranrec.empresa = Notafis.empresa', 'Tranrec.seqn = Notafis.seq', 'Tranrec.serie = Notafis.serie', 'Tranrec.numero = Notafis.numero')),
            '[Integfat] WITH(NOLOCK)' => array('className' => 'Integfat', 'foreignKey' => false, 'conditions' => array('Notaite.empresa = Integfat.empresa', 'Notaite.npedido = Integfat.npedido', 'Notaite.item = Integfat.nitem')),
            '[ItemPedido] WITH(NOLOCK)' => array('className' => 'ItemPedido', 'foreignKey' => false, 'conditions' => array('ItemPedido.codigo_pedido = Integfat.seq')),
            '[DetalheItemPedidoManual] WITH(NOLOCK)' => array('className' => 'DetalheItemPedidoManual', 'foreignKey' => false, 'conditions' => 'DetalheItemPedidoManual.codigo_item_pedido = ItemPedido.codigo'),
            '[Cliente] WITH(NOLOCK)' => array('className' => 'Cliente', 'foreignKey' => false, 'conditions' => array('Cliente.codigo = Notaite.cliente')),
        )));
		$fields = array(
            'Cliente.codigo AS cliente_codigo',
            'Cliente.codigo_corretora AS codigo_corretora',
            'ItemPedido.codigo_produto AS codigo_produto',
            'DetalheItemPedidoManual.codigo_servico AS codigo_servico',
            'DetalheItemPedidoManual.valor AS valor_servico',
            'DetalheItemPedidoManual.quantidade AS quantidade',
            '(DetalheItemPedidoManual.valor/DetalheItemPedidoManual.quantidade) AS valor_unitario',
            'ItemPedido.valor_total AS valor_produto',
            'Notafis.vlmerc AS vlmerc',
            'Tranrec.empresa',
            'Tranrec.numero',
            'Tranrec.seqn',
            'Tranrec.seq',
            'Tranrec.serie',
            'Tranrec.ordem',
            'Tranrec.dtpagto',
            'Tranrec.dtemiss',
        );

		$conditions = array(
        	'Tranrec.dtpagto BETWEEN ? AND ?' => $periodo,
        	'Tranrec.seq' => '02',
        	"Tranrec.banco <> '1A'",
        	'Cliente.codigo_corretora IS NOT NULL',
        	'ItemPedido.codigo_produto IS NOT NULL',
        	'DetalheItemPedidoManual.codigo_servico IS NOT NULL',
        );
        return $this->Tranrec->find('sql', compact('conditions','fields'));
	}
}
?>
