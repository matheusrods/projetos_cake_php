<?php
echo $paginator->options(array('update' => 'div.lista'));
$total_paginas = $this->Paginator->numbers();
?>
<div class="store-page">
	<div class='actionbar-right '>
		<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success text-white', 'title' => 'Cadastrar')); ?>
	</div>

	<table class="table table-striped">
		<thead>
			<tr>
				<th class="input-small">Cod. </th>
				<th class="input-small">Cód. Cliente</th>
				<th class="input-small">Nome</th>
				<th class="input-medium">Dsname</th>
				<th class="input-small">Apelido</th>
				<th style='width:75px'>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($layouts as $layout) : ?>
				<?php $lt = (object) $layout['MapLayout'] ?>
				<tr style="width: 100%">
					<td class="input-mini" >
						<?= $lt->codigo  ?>
					</td>
					<td class="input-mini" style="min-width: 40%;">
						<?= $lt->codigo_cliente  ?>
					</td>
					<td class="input-mini">
						<?= $lt->nome  ?>
					</td>
					<td class="input-mini">
						<?= $lt->dsname  ?>
					</td>
					<td class="input-mini" style="min-width: 40%;">
						<?= $lt->apelido  ?>
					</td>
					<td>
						<?php if ($lt->ativo) : ?>
							<?= $this->Html->link('', array('action' => 'troca_status', $lt->codigo), array('title' => 'Inativar', 'class' => 'icon-random  change-status')); ?>
							<span class="badge-empty badge badge-success" title="Ativo"></span>
						<?php else : ?>
							<?= $this->Html->link('', array('action' => 'troca_status', $lt->codigo), array('title' => 'Ativar', 'class' => 'icon-random change-status')); ?>
							<span class="badge-empty badge badge-important" title="Inativo"></span>
						<?php endif; ?>
						<?= $this->Html->link('', array('action' => 'editar', $lt->codigo), array('title' => 'Editar', 'class' => 'icon-edit')) ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<div class='row-fluid'>
		<div class='numbers span6'>
			<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'paginacao_anterior')); ?>
			<?php echo $this->Paginator->numbers(); ?>
			<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'paginacao_proximo')); ?>
		</div>
		<div class='counter span6'>
			<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
		</div>
	</div>
</div>
<script src="/portal/js/layouts/listagem.js"></script>