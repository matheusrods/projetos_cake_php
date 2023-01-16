<?php 
	echo $paginator->options(array('update' => 'div#lista-corretoras-visualizar')); 
	$total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
			<th><?php echo $this->Paginator->sort('Nome', 'nome') ?></th>
			<th><?php echo $this->Paginator->sort('Documento', 'codigo_documento') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($corretoras as $corretora): ?>
			<tr class="corretora-tr" codigo="<?php echo $corretora['Corretora']['codigo'] ?>" nome="<?php echo $corretora['Corretora']['nome'] ?>" >
				<td class="input-mini"><?php echo $corretora['Corretora']['codigo'] ?></td>
				<td><?php echo $corretora['Corretora']['nome'] ?></td>
				<td><?php echo $buonny->documento($corretora['Corretora']['codigo_documento']) ?></td>
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
<?php if ($destino == 'corretoras_buscar_codigo'): ?>
	<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {
		$('tbody').attr('class', 'line-selector');
		var double = true;
		$('tr.corretora-tr').click(function() {
			if(double){
				double = false;
				var codigo = $(this).attr('codigo');
				var nome = $(this).attr('nome');
				var input_codigo = $('#{$input_id}');
				input_codigo.val(codigo).change();
				var input_display = $('#{$input_display}');
				input_display.val(nome).change().blur();
				close_dialog();
			}
		})
	})"); ?>
<?php endif ?>
<?php echo $this->Js->writeBuffer(); ?>