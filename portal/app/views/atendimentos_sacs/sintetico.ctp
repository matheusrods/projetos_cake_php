<div class = 'form-procurar'>
	<?= $this->element('filtros/atendimentos_sac_sintetico') ?>
</div>
<div class='lista'></div>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>