<?php if( $ultilizacao_servicos ): ?>
<br />
<div id="cliente" class='well'>
    <span class="pull-right">
    <?php echo $this->Html->link(
        '<i class="cus-page-white-excel"></i>', 
        array( 'controller' => $this->name, 'action' => 'listagem_utilizacao_servicos', 'export' ), 
        array('escape' => false, 'title' => 'Exportar para Excel')); ?>
    </span>
</div>
<?endif;?>
<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Código Pagador</th>
            <th>Pagador</th>
            <th>Codigo Utilizador</th>
            <th>Ultilizador</th>
            <th>Produto</th>
            <th>Serviço</th>
            <th>Cobrado</th>
            <th>SM Online</th>
            <th>Quantidade</th>
            <th>Valor</th>
            <th>Data Utillização</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($ultilizacao_servicos as $servicos):?>
        <tr>
            <td class="input-medium"><?php echo $servicos[0]['codigo_cliente_pagador'] ?></td>
            <td><?php echo $servicos[0]['razao_social_pagador'] ?></td>
            <td class="input-medium"><?php echo $servicos[0]['codigo_cliente_utilizador'] ?></td>
            <td><?php echo $servicos[0]['razao_social_utilizador'] ?></td>
            <td><?php echo $servicos[0]['produto_descricao'] ?></td>
            <td><?php echo (iconv('ISO-8859-1', 'UTF-8', $servicos[0]['servico_descricao']))  ?></td>
            <td class="input-mini"><?php echo !empty($servicos[0]['cobrado']) ? 'Sim' : 'Não'; ?></td>
            <td class="input-medium"><?php echo !empty($servicos[0]['online']) ? 'Sim' : 'Não'; ?></td>
            <td class="input-mini numeric"><?php echo ($servicos[0]['total'] == 0  ? null : $servicos[0]['total']) ?></td>
            <td class="input-mini numeric"><?php echo ($servicos[0]['precoSomado'] == 0  ? null : $servicos[0]['precoSomado']) ?></td>
            <td class="input-medium" ><?php echo AppModel::dbDateToDate( substr($servicos['LogFaturamentoTeleconsult']['data_inclusao'], 0, 10 ) )?></td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
    <tfoot>
        <tr>
            <td colspan = "11"><strong>Total</strong> <?php  echo $this->Paginator->params['paging']['LogFaturamentoTeleconsult']['count']; ?></td>
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