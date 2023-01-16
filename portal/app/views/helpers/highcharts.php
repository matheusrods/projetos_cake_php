<?php
class HighchartsHelper extends AppHelper {
  var $helpers = array('Javascript');

  function render($xAxis, $series, $options = array()) {
    $x = '['.implode(',', $xAxis).']';
    $s = '';
	if ($options['chart']['type'] == 'pie') {
		foreach ($series as $serie) {
			// $s .= ',['.$serie['name'].','.$serie['values'].']';			
			if( isset($serie['color'])){
				$s .= ',{name:'.$serie['name'].', y: '.$serie['values'].', color: '.$serie['color'].'}';
			}elseif(isset($serie['option'])) {
				$s .= ',{name:'.$serie['name'].', y: '.$serie['values'].', option: '.$serie['option'].'}';
			} else{
				$s .= ',{name:'.$serie['name'].', y: '.$serie['values'].'}';				
			}
		}		
		// $s = '[{data:['.substr($s,1).']}]';		
		$s = '[{data:['.substr($s,1).']}]';
	} else {
	    foreach ($series as $serie) {
	      	//$s .= ',{name: '.$serie['name'].(isset($serie['color']) ? ', color:' ."'".$serie['color']."'" : '').', data: ['.implode(',', $serie['values']).']}';
	      	$s .= ',{'.
	      		(isset($serie['type']) ? 'type: '."'".$serie['type']."'," : '').
	      		'name: '.$serie['name'].
	      		(isset($serie['color']) ? ', color:' ."'".$serie['color']."'" : '').
	      		', visible:'.(isset($options['series']['visible']) ? 'false' : 'true').
	      		', data: ['.implode(',', $serie['values']).']'.
	      		(isset($serie['stack']) ? ', stack:'."'".$serie['stack']."'": '').
	      		', marker: {'.
	      			'enabled:'.(isset($serie['marker']['enabled']) ? $serie['marker']['enabled'] : 'true').
	      		'}'.
	      		', dataLabels: {'.
	      			'enabled:'.(isset($serie['dataLabels']['enabled']) && $serie['dataLabels']['enabled'] ? 'true,' : 'false,').
	      			'style: {'.
	      				'color:'.(isset($serie['dataLabels']['style']['color']) ? "'".$serie['dataLabels']['style']['color']."'," : "'#606060',").
	      				'fontSize:'.(isset($serie['dataLabels']['style']['fontSize']) ? "'".$serie['dataLabels']['style']['fontSize']."'" : "'11px'").
	      			'},'.
	      			'formatter:'.(isset($serie['dataLabels']['formatter']) ? $serie['dataLabels']['formatter'] : 'false').
	      		'}'.
	      	'}';
	    }
	    $s = '['.substr($s,1).']';
	  }

	
    if (isset($options['tooltip']['formatter'])){
        $tooltip_formatter = $options['tooltip']['formatter'];
    }else{
        $tooltip_formatter = ($options['chart']['type'] == 'pie' ? 'this.point.name': 'this.series.name')." +': '+ this.y;";
    }
  
    
    $retorno = "var " . (isset($options['chart_variable']) ? $options['chart_variable'] : "chart") . ";
			$(document).ready(function() {
				". (isset($options['chart_variable']) ? $options['chart_variable'] : "chart"). " = new Highcharts.Chart({
					credits: {
						enabled: false
					},
					chart: {
						animation: ".(isset($options['chart']['animation'])? $options['chart']['animation'] : 'true').",
						renderTo: '".(isset($options['renderTo'])? $options['renderTo'] : 'grafico')."',
						type: '".(isset($options['chart']['type'])? $options['chart']['type'] : 'column')."',
						spacingTop: ".(isset($options['chart']['spacingTop'])? $options['chart']['spacingTop'] : '10').",
						spacingBottom: ".(isset($options['chart']['spacingBottom'])? $options['chart']['spacingBottom'] : '15').",
						spacingLeft: ".(isset($options['chart']['spacingLeft'])? $options['chart']['spacingLeft'] : '10').",
						spacingRight: ".(isset($options['chart']['spacingRight'])? $options['chart']['spacingRight'] : '10').",
						events: ".(isset($options['chart']['events'])? $options['chart']['events'] : 'null').",
						zoomType: '".(isset($options['chart']['zoomType'])? $options['chart']['zoomType'] : '')."'
					},
					title: {
						text: '".(isset($options['title'])? $options['title'] : '')."'
					},
					subtitle: {
						text: '".(isset($options['subtitle'])? $options['subtitle'] : '')."'
					},
					xAxis: {
					    gridLineWidth: ".(isset($options['xAxis']['gridLineWidth'])? $options['xAxis']['gridLineWidth'] : '0').",
						categories: ".$x.",
						labels: {
						    rotation: ".(isset($options['xAxis']['labels']['rotation'])? $options['xAxis']['labels']['rotation'] : '0').",
						    x: ".(isset($options['xAxis']['labels']['x'])? $options['xAxis']['labels']['x'] : '0').",
						    y: ".(isset($options['xAxis']['labels']['y'])? $options['xAxis']['labels']['y'] : '0').",
						    align: ".(isset($options['xAxis']['labels']['align'])? "'".$options['xAxis']['labels']['align']."'" : "'right'").",
						    style: {
						    	fontSize: ".(isset($options['xAxis']['labels']['style']['fontSize'])? "'".$options['xAxis']['labels']['style']['fontSize']."'" : "'11px'").",
						    	fontFamily: ".(isset($options['xAxis']['labels']['style']['fontFamily'])? "'".$options['xAxis']['labels']['style']['fontFamily']."'" : "'Verdana, sans-serif'").",
								color: ".(isset($options['xAxis']['labels']['style']['color'])? "'".$options['xAxis']['labels']['style']['color']."'" : "'#666666'").",
								width: ".(isset($options['xAxis']['labels']['style']['width'])? "'".$options['xAxis']['labels']['style']['width']."'" : "'500'")."
						    }
						}
					},

					yAxis: {
						min: 0,
						title: {
							text: '".(isset($options['yAxis']['title'])? $options['yAxis']['title'] : 'Valor')."'
						},
						labels: {
						    rotation: ".(isset($options['yAxis']['labels']['rotation'])? $options['yAxis']['labels']['rotation'] : '0').",
						    x: ".(isset($options['yAxis']['labels']['x'])? $options['yAxis']['labels']['x'] : '0').",
						    y: ".(isset($options['yAxis']['labels']['y'])? $options['yAxis']['labels']['y'] : '0')."
						},
						stackLabels: {
							enabled: ".(isset($options['yAxis']['stackLabels']['enabled'])? $options['yAxis']['stackLabels']['enabled'] : 'false').",							
			                formatter: function () {
			                    return ".(isset($options['yAxis']['stackLabels']['formatter']['function'])? $options['yAxis']['stackLabels']['formatter']['function'] : 'this.total')."
			                }
  
						},
					},
					legend: {
						enabled: ".(isset($options['legend']['enabled'])? $options['legend']['enabled'] : 'true').",
						layout: '".(isset($options['legend']['layout'])? $options['legend']['layout'] : 'vertical')."',
						backgroundColor: '#FFFFFF',
						align: '".(isset($options['legend']['align'])? $options['legend']['align'] : 'right')."',
						verticalAlign: '".(isset($options['legend']['verticalAlign'])? $options['legend']['verticalAlign'] : 'top')."',
						x: ".(isset($options['legend']['x'])? $options['legend']['x'] : '10').",
						y: ".(isset($options['legend']['y'])? $options['legend']['y'] : '70').",
						floating: ".(isset($options['legend']['floating'])? $options['legend']['floating'] : 'false').",
						shadow: true,
						labelFormatter: ".(isset($options['legend']['labelFormatter'])? $options['legend']['labelFormatter'] : 'function() { return this.name }')."
					},
					tooltip: {
						".(isset($options['tooltip']['percentageStacking'])? 
							"formatter: function() {
								return (this.series.name == '{$options['tooltip']['percentageStacking']}') ? {$options['tooltip']['formatter1']} : {$options['tooltip']['formatter2']}
							}" : 
							"formatter: function() {
								return $tooltip_formatter
							}"
						)."
					},
					plotOptions: {
						column: {
							pointPadding: 0.2,
							borderWidth: 0
						},
						" . (isset($options['chart']['type']) && $options['chart']['type'] == 'pie' ? '' : "
						series: {
						    stacking:".(isset($options['plotOptions']['series']['stacking'])? "'".$options['plotOptions']['series']['stacking']."'," : 'null,')."
						    enableMouseTracking:".(isset($options['plotOptions']['series']['enableMouseTracking'])? $options['plotOptions']['series']['enableMouseTracking']."," : 'true,')."
							dataLabels:{
						    	enabled: ".(isset($options['plotOptions']['series']['dataLabels']['enabled'])? $options['plotOptions']['series']['dataLabels']['enabled']."," : 'false,')."
						    	formatter: ".(isset($options['plotOptions']['series']['dataLabels']['format'])? $options['plotOptions']['series']['dataLabels']['format']."," : 'false,')."
								borderRadius:".(isset($options['plotOptions']['series']['dataLabels']['borderRadius'])? $options['plotOptions']['series']['dataLabels']['borderRadius']."," : '0,')."
								padding:".(isset($options['plotOptions']['series']['dataLabels']['padding'])? $options['plotOptions']['series']['dataLabels']['padding']."," : '2,')."
								backgroundColor:".(isset($options['plotOptions']['series']['dataLabels']['backgroundColor'])? "'".$options['plotOptions']['series']['dataLabels']['backgroundColor']."'," : 'null,')."
								borderWidth:".(isset($options['plotOptions']['series']['dataLabels']['borderWidth'])? "'".$options['plotOptions']['series']['dataLabels']['borderWidth']."'," : '0,')."
								borderColor:".(isset($options['plotOptions']['series']['dataLabels']['borderColor'])? "'".$options['plotOptions']['series']['dataLabels']['borderColor']."'," : 'null,')."
								color:".(isset($options['plotOptions']['series']['dataLabels']['color'])? "'".$options['plotOptions']['series']['dataLabels']['color']."'," : 'null,')."
								y:".(isset($options['plotOptions']['series']['dataLabels']['y'])? $options['plotOptions']['series']['dataLabels']['y']."," : '0,')."
								style: {
									fontSize:".(isset($options['plotOptions']['series']['dataLabels']['style']['fontSize'])? " '".$options['plotOptions']['series']['dataLabels']['style']['fontSize']."'," : "'11px',")."
									color:".(isset($options['plotOptions']['series']['dataLabels']['style']['color'])? " '".$options['plotOptions']['series']['dataLabels']['style']['color']."'" : "'#606060'")."
								}
						    }
						}," ) .
						"pie: {
						    showInLegend: ".(isset($options['plotOptions']['pie']['showInLegend']) ? $options['plotOptions']['pie']['showInLegend'] : 'false').",
						    animation: ".(isset($options['plotOptions']['pie']['animation'])? $options['plotOptions']['pie']['animation'] : 'true').",
						    dataLabels: {
						    	enable: true,
						    	formatter: function() {
						    		return '<b>'+ this.point.name +'</b><br /> '+ this.percentage.toFixed(2) +' %';
						    	}
						    }
						}
					},
					exporting: {
						buttons: {
							exportButton: {
								enabled:".(isset($options['exporting']['buttons']['exportButton']['enabled'])? $options['exporting']['buttons']['exportButton']['enabled'] : 'true')."
							},
							printButton: {
								enabled:".(isset($options['exporting']['buttons']['printButton']['enabled'])? $options['exporting']['buttons']['printButton']['enabled'] : 'true')."
							}
						}
					},
				  series: ".$s."
				});
			});";

	// print $retorno; exit;
	return $retorno;

  }
}
?>