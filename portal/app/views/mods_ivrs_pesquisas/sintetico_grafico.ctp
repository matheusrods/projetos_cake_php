<?php if(!empty($dadosGrafico)):?>
<h4><?php echo (!empty($titulo) ? $titulo : '') ?></h4>
<div id="grafico" ></div>
<br/>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php echo $this->Javascript->codeBlock($this->Highcharts->render($dadosGrafico['eixo_x'], $dadosGrafico['series'], array(
    'renderTo' => 'grafico',
    'chart' => array('type' => $dadosGrafico['tipo']),
    'yAxis' => array('title' => 'Pontuação'),
    'xAxis' => array('labels' => array('rotation' => -10, 'y' => 20), 'gridLineWidth' => 1),
    'tooltip' => array('formatter' => 'this.y'),
    'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'),'printButton' => array('enabled'=> 'false'))),
))); ?>
<?php endif;?>    