<?php if(count($listagem)) : ?>
	<div id="grafico_cargo" class='gadget'>
		<img src="/portal/img/default.gif" style="padding: 15px;">Carregando...
	</div>
	
	<?php echo $this->Javascript->codeBlock($this->Highcharts->render($dadosGrafico['eixo_x'], $dadosGrafico['series'], array(
	    'renderTo' => 'grafico_cargo',
	    'chart' => array('type' => 'column'),
	    'yAxis' => array('title' => 'Maiores CIDs por Cargo'),
	    'xAxis' => array('labels' => array('rotation' => -50, 'y' => 20), 'gridLineWidth' => 2),
	    'tooltip' => array('formatter' => 'this.y'),
	    'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'),'printButton' => array('enabled'=> 'false'))),
	))); ?>
<?php endif; ?>