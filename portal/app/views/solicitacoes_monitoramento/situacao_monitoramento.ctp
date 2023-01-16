<div class="well">
    <h5><?= $this->Html->link('Definir Filtros', 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <?php echo $this->BForm->create('SituacaoMonitoramento', array( 'url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'situacao_monitoramento'))) ?>
    <div id='filtros' style="display:none;">
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('quantidade_eventos', array('class' => 'input-small numeric', 'label' => 'Qtd.Eventos', 'value'=> 5)); ?>                
            <?php echo $this->BForm->input('intervalo', array('class' => 'input-medium numeric', 'label' => 'Segundos para Atualização', 'value'=> 10)); ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
        <?php echo $this->BForm->end();?>
    </div>
</div>
<?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
<?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<div class="lista">  
    <div id="nagevacao">
        <p style="float:left;" id="info-pagina" style="visibility:hidden;"><strong>Pagina:</strong> <span id="info-pagina-dtr"></span></p>
        <a id="abrirTelao" href="javascript:void(0)" style="display:block; float:right;" class="icon-fullscreen" title="Tela Cheia"></a>
    </div>
    <!-- Gráfico de eventos SLA-->
    <div id="grafico" style="min-width: 400px; height: 400px; width: 1160px; margin: 0 auto 50px; display:none; clear:both;"></div>
   
    <!-- Tabela de viagens sem operador-->  
    <div class="lista-sem-operador"> 
        <table class="table table-striped table-bordered tablesorter" id="sm-sem-operador" style="visibility:hidden; margin: 0 auto 50px;">
            <thead>
                <tr>
                    <th colspan="2">SM sem operador</th>
                </tr>
            </thead>
            <tr>
                <td>Quantidade</td>
                <td class="input-mini numeric" id="total-sem-operador"></td>
            </tr>            
        </table>
    </div>  
    <!-- Tabela de total de viagens (telão)-->  
    <table class="table table-striped table-bordered tablesorter" id="info-sm" style="visibility:hidden;">
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
    <!-- Tabela de Evento (SLA)-->
    <table class="table table-striped table-bordered tablesorter" id="info-eventos" style="visibility:hidden; margin-top:-100px">
        <thead>
            <tr>
                <th>Evento</th>
                <th class="numeric">Dentro SLA</th>
                <th class="numeric">Fora SLA</th>
                <th class="numeric">Total</th>
            </tr>
        </thead>
        <tbody id="dados-eventos"></tbody>
        <tfoot>
            <th>Total</th>
            <th class="numeric" id="total-dentro"></th>
            <th class="numeric" id="total-fora"></th>
            <th class="numeric" id="total-dentro-fora"></th>
        </tfoot>
    </table>
</div>
<?php echo $this->Javascript->codeBlock("
    function sem_operador() {
        var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('target', form_id);
        form.setAttribute('action', '/portal/operadores/viagens_sem_operador/' + Math.random());

        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
        document.body.appendChild(form);
        form.submit();
    }

    jQuery(document).ready(function(){
        $('.btn').click(function(event){
            event.preventDefault(); 
            carregaDadosSituacaoMonitoramento( $('#SituacaoMonitoramentoQuantidadeEventos').val(), $('#SituacaoMonitoramentoIntervalo').val());
            jQuery('div#filtros').slideToggle('slow');
        })

        $('#SituacaoMonitoramentoQuantidadeEventos').keyup(function(){                
            if ( isNaN( $('#SituacaoMonitoramentoQuantidadeEventos').val() ) || $('#SituacaoMonitoramentoQuantidadeEventos').val() == '' )
                $('#SituacaoMonitoramentoQuantidadeEventos').val(5);
        })

        $('#SituacaoMonitoramentoIntervalo').keyup(function(){
            if ( isNaN( $('#SituacaoMonitoramentoIntervalo').val() ) || $('#SituacaoMonitoramentoIntervalo').val() == '' )
                $('#SituacaoMonitoramentoIntervalo').val(10);
        })

        $('a#filtros').click(function(){
            jQuery('div#filtros').slideToggle('slow');
        });

        $('#abrirTelao').click(function(event){                
            telao_buonnysat($('#SituacaoMonitoramentoQuantidadeEventos').val(), $('#SituacaoMonitoramentoIntervalo').val());
        });

    });"
);?>
<?php  echo $this->Javascript->codeBlock(
    $this->Highcharts->render(array(''), 
        array(
            array(
                'name' => "'Dentro SLA'",
                'values' => array() ), 
            array(
                'name' => "'Fora SLA'",
                'values' => array() )
            ),
        array(
            'renderTo' => 'grafico',
            'chart' => array(
                'type' => 'bar', 
                'events'=> '{
                    load:function(){
                        carregaDadosSituacaoMonitoramento(
                            document.getElementById("SituacaoMonitoramentoQuantidadeEventos").value,
                            document.getElementById("SituacaoMonitoramentoIntervalo").value                            
                        );
                    }
                }'
            ),                  
            'plotOptions' => array(
                'series' => array(
                    'bar' => 'normal', 
                    'dataLabels' => array(
                        'enabled' => true,                        
                        'style' => array(
                            'fontSize' => '13px'
                        )
                    ),
                    'pointWidth' => 20                   
                )            
            ),       
        )
    )); 
?>