<div id="grafico" style="width:100%;height:450px;float:left"></div>
<?php echo $this->Javascript->codeBlock($this->Highcharts->render($dadosGrafico['eixo_x'], $dadosGrafico['series'], array(
    'renderTo' => 'grafico',
    'chart' => array('type' => 'column'),
    'yAxis' => array(
        'title' => '',       
    ),
  
    'xAxis' => array('labels' => array('rotation' => -10, 'y' => 20), 'gridLineWidth' => 1),
   // 'tooltip' => array(
        //'percentageStacking' => 'Realizado',
   //     'formatter1' => "'<b>'+this.series.name+' $descricaoVisualizacao '+(this.y/(this.point.stackTotal-this.y)*100).toFixed(2)+' %'+'</b><br><b>'+this.x+'</b><br>'+'<b>Total: </b>'+this.y+'<br>'",
   //     'formatter2' => "'<b>'+this.series.name+' $descricaoVisualizacao '+'</b><br><b>'+this.x+'</b><br>'+'<b>Total: </b>'+this.y+'<br>'"
   // ),
 
    'title' => $descricaoVisualizacao,
    'plotOptions' => array(         
        'series' => array(
            //'stacking' => 'normal'
        )
    ),
    'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'),'printButton' => array('enabled'=> 'false'))),
))); 
?>