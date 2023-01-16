<div id="EvolucaoGraph" style="width:98%;height:450px;float:left"></div>
<?php 



if(isset($EvolucaoGraph['eixo_x']))
{

  echo $this->Javascript->codeBlock($this->Highcharts->render($EvolucaoGraph['eixo_x'], $EvolucaoGraph['series'], array(
      'renderTo' => 'EvolucaoGraph',
      'chart' => array('type' => 'column'),
      'yAxis' => array(
          'title' => '',       
      ),
      'xAxis' => array('labels' => array('rotation' => -35, 'y' => 20), 'gridLineWidth' => 1),
      'title' => 'Evolução de Chamados',
      'plotOptions' => array(         
          'series' => array(
          )
      ),
      'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'),'printButton' => array('enabled'=> 'false'))),
  ))); 

}

?>