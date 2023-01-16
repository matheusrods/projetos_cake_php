<?php
class NotafisShell extends Shell {
	var $uses = array('Notafis','NotafisComplemento','MonitoraCron');

	function main() {
		echo "**********************************************\n";
		echo "$ \n";
		echo "$ Notafis\n";
		echo "$ \n";
		echo "**********************************************\n\n";
		echo "=> incluir_complemento: complementa as informações de filial para o faturamento";
		echo "\n\n";
	}	

	function incluir_complemento() {
		echo "Iniciando inclusao \n";
		$SQL = "INSERT INTO dbBuonny.dbo.notafis_complemento(empresa,seq,serie,numero,endereco_regiao,codigo_gestor,codigo_seguradora,codigo_corretora,data_inclusao) 
			SELECT DISTINCT
			    notafis.empresa,
			    notafis.seq,
			    notafis.serie,
			    notafis.numero,
			    cliente.codigo_endereco_regiao,
			    cliente.codigo_gestor,
			    cliente.codigo_seguradora,
			    cliente.codigo_corretora,
			    GETDATE()
			FROM Navegarq.dbo.notafis
			    INNER JOIN dbBuonny.vendas.cliente
			        ON cliente.codigo = CAST(notafis.cliente AS int)
			    LEFT JOIN dbBuonny.dbo.notafis_complemento
			        ON notafis.empresa = notafis_complemento.empresa
			        AND notafis.serie = notafis_complemento.serie
			        AND notafis.seq = notafis_complemento.seq
			        AND notafis.numero = notafis_complemento.numero
			WHERE 
			   notafis.empresa = '03'
			   AND notafis_complemento.empresa IS NULL
			   AND notafis.dtemissao >= '20131001 00:00:00'";
		$this->NotafisComplemento->query("Set ANSI_NULLS ON;Set ANSI_WARNINGS ON;");
		$this->NotafisComplemento->query($SQL);
		echo "Inclusao Efetuada \n";
		$this->MonitoraCron->execucao('cron_notafis_incluir_complemento');
	}
}
