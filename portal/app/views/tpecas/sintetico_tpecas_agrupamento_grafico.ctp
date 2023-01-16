<?php if(empty($dadosGrafico)): ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php else: ?>
	<div id="grafico_tipo_peca" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
	<?php echo $this->Javascript->codeBlock($this->Highcharts->render($dadosGrafico['eixo_x'], $dadosGrafico['series'], array(
	    'title' => '',
	    'renderTo' => 'grafico_tipo_peca',
	    'chart' => array('type' => 'pie'),
		'legend' => array('labelFormatter' => 'function() { return this.name + " - " + this.y; }'),
		'plotOptions' => array('pie' => array('showInLegend'=>true)),
		'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'), 'printButton' => array('enabled'=> 'false')))
	))); ?>
<?php endif; ?>