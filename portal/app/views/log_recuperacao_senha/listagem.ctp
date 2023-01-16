<?php if(isset($logs) && !empty($logs)):?>
	<?php
	    echo $paginator->options(array('update' => 'div.lista'));
	?>
	<table class='table table-striped' style='max-width:none;white-space:nowrap'>
		<thead>
			<th><?php echo $this->Paginator->sort('IP', 'ip') ?></th>
			<th><?php echo $this->Paginator->sort('Navegador', 'navegador') ?></th>
			<th><?php echo $this->Paginator->sort('Data', 'data_inclusao') ?></th>
			<th><?php echo $this->Paginator->sort('Código Usuário', 'codigo_usuario') ?></th>
			<th><?php echo $this->Paginator->sort('Usuário', 'Usuario.apelido') ?></th>
			<th><?php echo $this->Paginator->sort('E-mail de envio', 'email') ?></th>
		</thead>
		<tbody>
			<?php foreach ($logs as $dado): ?>
				<tr style='word-wrap:none'>
					<td><?= $dado['LogRecuperaSenha']['ip'] ?></td>
					<td><?= $dado['LogRecuperaSenha']['navegador'] ?></td>
					<td><?= $dado['LogRecuperaSenha']['data_inclusao'] ?></td>
					<td><?= $dado['LogRecuperaSenha']['codigo_usuario'] ?></td>
					<td><?= $dado['Usuario']['apelido'] ?></td>
					<td><?= $dado['LogRecuperaSenha']['email'] ?></td>
				</tr>
			<?php endforeach ?>
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
<?php else:?>
	<div class="alert">Nenhum registro encontrado</div>	
<?php endif;?>