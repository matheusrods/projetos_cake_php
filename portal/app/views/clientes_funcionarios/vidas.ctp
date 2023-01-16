<div class = 'form-procurar'>
	<?= $this->element('/filtros/vidas') ?>
</div>
<div class='lista' id='lista'></div>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php echo $this->Javascript->codeblock("
	$(document).ready(function() { setup_mascaras() });
", false); ?>