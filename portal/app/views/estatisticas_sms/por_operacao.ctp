<div class='well'>
    <?php echo $this->BForm->create('EstatisticaSm', array('autocomplete' => 'off', 'url' => array('controller' => 'estatisticas_sms', 'action' => 'por_operacao'))) ?>
    	<div class="row-fluid inline">
            <?php echo $this->BForm->input('tipo', array('class' => 'input-medium', 'label' => false, 'options' => $tipos)) ?>
            <?php echo $this->BForm->input('data', array('class' => 'data input-small', 'placeholder' => 'Data', 'label' => false, 'type' => 'text')) ?>
            <?php echo $this->BForm->input('hora', array('type' => 'hidden', 'value' => $this->data['EstatisticaSm']['data'])) ?>
            <?php echo $this->BForm->input('tipo_grafico', array('type' => 'radio', 'options' => array('Média', 'Monitoradas'), 'default' => '0', 'legend' => false, 'label' => array('class' => 'radio inline'))) ?>
        </div>
        <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $this->BForm->end() ?>  
</div>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php if (!empty($eixo_x)): ?>
    <div id = "grafico_em_andamento_por_operador">
        <?php echo $this->Html->link('Marcar Todos', 'javascript:void(0)', array('onclick' => 'return hc_marcar_todos(chart_media)')) ?>
        <?php echo $this->Html->link('Demarcar Todos', 'javascript:void(0)', array('onclick' => 'return hc_desmarcar_todos(chart_media)')) ?>
        <div id="grafico1" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
    </div>
    <div id = "grafico_em_andamento">
        <?php echo $this->Html->link('Marcar Todos', 'javascript:void(0)', array('onclick' => 'return hc_marcar_todos(chart_em_andamento)')) ?>
        <?php echo $this->Html->link('Demarcar Todos', 'javascript:void(0)', array('onclick' => 'return hc_desmarcar_todos(chart_em_andamento)')) ?>
        <div id="grafico2" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
    </div>
    <table class="table table-striped table-bordered tablesorter">
        <thead>
            <tr>
                <th title="Nome da Operação"><?= $this->Html->link('Operação', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Operadores"><?= $this->Html->link('Operadores', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de SMs abertas"><?= $this->Html->link('SMs Abertas', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de SMs Monitoradas"><?= $this->Html->link('SMs Monitoradas', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Média de SMs em Viagem por Operador"><?= $this->Html->link('Média', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Ocorrências"><?= $this->Html->link('Ocorrências', 'javascript:void(0)') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $qtd_operacoes = 0; ?>
            <?php $qtd_sm_aberta = 0; ?>
            <?php $qtd_sm_em_andamento = 0; ?>
            <?php $qtd_ocorrencias = 0; ?>
            <?php if ($lista): ?>
                <?php foreach ($lista as $operacao): ?>
                    <tr>
                        <td><?= $operacao['descricao'] ?></td>
                        <td class="numeric"><?= $this->Html->link($operacao['operadores'], 'javascript:void(0)', array('onclick' => "estatistica_por_operador({$operacao['codigo_tipo_operacao']})")) ?></td>
                        <td class="numeric"><?= $this->Html->link($operacao['em_aberto'], 'javascript:void(0)', array('onclick' => "sm_consulta_geral_por_operacao_historico('{$operacao['codigo_tipo_operacao']}', '{$this->data['EstatisticaSm']['data']}', '0', '{$this->data['EstatisticaSm']['tipo']}')")) ?></td>
                        <td class="numeric"><?= $this->Html->link($operacao['em_andamento'], 'javascript:void(0)', array('onclick' => "sm_consulta_geral_por_operacao_historico('{$operacao['codigo_tipo_operacao']}', '{$this->data['EstatisticaSm']['data']}', '1', '{$this->data['EstatisticaSm']['tipo']}')")) ?></td>
                        <td class="numeric"><?= $this->Buonny->moeda(round($operacao['em_andamento_por_operador'],2), array('edit' => true)) ?></td>
                        <td class="numeric"><?= $operacao['ocorrencias'] ?></td>
                    </tr>
                    <?php $qtd_operacoes += 1; ?>
                    <?php $qtd_sm_aberta += $operacao['em_aberto']; ?>
                    <?php $qtd_sm_em_andamento += $operacao['em_andamento']; ?>
                    <?php $qtd_ocorrencias += $operacao['ocorrencias']; ?>
                <?php endforeach; ?>
            <?php else: ?>
                Sem dados para o dia selecionado
            <?php endif; ?>
        </tbody>
        <tfoot>
            <th class="numeric"><?= $qtd_operacoes ?></th>
            <th class="numeric"></th>
            <th class="numeric"><?= $qtd_sm_aberta ?></th>
            <th class="numeric"><?= $qtd_sm_em_andamento ?></th>
            <th class="numeric"></th>
            <th class="numeric"><?= $qtd_ocorrencias ?></th>
        </tfoot>
    </table>
    <?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
    <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
    <?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
    <?php 
        $script = $this->Highcharts->render($eixo_x, $series['em_andamento_por_operador'], array(
            'chart_variable' => 'chart_media',
            'renderTo' => 'grafico1',
            'chart' => array('type' => 'line'),
            'yAxis' => array('title' => 'Média SMs em Viagem por Operador'),
            'xAxis' => array('labels' => array('rotation' => -75, 'y' => 10), 'gridLineWidth' => 1),
        )); 
        $script .= $this->Highcharts->render($eixo_x, $series['em_andamento'], array(
            'chart_variable' => 'chart_em_andamento',
            'renderTo' => 'grafico2',
            'chart' => array('type' => 'line'),
            'yAxis' => array('title' => 'Monitoradas'),
            'xAxis' => array('labels' => array('rotation' => -75, 'y' => 10), 'gridLineWidth' => 1),
        )); 
        $script .= "jQuery(document).ready(function() {
            jQuery('table.table').tablesorter({sortList: [[0,0]]});
            jQuery('input[id^=EstatisticaSmTipoGrafico]').click(function() {
                selecionado = jQuery('input[id^=EstatisticaSmTipoGrafico]:checked').val();
                jQuery('div[id^=grafico_]').hide();
                if (selecionado == 0) {
                    jQuery('#grafico_em_andamento_por_operador').show();
                } else if (selecionado == 1) {
                    jQuery('#grafico_em_andamento').show();
                }
            });
            jQuery('input[id^=EstatisticaSmTipoGrafico]:checked').click();
        });";
        echo $this->Javascript->codeBlock($script);
    ?>
<?php endif; ?>