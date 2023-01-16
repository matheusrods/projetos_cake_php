<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped table-bordered horizontal-scroll" style='width:2500px;max-width:none;'>
    <thead>
        <tr>
            <th class="input-xlarge"><?= $this->Paginator->sort('Pagador', 'pagador') ?></th>
            <th class="input-xlarge"><?= $this->Paginator->sort('Embarcador', 'cnpj_embarcador') ?></th>
            <th class="input-xlarge"><?= $this->Paginator->sort('Transportador', 'cnpj_transportador') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Qtd SM Monitorado', 'qtd_sm_monitorado_frota') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Serviço SM Monitorado', 'valor_sm_monitorado') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Teto Máximo SM Monitorado', 'valor_liq_sm_monitorado') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Qtd SM Telemonitorado', 'qtd_sm_telemonitorado_frota') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Serviço SM Telemonitorado', 'valor_sm_telemonitorado') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Teto Máximo SM Telemonitorado', 'valor_liq_sm_telemonitorado') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Qtd Placa Frota', 'qtd_placa_frota') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Serviço Placa Frota', 'valor_placa_frota') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Qtd Placa Avulso', 'qtd_placa_avulso') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Serviço Placa Avulso', 'valor_placa_avulso') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Qtd KM', 'qtd_km') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Serviço KM', 'valor_km') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Teto Máximo KM', 'valor_liq_km') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Qtd Dia', 'qtd_dia') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Serviço Dia', 'valor_dia') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Teto Máximo Dia', 'valor_liq_dia') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($faturamento_total as $faturamento): ?>
            <tr>
                <td><?= $faturamento['ViagemFaturamentoSubtotal']['pagador'].' - '.$faturamento['Pagador']['razao_social'] ?></td>
                <td><?= ($faturamento['Embarcador']['codigo'] ? $faturamento['Embarcador']['codigo'].' - '.$faturamento['Embarcador']['razao_social'] : '') ?></td>
                <td><?= $faturamento['Transportador']['codigo'].' - '.$faturamento['Transportador']['razao_social'] ?></td>
                <td class="numeric"><?= $this->Html->link($this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['qtd_sm_monitorado_frota'],array('places' => 0, 'nozero' => true)), 'javascript:void(0)', array('onclick' => 'listar_sms("'.$faturamento['ViagemFaturamentoSubtotal']['pagador'].'","'.$faturamento['Embarcador']['codigo'].'","'.$faturamento['Transportador']['codigo'].'","1")')) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['valor_sm_monitorado'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['teto_max_sm_monitorado'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Html->link($this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['qtd_sm_telemonitorado_frota'],array('places' => 0, 'nozero' => true)), 'javascript:void(0)', array('onclick' => 'listar_sms("'.$faturamento['ViagemFaturamentoSubtotal']['pagador'].'","'.$faturamento['Embarcador']['codigo'].'","'.$faturamento['Transportador']['codigo'].'","2")')) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['valor_sm_telemonitorado'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['teto_max_sm_telemonitorado'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Html->link($this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['qtd_placa_frota'],array('places' => 0, 'nozero' => true)), 'javascript:void(0)', array('onclick' => 'placas("'.$faturamento['ViagemFaturamentoSubtotal']['pagador'].'","1")')) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['valor_placa_frota'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Html->link($this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['qtd_placa_avulso'],array('places' => 0, 'nozero' => true)), 'javascript:void(0)', array('onclick' => 'placas("'.$faturamento['ViagemFaturamentoSubtotal']['pagador'].'","2")')) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['valor_placa_avulso'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['qtd_km'],array('places' => 0, 'nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['valor_km'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['teto_max_km'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['qtd_dia'],array('places' => 0, 'nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['valor_dia'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoSubtotal']['teto_max_dia'],array('nozero' => true)) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td><strong>Total: </strong><?php echo $this->Paginator->params['paging']['ViagemFaturamentoSubtotal']['count'] ?></td>
            <td colspan="2"></td>
            <td class="numeric"><?php echo $totais[0]['qtd_sm_monitorado_frota'] ?></td>
            <td colspan="2"></td>
            <td class="numeric"><?php echo $totais[0]['qtd_sm_telemonitorado_frota'] ?></td>
            <td colspan="4"></td>
            <td class="numeric"><?php echo $totais[0]['qtd_placa_avulso'] ?></td>
            <td colspan="1"></td>
            <td class="numeric"><?php echo $totais[0]['qtd_km'] ?></td>
            <td colspan="2"></td>
            <td class="numeric"><?php echo $totais[0]['qtd_dia'] ?></td>
            <td colspan="3"></td>
        </tr>
    </tfoot>
</table>
<div class='row-fluid'>
    <div class='numbers span6'>
        <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
      <?php echo $this->Paginator->numbers(); ?>
        <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>