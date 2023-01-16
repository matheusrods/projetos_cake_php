
    <div id="nagevacao" style=" width:100%; height:40px;">        
        <a id="abrirTelao" href="javascript:void(0)" style="display:block; float:right;" class="icon-fullscreen" title="Tela Cheia"></a>
    </div>

    <div class='span6 window-gadget' style="margin-left:0">    
        <div class='alert alert-info'>Mês: <strong><?php echo $mesAtual; ?></strong></div>
        <table class="table table-striped table-bordered tablesorter">
            
            <tbody>
                    <tr>
                        <td><strong>Total SMs</strong></td>
                        <td class="numeric"><?php echo number_format($dadosMensal['qdt_sm'],0,",","."); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Valor Total SMs</strong></td>
                        <td class="numeric"><?php echo $this->Buonny->moeda($dadosMensal['valor_total']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Eventos Conforme</strong></td>
                        <td class="numeric"><?php echo number_format($dadosMensal['eventos_conforme'],0,",","."); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Eventos não Conforme</strong></td>
                        <td class="numeric"><?php echo number_format($dadosMensal['eventos_nao_conforme'],0,",","."); ?></td>
                    </tr>
            </tbody>        
        </table>
    </div>    

    <div class='span6 window-gadget'>    
        <div class='alert alert-info'>Ano: <strong><?php echo $anoAtual; ?></strong></div>
        <table class="table table-striped table-bordered tablesorter">
            
            <tbody>
                    <tr>
                        <td><strong>Total SMs</strong></td>
                        <td class="numeric"><?php echo number_format($dadosAnual['qdt_sm'],0,",","."); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Valor Total SMs</strong></td>
                        <td class="numeric"><?php echo $this->Buonny->moeda($dadosAnual['valor_total']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Eventos Conforme Últimos 60 Dias</strong></td>
                        <td class="numeric"><?php echo number_format($dadosAnual['eventos_conforme'],0,",","."); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Eventos não Conforme Últimos 60 Dias</strong></td>
                        <td class="numeric"><?php echo number_format($dadosAnual['eventos_nao_conforme'],0,",","."); ?></td>
                    </tr>
            </tbody>        
        </table>
    </div>

    <div class='span6 window-gadget' style="margin-left:0">    
        <div class='alert alert-info'>Gráfico Dia a Dia do Mês: <strong><?php echo $mesAtual; ?></strong></div>
            <div id="grafico-mensal"></div>
    </div>

    <div class='span6 window-gadget' style="">    
        <div class='alert alert-info'>Gráfico Mês a Mês do Ano: <strong><?php echo $anoAtual; ?></strong></div>
            <div id="grafico-anual"></div>
    </div>


<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php echo $this->Javascript->codeBlock("
        $(function(){
            var widget = $('div.span6.window-gadget');
            var largura = ($('div.container').width()/2)-30;
            var cont = 0;
            widget.each(function(){
                
                $(this).css({'width':largura,'float':'left'});
                if(cont%2){
                    $(this).css({'width':largura,'float':'right'});
                }
                cont++;
            })

            $('#abrirTelao').click(function(){

                telao_cockpit_buonnysat();
            })
        })
    "); ?>
<?php echo $this->Javascript->codeBlock($this->Highcharts->render($dadosGraficoMensal['eixo_x'], $dadosGraficoMensal['series'], array(
        'renderTo' => 'grafico-mensal',
        'chart' => array('type' => 'line', 'spacingBottom' => 70),
        'yAxis' => array('title' => false),
        'xAxis' => array('labels' => array('rotation' => -75, 'y' => 30), 'gridLineWidth' => 1),
        'exporting' => array('buttons' => array('exportButton' => array('enabled' => 'false'), 'printButton' => array('enabled' => 'false')),),
        'legend' => array('align' => 'left', 'verticalAlign' => 'bottom', 'layout' => 'horizontal', 'floating' => 'true'),
        'tooltip' => array('formatter' => "'<b>'+ this.series.name +'</b><br/>'+formata_numeros(this.y)"),
    ))); ?>
<?php echo $this->Javascript->codeBlock($this->Highcharts->render($dadosGraficoAnual['eixo_x'], $dadosGraficoAnual['series'], array(
        'renderTo' => 'grafico-anual',
        'chart' => array('type' => 'line', 'spacingBottom' => 70),
        'yAxis' => array('title' => false),
        'xAxis' => array('labels' => array('rotation' => -75, 'y' => 30), 'gridLineWidth' => 1),
        'exporting' => array('buttons' => array('exportButton' => array('enabled' => 'false'), 'printButton' => array('enabled' => 'false')),),
        'legend' => array('align' => 'left', 'verticalAlign' => 'bottom', 'layout' => 'horizontal', 'floating' => 'true'),
        'tooltip' => array('formatter' => "'<b>'+ this.series.name +'</b><br/>'+formata_numeros(this.y)"),
    ))); ?>
