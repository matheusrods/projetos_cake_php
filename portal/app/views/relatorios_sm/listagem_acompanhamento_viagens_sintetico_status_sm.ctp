<?php if(empty($relatorioStatusSM['series'])): ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php else: ?>
	<div id="grafico_status_sm" style="min-width: 350px; height: 350px; margin: 0 auto 50px"></div>
	<?php echo $this->Javascript->codeBlock($this->Highcharts->render(array(), $relatorioStatusSM['series'], array(
	    'title' => '',
	    'renderTo' => 'grafico_status_sm',
	    'chart' => array('type' => 'pie'),
		'legend' => array('labelFormatter' => 'function() { return this.name + " - " + this.y; }'),
		'plotOptions' => array('pie' => array('showInLegend'=>true)),
		'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'), 'printButton' => array('enabled'=> 'false')))
	))); ?>
	<?php echo $this->Javascript->codeBlock("$('#total-sm').html('{$total_sms}')") ?>
<?php endif; ?>