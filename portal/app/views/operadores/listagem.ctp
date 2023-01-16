<?php
    echo $paginator->options(array('update' => 'div.lista'));
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('Login', 'apelido') ?></th>
            <th><?= $this->Paginator->sort('Nome', 'nome') ?></th>
            <th><?= $this->Paginator->sort('Status', 'oras_eobj_codigo') ?></th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $usuario): ?>
        <tr>
            <td><?= $usuario['TUsuaUsuario']['usua_login'] ?></td>
            <td><?= $usuario['TPessPessoa']['pess_nome'] ?></td>
            <?php if($usuario['TOrasObjetoRastreado']['oras_eobj_codigo'] == '1') : ?>
                 <td><?= "Ativo" ?></td>
            <?php endif ?>
            <?php if($usuario['TOrasObjetoRastreado']['oras_eobj_codigo'] == '2') : ?>
                <td><?= "Inativo" ?></td>
            <?php endif ?>

            <td><?= $html->link('', array('action' => 'gerenciar_areas_atuacoes', $usuario['TUsuaUsuario']['usua_pfis_pess_oras_codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar áreas de atuação')) ?></td>
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