<div class = 'form-procurar'>
	<?= $this->element('filtros/logs_integracoes_consultar') ?>
</div>
<div id='logs_integracoes'></div>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>