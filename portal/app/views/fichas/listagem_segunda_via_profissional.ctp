<?php
    echo $paginator->options(array('update' => 'div.lista'));
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>N. Consulta</th>
            <th>CPF</th>
            <th>Profissional</th>
            <th>Cliente</th>
            <th>Produto</th>
            <th>Data</th>
            <th>Placa</th>
            <?php /* <th>Carreta</th> */ ?>
            <th></th>
        </tr>
    </thead>
<?php foreach ($logs_faturamentos as $log_faturamento): ?>
    <tr>
        <td><?php echo $log_faturamento['LogFaturamentoTeleconsult']['numero_liberacao']; ?></td>
        <td><?php echo Comum::formatarDocumento($log_faturamento['Profissional']['codigo_documento']); ?></td>
        <td><?php echo $log_faturamento['Profissional']['nome']; ?></td>
        <td><?php echo $log_faturamento['Cliente']['razao_social']; ?></td>
        <td><?php echo $log_faturamento['Produto']['descricao']; ?></td>
        <td><?php echo $log_faturamento['LogFaturamentoTeleconsult']['data_inclusao']; ?></td>
        <td><?php echo Comum::formatarPlaca($log_faturamento[0]['veiculo_placa']); ?></td>
        <?php /*<td><?php echo $log_faturamento[0]['veiculo_carreta_placa']; ?></td>*/ ?>
        <td><?php echo $this->Html->link('', array('controller' => 'fichas', 'action' => 'visualizar_segunda_via_profissional', $log_faturamento['LogFaturamentoTeleconsult']['codigo']), array('escape' => false, 'class' => 'icon-eye-open', 'title' => 'Visualizar')); ?></td>
    </tr>
<?php endforeach; ?>
</table>
<?php /*
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
*/ ?>

<?php echo $this->element('generico/bootstrap_pagination'); ?>

<?php echo $this->Js->writeBuffer(); ?>