<?php $div_id = rand() ?>
<div id="<?= $div_id ?>" style="height:400px" class='gadget'></div>
<?php echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
        'renderTo' => $div_id,
        'chart' => array('type' => 'line', 'spacingBottom' => 70),
        'yAxis' => array('title' => false),
        'xAxis' => array('labels' => array('rotation' => -75, 'y' => 10), 'gridLineWidth' => 1),
        'exporting' => array('buttons' => array('exportButton' => array('enabled' => 'false'), 'printButton' => array('enabled' => 'false')),),
        'legend' => array('align' => 'center', 'verticalAlign' => 'bottom', 'layout' => 'horizontal'),
        'tooltip' => array('formatter' => "'<b>'+ this.series.name +'</b><br/>R$ '+moeda2(this.y)"),
    )));
?>