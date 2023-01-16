<?php 
$eixo_x = array(
		array('name' => "'".'Grave'."'", 'values' => $grave,
			'color' => '#ff0000',
			'dataLabels' => array(
				'enabled' => true,
				'style' => array(
					'color' => 'black',
				),
				'formatter' => "function(){return (this.y);}"
			)
		),		
		array('name' => "'".'Médio'."'", 'values' => $medio,
			'color' => '#ffcc00',
			'dataLabels' => array('enabled' => true,
				'style' => array(
					'color' => 'black',
				),
				'formatter' => "function(){return (this.y);}")
		),
		array('name' => "'".'Informativo'."'",'values' => $informativo,
			'color' => '#009933',
			'dataLabels' => array('enabled' => true,
				'style' => array(
					'color' => 'black',
				),
				'formatter' => "function(){return (this.y);}"
			)
		),
		
		
	);
echo $this->Javascript->codeBlock($this->Highcharts->render($series, $eixo_x, array(
        'title' => '',
        'renderTo' => 'graph',        
        'chart' => array('type' => 'bar', 'spacingBottom' => 70),
        'yAxis' => array('title' => false),
        'xAxis' => array('labels' => array('rotation' => -15, 'x' => -20),'gridLineWidth' => 1),
        'plotOptions' => array(         
            'series' => array('stacking' => 'normal')
        ),
        'exporting' => array('buttons' => array('exportButton' => array('enabled' => 'false'), 'printButton' => array('enabled' => 'false')),),
        'legend' => array('align' => 'left', 'verticalAlign' => 'bottom', 'layout' => 'horizontal', 'floating' => 'true')
    )));

?>
<div class='well'>
    <?php if (isset($cliente)): ?>
        <strong>Cliente:</strong> <?= $cliente['Cliente']['razao_social'] ?>
    <?php endif ?>
</div>
<?php echo $this->element('rma/sintetico-tabela', array('series' => $series, 'agrupamento' => $agrupamento)) ?>