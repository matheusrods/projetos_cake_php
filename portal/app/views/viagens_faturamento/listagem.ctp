<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped table-bordered horizontal-scroll" style='width:2000px;max-width:none;'>
    <thead>
        <tr>
            <th class="input-mini numeric"><?= $this->Paginator->sort('Pagador', 'pagador') ?></th>
            <th class="input-xxlarge"><?= $this->Paginator->sort('Nome Pagador', 'Cliente.razao_social') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Qtd SM Monitorado', 'qtd_sm_monitorado_frota') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor SM Monitorado', 'valor_sm_monitorado') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Líquido SM Monitorado', 'valor_liq_sm_monitorado') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Qtd SM Telemonitorado', 'qtd_sm_telemonitorado_frota') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor SM Telemonitorado', 'valor_sm_telemonitorado') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Líquido SM Telemonitorado', 'valor_liq_sm_telemonitorado') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Qtd Placa Frota', 'qtd_placa_frota') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Placa Frota', 'valor_placa_frota') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Qtd Placa Avulso', 'qtd_placa_avulso') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Placa Avulso', 'valor_placa_avulso') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Qtd KM', 'qtd_km') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor KM', 'valor_km') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Líquido KM', 'valor_liq_km') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Qtd Dia', 'qtd_dia') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Dia', 'valor_dia') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Valor Líquido Dia', 'valor_liq_dia') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($faturamento_total as $faturamento): ?>
            <tr>
                <td class="numeric"><?= $this->Html->link($faturamento['ViagemFaturamentoTotal']['pagador'], 'javascript:void(0)', array('onclick' => 'subtotal('.$faturamento['ViagemFaturamentoTotal']['pagador'].')')) ?></td>
                <td><?= $faturamento['Cliente']['razao_social'] ?></td>
                <td class="numeric"><?= $this->Html->link($this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['qtd_sm_monitorado_frota'],array('places' => 0, 'nozero' => true)), 'javascript:void(0)', array('onclick' => 'listar_sms("'.$faturamento['ViagemFaturamentoTotal']['pagador'].'","1")')) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['valor_sm_monitorado'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['valor_liq_sm_monitorado'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['qtd_sm_telemonitorado_frota'],array('places' => 0, 'nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['valor_sm_telemonitorado'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['valor_liq_sm_telemonitorado'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Html->link($this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['qtd_placa_frota'],array('places' => 0, 'nozero' => true)), 'javascript:void(0)', array('onclick' => 'placas("'.$faturamento['ViagemFaturamentoTotal']['pagador'].'","1")')) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['valor_placa_frota'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Html->link($this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['qtd_placa_avulso'],array('places' => 0, 'nozero' => true)), 'javascript:void(0)', array('onclick' => 'placas("'.$faturamento['ViagemFaturamentoTotal']['pagador'].'","2")')) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['valor_placa_avulso'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['qtd_km'],array('places' => 0, 'nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['valor_km'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['valor_liq_km'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['qtd_dia'],array('places' => 0, 'nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['valor_dia'],array('nozero' => true)) ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamentoTotal']['valor_liq_dia'],array('nozero' => true)) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td class="numeric"><strong>Total:</strong></td>
            <td><?php echo $this->Paginator->params['paging']['ViagemFaturamentoTotal']['count'] ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['qtd_sm_monitorado_frota'],array('places' => 0, 'nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['valor_sm_monitorado'],array('nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['valor_liq_sm_monitorado'],array('nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['qtd_sm_telemonitorado_frota'],array('places' => 0, 'nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['valor_sm_telemonitorado'],array('nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['valor_liq_sm_telemonitorado'],array('nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['qtd_placa_frota'],array('places' => 0, 'nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['valor_placa_frota'],array('nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['qtd_placa_avulso'],array('places' => 0, 'nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['valor_placa_avulso'],array('nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['qtd_km'],array('places' => 0, 'nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['valor_km'],array('nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['valor_liq_km'],array('nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['qtd_dia'],array('places' => 0, 'nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['valor_dia'],array('nozero' => true)) ?></td>
            <td class="numeric"><?php echo $this->Buonny->moeda($totais[0]['valor_liq_dia'],array('nozero' => true)) ?></td>
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