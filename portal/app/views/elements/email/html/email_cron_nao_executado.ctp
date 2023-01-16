<style>
	body {font-family: Arial; color: #000;}
	table, th, td{ border: 1px solid black; }
	p.titulo {background-color: #3B90DF; color: #fff; text-align: center; font-size: 9pt; margin: 3px; font-weight: bold; padding: 3px;}
	p.corpo {font-size: 9pt; }
</style>
<p class="titulo">
	FALHA NA EXECU&Ccedil;&Atilde;O DO CRON - <?= $dados_erro['MonitoraCron']['descricao'];?>
</p>
<p class="corpo">
	Prezados Srs,<br/><br/><br/>
	<?php if(!empty($dados_erro['MonitoraCron']['data_ultima_execucao'])):?>
		Data da ultima execu&ccedil;&atilde;o do Cron &agrave;s <?= $dados_erro['MonitoraCron']['data_ultima_execucao'];?>
	<?php else:?>
		Cron nunca foi executado.
	<?php endif;?>	
</p>
<p class="corpo">
	Data da verifica&ccedil;&atilde;o <?= date('d/m/Y H:i:s')?>
</p>
<p class="corpo">
	<?= empty($dados_erro['MonitoraCron']['dia_processamento']) ? 'Execu&ccedil;&atilde;o diaria.' : 'Execu&ccedil;&atilde;o mensal.' ;?>
</p>
<br/>
<p class="corpo">Atenciosamente</p>
<p class="corpo"><b>Buonny Projetos e Servi&ccedil;os Ltda</b></p>