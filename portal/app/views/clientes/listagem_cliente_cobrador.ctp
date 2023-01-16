<?php 
    echo $paginator->options(array('update' => 'div.lista_cliente_cobrador')); 
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('Código', 'codigo') ?></th>
            <th><?= $this->Paginator->sort('CNPJ', 'codigo_documento') ?></th>
            <th><?= $this->Paginator->sort('Razão Social', 'razao_social') ?></th>
            <th><?= $this->Paginator->sort('Nome Fantasia', 'nome_fantasia') ?></th>
            <th><?= $this->Paginator->sort('Servico', 'descricao') ?></th>
            <th><?= $this->Paginator->sort('MotivoBloqueio', 'descricao') ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes as $cliente): ?>
        <tr>        
            <td>
                <?= $cliente['Cliente']['codigo'] ?>
            </td>
            <td>
                <?= $cliente['Cliente']['codigo_documento'] ?>
            </td>
            <td>
                <?= $cliente['Cliente']['razao_social'] ?>
            </td>
            <td>
                <?= $cliente['Cliente']['nome_fantasia'] ?>
            </td>
            <td>
                <?= $cliente['Servico']['descricao'] ?>
            </td>
            <td>
                <?= $cliente['MotivoBloqueio']['descricao'] ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
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