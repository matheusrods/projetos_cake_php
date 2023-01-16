<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped table-bordered horizontal-scroll">
    <thead>
        <tr>
            <th class="input-mini numeric"><?= $this->Paginator->sort('SM', 'sm') ?></th>
            <th class="input-xlarge"><?= $this->Paginator->sort('Pagador', 'codigo_cliente_pagador') ?></th>
            <th class="input-xlarge"><?= $this->Paginator->sort('Embarcador', 'cnpj_embarcador') ?></th>
            <th class="input-xlarge"><?= $this->Paginator->sort('Transportador', 'cnpj_transportador') ?></th>
            <th class="numeric"><?= $this->Paginator->sort('Distância Viagem', 'distancia_viagem') ?></th>
            <th><?= $this->Paginator->sort('Data Início', 'data_inicio') ?></th>
            <th><?= $this->Paginator->sort('Data Fim', 'data_fim') ?></th>
            <th><?= $this->Paginator->sort('Placa', 'placa') ?></th>
            <th><?= $this->Paginator->sort('Tecnologia', 'tecn_codigo') ?></th>
            <th><?= $this->Paginator->sort('Frota', 'frota') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($faturamento_total as $faturamento): ?>
            <tr>
                <td class="numeric"><?= $this->Buonny->codigo_sm($faturamento['ViagemFaturamento']['SM']) ?></td>
                <td><?= $faturamento['ViagemFaturamento']['codigo_cliente_pagador'].' - '.$faturamento['Pagador']['razao_social'] ?></td>
                <td><?= ($faturamento['Embarcador']['codigo'] ? $faturamento['Embarcador']['codigo'].' - '.$faturamento['Embarcador']['razao_social'] : '') ?></td>
                <td><?= $faturamento['Transportador']['codigo'].' - '.$faturamento['Transportador']['razao_social'] ?></td>
                <td class="numeric"><?= $this->Buonny->moeda($faturamento['ViagemFaturamento']['distancia_viagem'],array('nozero' => true)) ?></td>
                <td><?= $faturamento['ViagemFaturamento']['data_inicio'] ?></td>
                <td><?= $faturamento['ViagemFaturamento']['data_fim'] ?></td>
                <td><?= $faturamento['ViagemFaturamento']['placa'] ?></td>
                <td><?= $faturamento['ViagemFaturamento']['tecn_descricao'] ?></td>
                <td><?= ($faturamento['ViagemFaturamento']['frota']?'Sim':'Não') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td class="numeric"><strong>Total:</strong></td>
            <td><?php echo $this->Paginator->params['paging']['ViagemFaturamento']['count'] ?></td>
            <td colspan="8"></td>
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