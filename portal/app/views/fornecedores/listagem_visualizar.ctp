<?php 
	echo $paginator->options(array('update' => 'div#lista-fornecedores-visualizar')); 
	$total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
			<th><?php echo $this->Paginator->sort('Nome Fantasia', 'nome_fantasia') ?></th>
			<th><?php echo $this->Paginator->sort('Razão Social', 'razao_social') ?></th>
			<th><?php echo $this->Paginator->sort('CNPJ', 'codigo_documento') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($fornecedores as $fornecedor): ?>
			<tr class="fornecedor-tr" codigo="<?php echo $fornecedor['Fornecedor']['codigo'] ?>" razao_social="<?php echo $fornecedor['Fornecedor']['razao_social'] ?>" >
				<td class="input-mini"><?php echo $fornecedor['Fornecedor']['codigo'] ?></td>
				<td><?php echo $fornecedor['Fornecedor']['nome'] ?></td>
				<td><?php echo $fornecedor['Fornecedor']['razao_social'] ?></td>
				<td><?php echo $buonny->documento($fornecedor['Fornecedor']['codigo_documento']) ?></td>
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
<?php if ($destino == 'fornecedores_buscar_codigo'): ?>
	<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {
		$('tbody').attr('class', 'line-selector');
		var double = true;
		$('tr.fornecedor-tr').click(function() {
			if(double){
				double = false;
				var codigo = $(this).attr('codigo');
				var razao_social = $(this).attr('razao_social');
				var input_codigo = $('#{$input_id}');
				input_codigo.val(codigo).change();
				var input_display = $('#{$input_display}');
				input_display.val(razao_social).change().blur();
				close_dialog();
			}
		})
	})"); ?>
<?php endif ?>
<?php echo $this->Js->writeBuffer(); ?>