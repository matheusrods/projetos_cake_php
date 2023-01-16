<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php // $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>

<div class = 'form-procurar'>
	<?= $this->element('/filtros/quantitativo_por_cid') ?>
</div>

<div class='lista' id='lista'></div>