<div class = 'form-procurar'>
	<?= $this->element('/filtros/pcp_sintetico') ?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>