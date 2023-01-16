<h4>Motivos de Atraso</h4>
<div id='grafico-nivel-servico-atraso'></div>
<?php echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
        'renderTo' => 'grafico-nivel-servico-atraso',
        'chart' => array('type' => 'column', 'spacingBottom' => 70),
        'yAxis' => array('title' => false),
        'xAxis' => array('labels' => array('rotation' => -75, 'y' => 30), 'gridLineWidth' => 1),
        'exporting' => array('buttons' => array('exportButton' => array('enabled' => 'false'), 'printButton' => array('enabled' => 'false')),),
        'tooltip' => array('formatter' => "'<b>'+ this.series.name +'</b><br/>'+this.y"),
    )));
?>