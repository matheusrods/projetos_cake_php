<?php
class RateioTranpagShell extends Shell {
    
	function run() {
        if( empty($this->args) ){
            for( $i=2;$i>0; $i-- ){
                $data_anterior  = date('Ym', strtotime("-{$i} month"));
                $mes = substr( $data_anterior, 4, 2 );
                $ano = substr( $data_anterior, 0, 4 );
                $this->atualiza($ano, $mes);
            }
        }
        $ano = empty($this->args[0]) ? date('Y') : $this->args[0];
        $mes = empty($this->args[1]) ? date('m') : $this->args[1];        
    	$this->atualiza($ano, $mes);
	}
	
	function init() {
		$ano_atual = date('Y');
		$mes_atual = date('m');

		$ano = 2011;
		$mes = 12;
		do {
			$ano = date('Y', strtotime("{$ano}-{$mes} +1 month"));
			$mes = date('m', strtotime("{$ano}-{$mes} +1 month"));
			$this->out("Atualizacao de rateio do mes {$ano}-{$mes}");
			$this->atualiza($ano, $mes);
		} while ($ano != $ano_atual || $mes != $mes_atual);
	}
	
	function daily() {
		$ano_anterior = date('Y', strtotime('-1 month'));
		$mes_anterior = date('m', strtotime('-1 month'));
		$this->atualiza($ano_anterior, $mes_anterior);

		$ano_atual = date('Y');
		$mes_atual = date('m');
		$this->atualiza($ano_atual, $mes_atual);
	}
	
    function atualiza($ano, $mes) {
        $tranpccrat = ClassRegistry::init('Tranpccrat');
        $tranpag = ClassRegistry::init('Tranpag');
        $tranpcc = ClassRegistry::init('Tranpcc');
        $planoct = ClassRegistry::init('Planoct');
        $ratct = ClassRegistry::init('Ratct');
        $this->MonitoraCron = ClassRegistry::init('MonitoraCron');
        echo "\n Periodo: {$mes}/{$ano}";
        $where = "WHERE YEAR(dtemiss) = {$ano} AND MONTH(dtemiss) = {$mes}";
        $sql_truncate = "DELETE FROM {$tranpccrat->databaseTable}.{$tranpccrat->tableSchema}.{$tranpccrat->useTable} {$where};";       
        $sql_insert = "
            INSERT {$tranpccrat->databaseTable}.{$tranpccrat->tableSchema}.{$tranpccrat->useTable}
                SELECT
                    tranpcc.numero
                    , tranpcc.serie
                    , tranpcc.tipodoc
                    , tranpcc.emitente
                    , tranpcc.ordem
                    , CASE WHEN ratct.codconta IS NOT NULL AND planoct.rateio = 'S' 
                        THEN ratct.ccusto 
                        ELSE tranpcc.ccusto
                    END AS ccusto
                    , tranpcc.numconta 
                    , CASE WHEN ratct.codconta IS NOT NULL AND planoct.rateio = 'S' 
                        THEN tranpcc.valor * ratct.rateio 
                        ELSE tranpcc.valor
                    END AS valor_total
                    , tranpag.grflux
                    , tranpag.sbflux
                    , tranpag.dtemiss
                    , (SELECT dtpagto FROM {$tranpag->databaseTable}.{$tranpag->tableSchema}.{$tranpag->useTable} AS p WHERE p.empresa = tranpag.empresa AND p.numero = tranpag.numero AND p.tipodoc = tranpag.tipodoc AND p.emitente = tranpag.emitente AND p.tiptit = tranpag.tiptit AND p.ordem = tranpag.ordem AND p.tipoemit = tranpag.tipoemit AND p.seq = '02')
                FROM {$tranpag->databaseTable}.{$tranpag->tableSchema}.{$tranpag->useTable} AS tranpag 
                    INNER JOIN {$tranpcc->databaseTable}.{$tranpcc->tableSchema}.{$tranpcc->useTable} AS tranpcc ON tranpcc.numero = tranpag.numero 
                        AND tranpcc.serie = tranpag.serie 
                        AND tranpcc.emitente = tranpag.emitente 
                        AND tranpcc.ordem = tranpag.ordem 
                        AND tranpcc.tipodoc = tranpag.tipodoc 
                    INNER JOIN {$planoct->databaseTable}.{$planoct->tableSchema}.{$planoct->useTable} AS planoct ON planoct.numred = tranpcc.numconta
                    LEFT JOIN {$ratct->databaseTable}.{$ratct->tableSchema}.{$ratct->useTable} AS ratct ON ratct.codconta = tranpcc.numconta
                {$where} AND seq='01' AND tranpag.empresa = '03' ;";
        $tranpccrat->query($sql_truncate);
        $tranpccrat->query($sql_insert);
        $this->MonitoraCron->execucao('cron_rateio_tranpag');
    }
    
}
?>