<div class = 'form-procurar'>
	<?= $this->element('/filtros/pcp_analitico') ?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>