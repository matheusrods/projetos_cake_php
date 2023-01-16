<div class='well'>
    <?php echo $this->BForm->create('TEviaEstaViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'estatisticas_viagens', 'action' => 'geral'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('tipo', array('class' => 'input-medium', 'label' => false, 'options' => $tipos)) ?>
            <?php echo $this->BForm->input('data', array('class' => 'data input-small', 'placeholder' => 'Data', 'label' => false, 'type' => 'text')) ?>
        </div>
        <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $this->BForm->end() ?>
</div>
<?php $this->addScript($this->Buonny->link_js('estatisticas2')) ?>
<?php if (!empty($lista)): ?>
    <div id="grafico" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
    <table class="table table-striped table-bordered tablesorter">
        <thead>
            <tr>
                <th class="" title="Período"><?= $this->Html->link('Período', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Embarcadores"><?= $this->Html->link('Embarcadores', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Transportadores"><?= $this->Html->link('Transportadores', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Seguradoras"><?= $this->Html->link('Seguradoras', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Corretoras"><?= $this->Html->link('Corretoras', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Tecnologias"><?= $this->Html->link('Tecnologias', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Operadores"><?= $this->Html->link('Operadores', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de SMs abertas"><?= $this->Html->link('SMs Abertas', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de SMs Monitoradas"><?= $this->Html->link('SMs Monitoradas', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Média de SMs Monitoradas por Operador"><?= $this->Html->link('Média', 'javascript:void(0)') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lista as $key => $operacao): ?>
                <tr>
                    <td class=""><?= $key ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[0][$prefixo.'_emba_total'], 'javascript:void(0)', array('onclick' => 'estatistica_por_agrupamento("'.$key.'",1)')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[0][$prefixo.'_tran_total'], 'javascript:void(0)', array('onclick' => 'estatistica_por_agrupamento("'.$key.'",2)')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[0][$prefixo.'_segu_total'], 'javascript:void(0)', array('onclick' => 'estatistica_por_agrupamento("'.$key.'",3)')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[0][$prefixo.'_corr_total'], 'javascript:void(0)', array('onclick' => 'estatistica_por_agrupamento("'.$key.'",4)')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[0][$prefixo.'_tecn_total'], 'javascript:void(0)', array('onclick' => 'estatistica_por_agrupamento("'.$key.'",5)')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[0][$prefixo.'_oper_total'], 'javascript:void(0)', array('onclick' => 'estatistica_por_agrupamento("'.$key.'",6)')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[0][$prefixo.'_sm_em_aberto'], 'javascript:void(0)', array('onclick' => 'listar_sms("'.$key.'",0)')) ?></td>
                    <td class="numeric"><?= $this->Html->link($operacao[0][$prefixo.'_sm_em_andamento'], 'javascript:void(0)', array('onclick' => 'listar_sms("'.$key.'",1)')) ?></td>
                    <td class="numeric"><?= $this->Buonny->moeda(round($operacao[0][$prefixo.'_em_andamento_por_operador'],2), array('edit' => true,'places' => 1)) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
    <?php $this->addScript($this->Javascript->codeBlock("
    $.tablesorter.addParser({
        id: 'datetime',
        is: function(s) {
            return false; 
        },
        format: function(s,table) {
            s = s.replace(/\-/g,'/');
            s = s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/, '$3/$2/$1');
            return $.tablesorter.formatFloat(new Date(s).getTime());
        },
        type: 'numeric'
    });
    jQuery('table.table').tablesorter({
        sortList: [[0,1]], 
        dateFormat: 'dd/mm/yyyy',
        headers: {
            0: {sorter: 'datetime'}
        }
    })")) ?>
    
    <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
    <?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
    <?php echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
        'renderTo' => 'grafico',
        'chart' => array('type' => 'line'),
        'yAxis' => array('title' => ''),
        'xAxis' => array('labels' => array('rotation' => -75, 'y' => 10), 'gridLineWidth' => 1),
        'tooltip' => array('formatter' => 'this.y'),
    ))); ?>
<?php endif; ?>