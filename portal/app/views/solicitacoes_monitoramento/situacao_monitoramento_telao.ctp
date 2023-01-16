<style type="text/css">
    .page-title{ width: 962px; height: 44px; float: left; padding-top: 20px; }
    .page-title h3{ text-align: right; width: 500px; margin: 0 auto; }
    #logo{ width: 200px; height: 58px; float: right; }
    .lista{ clear: both; }
    #info-pagina{ font-size: 18px; }
</style>

<div id="logo">
    <img src="http://www.buonny.com.br/images/logo_situacao_monitoramento.jpg" border="0" />
</div>

<div class="lista">

    <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
    <?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
    <?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>           
    
    <p style="float:left;" id="info-pagina" style="visibility:hidden;"><strong>Pagina:</strong> <span id="info-pagina-dtr"></span></p>

    <div id="grafico" style="min-width: 400px; border:1px solid #FFFFFF; height: 680px; width: 1160px; margin: 0 auto 50px auto; display:none;"></div>

    <table class="table table-striped table-bordered tablesorter" id="info-sm" style="visibility:hidden; font-size:20px;">
        <thead>
            <tr>
                <th><center>Em viagem</center></th>
                <th><center>Paradas</center></th>
                <th><center>Iniciadas no dia</center></th>
                <th><center>Finalizadas no dia</center></th>
            </tr>
        </thead>
        <tbody>
                <tr>
                    <td id="viagem"></td>
                    <td id="paradas"></td>
                    <td id="iniciadas"></td>
                    <td id="finalizadas"></td>
                </tr>
        </tbody>      
    </table>

    <?php

        $events = "{
            load: function(){
                carregaDadosSituacaoMonitoramento(".$quantidadeEventos.", ".$intervalo.", '1')
            }
        }";             

        echo $this->Javascript->codeBlock($this->Highcharts->render(array(''), array( array( 'name' => "'Dentro SLA'", 'values' => array() ), array( 'name' => "'Fora SLA'", 'values' => array() ) ), array(
            'renderTo' => 'grafico',
            'chart' => array('type' => 'bar', 'events'=> $events ),
            'plotOptions' => array(
                'series' => array(
                    'align' => 'left',
                    'dataLabels' => array(
                        'enabled' => true, 
                        'color' => '#000000',
                        'style' => array(
                            'fontSize' => '22px',
                        )
                    ),
                    'pointWidth' => 40 
                )            
            ),
            'xAxis' => array(
                'labels' => array(
                    'rotation' => 0,                    
                    'align' => 'right',
                    'style' => array(
                        'fontSize' => '21px',
                        'color' => 'black',
                        'width' => '500',                        
                    )
                )
            ),
            'legend' => array(
                'layout' => 'horizontal',
                'align' => 'center',
                'verticalAlign' => 'top',
                'y' => 20
            ),
            'exporting' => array(
                'buttons' => array(
                    'exportButton' => array('enabled'=>'false'),
                    'printButton' => array('enabled'=>'false'),
                )
            )
        ))); 

    echo $this->Javascript->codeBlock('

        setInterval(

            function(){
                location.reload();
            },
            300000
        )
    ');

    ?>

</div>