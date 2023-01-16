<div class='well'>
    <strong>Perfil:</strong> <?= $uperfil['Uperfil']['descricao'] ?>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th class='input-medium'><?= $this->Paginator->sort('Login', 'apelido') ?></th>
            <th><?= $this->Paginator->sort('Nome', 'nome') ?></th>
            <th>Cliente</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $usuario): ?>
        <tr>
            <td class='input-medium'><?= $usuario['Usuario']['apelido'] ?></td>
            <td><?= $usuario['Usuario']['nome'] ?></td>
            <td><?= $usuario['Cliente']['razao_social'] ?></td>
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