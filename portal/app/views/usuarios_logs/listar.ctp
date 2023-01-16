<?php
    echo $paginator->options(array('update' => 'div#logs'));
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?= $this->Paginator->sort('Responsável', 'apelido') ?></th>
            <th><?= $this->Paginator->sort('Data', 'data_inclusao') ?></th>
            <th><?= $this->Paginator->sort('Perfil', 'descricao') ?></th>
        </tr>
    </thead>
    <tbody>
    	<?php if(is_array($usuarios_log) || is_object($usuarios_log)) : ?>
	        <?php foreach ($usuarios_log as $usuario ): ?>
	        <tr>
	            <td><?= $usuario['Usuario']['apelido'] ?></td>
	            <td><?= $usuario['UsuarioLog']['data_inclusao'] ?></td>
	            <td><?= $usuario['Uperfil']['descricao'] ?></td>
	        </tr>
	        <?php endforeach; ?>    		
    	<?php endif; ?>
    </tbody>
</table>
<div class='row-fluid'>
	<div class='numbers span6'>
		<?php if($total_paginas > 1) : ?>
			<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
			<?php echo $this->Paginator->numbers(); ?>
			<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>		
		<?php endif; ?>
	</div>
	<div class='counter span6'>
	    <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
	</div>
</div>
<?php echo $this->Js->writeBuffer(); ?>