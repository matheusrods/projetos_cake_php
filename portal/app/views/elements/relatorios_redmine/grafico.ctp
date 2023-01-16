<div id="grafico" style="width:48%;height:450px;float:left"></div>
<?php 



if(isset($graphitems['eixo_x']))
{

  echo $this->Javascript->codeBlock($this->Highcharts->render($graphitems['eixo_x'], $graphitems['series'], array(
      'renderTo' => 'grafico',
      'chart' => array('type' => 'column'),
      'yAxis' => array(
          'title' => '',       
      ),
    
      'xAxis' => array('labels' => array('rotation' => -35, 'y' => 20), 'gridLineWidth' => 1),
     // 'tooltip' => array(
          //'percentageStacking' => 'Realizado',
     //     'formatter1' => "'<b>'+this.series.name+' $descricaoVisualizacao '+(this.y/(this.point.stackTotal-this.y)*100).toFixed(2)+' %'+'</b><br><b>'+this.x+'</b><br>'+'<b>Total: </b>'+this.y+'<br>'",
     //     'formatter2' => "'<b>'+this.series.name+' $descricaoVisualizacao '+'</b><br><b>'+this.x+'</b><br>'+'<b>Total: </b>'+this.y+'<br>'"
     // ),
   
      'title' => 'Entregas no Período por Analista',
      'plotOptions' => array(         
          'series' => array(
              //'stacking' => 'normal'
          )
      ),
      'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'),'printButton' => array('enabled'=> 'false'))),
  ))); 

}

?>