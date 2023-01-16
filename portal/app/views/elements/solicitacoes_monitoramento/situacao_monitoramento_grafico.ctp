
<?php if (!empty($dados)): ?>

    <div id="grafico" style="min-width: 400px; height: 400px; margin: 0 auto 50px; display:none;"></div>

     <table class="table table-striped table-bordered tablesorter">
        <thead>
            <tr>
                <th>Em viagem</th>
                <th>Paradas</th>
                <th>Iniciadas no dia</th>
                <th>Finalizadas no dia</th>
            </tr>
        </thead>
        <tbody>
                <tr>
                    <td><?php echo $dadosSm[0][0]['em_viagem'] ?></td>                    
                    <td><?php echo $dadosSm[0][0]['paradas'] ?></td>                    
                    <td><?php echo $dadosSm[0][0]['iniciadas_no_dia'] ?></td>                    
                    <td><?php echo $dadosSm[0][0]['iniciadas_no_dia'] ?></td>                    
                </tr>
        </tbody>        
    </table>
    
    
    <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
    <?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
    <?php    

    $events = "{
        load:function() {
            setInterval(
                function() {   

                    var eventos       = [];
                    var dentro_do_sla = [];
                    var fora_do_sla   = [];

                    $.ajax({
                        type: 'POST',
                        dataType: 'JSON',
                        url: '/portal/solicitacoes_monitoramento/situacao_monitoramento/dataChart',

                        success : function(data){                

                            $(data).each( function( index, data ){
                                
                                eventos       = data['eixo_x']; 
                                dentro_do_sla = data['series'][0]['values'];
                                fora_do_sla   = data['series'][1]['values'];
                            })                            

                            chart.xAxis[0].setCategories( eventos );
                            chart.series[0].setData( dentro_do_sla );
                            chart.series[1].setData( fora_do_sla );
                        }
                    });

                }, ".$intervalo."
            );
        }
    }";

    echo $this->Javascript->codeBlock($this->Highcharts->render($dados['eixo_x'], $dados['series'], array(
        'renderTo' => 'grafico',
        'chart' => array('type' => 'bar')        
    ))); 

    ?>


<?php endif; ?>

