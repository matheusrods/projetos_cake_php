<?php if(empty($dadosGrafico)): ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php else: ?>
	<div id="grafico_tipo_veiculo" style="min-width: 350px; height: 350px; margin: 0 auto 50px"></div>
	<?php echo $this->Javascript->codeBlock($this->Highcharts->render($dadosGrafico['eixo_x'], $dadosGrafico['series'], array(
	    'title' => '',
	    'renderTo' => 'grafico_tipo_veiculo',
	    'chart' => array('type' => 'pie'),
		'legend' => array('labelFormatter' => 'function() { return this.name + " - " + this.y; }'),
		'plotOptions' => array('pie' => array('showInLegend'=>true)),
		'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'true'), 'printButton' => array('enabled'=> 'true')))
	))); ?>
<?php endif; ?>