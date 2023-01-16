<?php if(!empty($series_pizza)):?>	
	<div class="row">
			<div id="grafico_pizza"></div>
	</div>
	<hr />
	
	<?php 
		echo $this->Javascript->codeBlock(
		    $this->Highcharts->render(array(), $series_pizza, array(
				'renderTo' => 'grafico_pizza',
		    	'title' => 'Percentual de Funcionários',
		        'chart' => array('type' => 'pie'),
			))
		); 
	?>
	<div class="right" style="margin-bottom: 10px; text-align: right;">
		<a href="/portal/absenteismo/exportar/<?php echo $codigo_grupo_economico; ?>" class="btn btn-success">Exportar em Excel</a>
	</div>
<?php else: ?>
	<div class="alert">Nenhum resultado encontrado.</div>
<?php endif; ?>