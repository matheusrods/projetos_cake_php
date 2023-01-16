<div id="grafico" style="min-width: 800px;height:500px;margin: 30px auto 50px;"></div>
<?php echo $this->Javascript->codeBlock(
	$this->Highcharts->render($dadosGrafico['eixo_x'], $dadosGrafico['series'], 
		array(	
				'chart_variable' => 'grafico',
				'renderTo' => 'grafico',
				'chart' => array(
					'type' => 'line',
				),
				'series' =>array('visible' => 'false'),
				'yAxis' => array('title' => '',),
				'xAxis' => array(
					'labels' => array(
						'rotation' => 0,
						'y' => 20,
						'useHTML' =>  true,                
			            'style'=> array(
			                'fontSize'=> '12px',			              
						)
					),
					'gridLineWidth' => 1,
				),

				'legend' => array(			
					'y' => 0
				),
				'tooltip' => array(
					'formatter' => "'<b>'+this.series.name+'</b><br>'+'<b>Eventos: </b>'+this.y+'<br><b>Hora: </b>'+this.x+':00</b>'"
				),
				'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'),'printButton' => array('enabled'=> 'false'))),
				'plotOptions' => array(
					'series' => array(
						'dataLabels' => array(
							'enabled' => 'false',
							'color' => '#FFF'
						)
					)
				)
			)
		)
	); 
?>
<?php echo $this->Javascript->codeBlock("
	jQuery(document).ready(function(){
	    hc_habilitar_qtd_series(grafico,2);
	});");
?>