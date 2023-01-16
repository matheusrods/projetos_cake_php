<?php 
	echo $paginator->options(array('update' => 'div#lista-cnae-visualizar')); 
	$total_paginas = $this->Paginator->numbers();
?>

<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('Cnae', 'cnae') ?></th>
			<th><?php echo $this->Paginator->sort('Ramo de Atividade', 'descricao') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($cnaes as $cnae): ?>
			<tr class="cnae-tr" cnae="<?php echo $cnae['Cnae']['cnae'] ?>" descricao="<?php echo $cnae['Cnae']['descricao'] ?>" >
				<td class="input-mini"><?php echo $cnae['Cnae']['cnae'] ?></td>
				<td><?php echo $cnae['Cnae']['descricao'] ?></td>
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
<?php if ($destino == 'cnae_buscar_codigo') : ?>
	<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {

		$('tbody').attr('class', 'line-selector');
		var double = true;

		$('tr.cnae-tr').click(function() {
			if(double) {
				double = false;
				var cnae = $(this).attr('cnae');
				var descricao = $(this).attr('descricao');
			
				var input_codigo = $('#{$input_id}');
				input_codigo.val(cnae).change();
				
				var input_display = $('#{$input_display}');
				input_display.val(descricao).change().blur();
				
				close_dialog();
			}
		})
	})"); ?>
<?php endif ?>
<?php echo $this->Js->writeBuffer(); ?>