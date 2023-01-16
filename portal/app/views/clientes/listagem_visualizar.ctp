<?php 
	echo $paginator->options(array('update' => 'div#lista-clientes-visualizar')); 
	$total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
			<th><?php echo $this->Paginator->sort('Razão Social', 'razao_social') ?></th>
			<th><?php echo $this->Paginator->sort('Nome Fantasia', 'nome_fantasia') ?></th>
			<th><?php echo $this->Paginator->sort('Documento', 'codigo_documento') ?></th>
			<?php if ($destino != 'clientes_buscar_codigo'): ?>
				<th>&nbsp;</th>
			<?php endif ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach($clientes as $cliente): ?>
			<tr class="cliente-tr" codigo="<?php echo $cliente['Cliente']['codigo'] ?>" >
				<td class="input-mini"><?php echo $cliente['Cliente']['codigo'] ?></td>
				<td>
				<?php
				if (!is_null($cliente['GrupoEconomico']['codigo_cliente'])) {
					?>
					<img class="icon icon-24" data-placement="left" title="Matriz" src="<?php echo $this->webroot; ?>img/icon_star.svg" />
					<?php
				}
				?><?php echo $cliente['Cliente']['razao_social'] ?></td>
				<td><?php echo $buonny->documento($cliente['Cliente']['nome_fantasia']) ?></td>
				<td><?php echo $buonny->documento($cliente['Cliente']['codigo_documento']) ?></td>
				<?php if ($destino != 'clientes_buscar_codigo'): ?>
					<td class="pagination-centered">
						<?php echo $html->link('', array('action' => 'visualizar', $cliente['Cliente']['codigo']), array('class' => 'icon-eye-open', 'title' => 'Visualizar')) ?>
					</td>
				<?php endif ?>
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
<?php if ($destino == 'clientes_buscar_codigo'): ?>
	<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {
		$('tbody').attr('class', 'line-selector');
		var double = true;
		$('tr.cliente-tr').click(function() {
			if(double){
				double = false;
				var codigo = $(this).attr('codigo');
				var input = $('#{$input_id}');
				input.val(codigo).change().blur();
				close_dialog();
			}
		})
		$('.tooltip').tooltip();
	})"); ?>
<?php endif ?>
<?php echo $this->Js->writeBuffer(); ?>