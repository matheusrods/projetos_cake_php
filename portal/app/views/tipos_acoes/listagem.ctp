<?php
    echo $paginator->options(array('update' => 'div.lista'));
    $total_paginas = $this->Paginator->numbers();
?>

<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-small"><?= $this->Paginator->sort('Descrição', 'descricao') ?></th>
            <th class="input-medium"><?= $this->Paginator->sort('Classificação', 'classificacao') ?></th>
            <th style='width:75px'>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $d): ?>
        <tr>
            <td><?= $d[0]['descricao'] ?></td>
            <td><?= $d[0]['classificacao'] ?></td>
            <td>
                <?php if($d[0]['status'] == 0): ?>
                    <span class="badge-empty badge badge-important" title="Desativado"></span>
                    <a href="#" class="icon-random" onclick="return fnc_toggle_tipo_acao('<?=$d[0]['codigo']?>', 1)" title="Ativar"></a>
                <?php elseif($d[0]['status'] == 1): ?>
                    <span class="badge-empty badge badge-success" title="Ativado"></span>
                    <a href="#" class="icon-random" onclick="return fnc_toggle_tipo_acao('<?=$d[0]['codigo']?>', 0)" title="Desativar"></a>
                <?php endif; ?>
                <?= $html->link('', array('action' => 'editar', $d[0]['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
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
