<div id="pizzaclientes" style="width:48%;height:450px;float:left"></div>
<?php 

if(isset($graphitemspizza['eixo_x']))
{


  echo $this->Javascript->codeBlock($this->Highcharts->render($graphitemspizza['eixo_x'], $graphitemspizza['series'], array(
      'renderTo' => 'pizzaclientes',
      'chart' => array('type' => 'pie'),
     // 'tooltip' => array(
          //'percentageStacking' => 'Realizado',
     //     'formatter1' => "'<b>'+this.series.name+' $descricaoVisualizacao '+(this.y/(this.point.stackTotal-this.y)*100).toFixed(2)+' %'+'</b><br><b>'+this.x+'</b><br>'+'<b>Total: </b>'+this.y+'<br>'",
     //     'formatter2' => "'<b>'+this.series.name+' $descricaoVisualizacao '+'</b><br><b>'+this.x+'</b><br>'+'<b>Total: </b>'+this.y+'<br>'"
     // ),
   
      'title' => 'Solicitantes',
      'plotOptions' => array(         
          'series' => array(
              //'stacking' => 'normal'
          )
      ),
      'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'),'printButton' => array('enabled'=> 'false'))),
  ))); 

}

?>

