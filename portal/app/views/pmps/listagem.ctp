<?php
    echo $paginator->options(array('update' => 'div.lista'));
    $total_paginas = $this->Paginator->numbers();
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-small"><?= $this->Paginator->sort('Matriz', 'ClienteMatriz.razao_social') ?></th>
            <th class="input-medium"><?= $this->Paginator->sort('Unidade', 'ClienteUnidade.razao_social') ?></th>
            <th class="input-medium"><?= $this->Paginator->sort('Resumo Desc.', 'Pmps.descricao') ?></th>
            <th style='width:75px'>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $d): ?>
        <tr>
            <td><?= $d[0]['cliente_matriz'] ?></td>
            <td><?= $d[0]['cliente_unidade'] ?></td>
            <td><?= $d[0]['material_pronto_socorro_resumo'] ?></td>
            <td>
                <?= $html->link('', array('action' => 'editar', $d[0]['codigo_cliente_matriz'], $d[0]['codigo_cliente_unidade']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
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