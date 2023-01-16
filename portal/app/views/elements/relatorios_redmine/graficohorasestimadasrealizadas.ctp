<div id="EstRealGraph" style="width:98%;height:450px;float:left"></div>
<?php 




if(isset($EstRealGraph['eixo_x']))
{

  echo $this->Javascript->codeBlock($this->Highcharts->render($EstRealGraph['eixo_x'], $EstRealGraph['series'], array(
      'renderTo' => 'EstRealGraph',
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
   
      'title' => 'Horas Estimadas Vs Realizadas',
      'plotOptions' => array(         
          'series' => array(
              //'stacking' => 'normal'
          )
      ),
      'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'),'printButton' => array('enabled'=> 'false'))),
  ))); 

}

?>