<?php 
	echo $paginator->options(array('update' => 'div#lista-cid-visualizar')); 
	$total_paginas = $this->Paginator->numbers();
?>

<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('CID10', 'codigo_cid10') ?></th>
			<th><?php echo $this->Paginator->sort('Descrição', 'descricao') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($cids as $cid): ?>
			<tr class="cid-tr" cid="<?php echo $cid['Cid']['codigo_cid10'] ?>" descricao="<?php echo $cid['Cid']['descricao'] ?>" >
				<td class="input-mini"><?php echo $cid['Cid']['codigo_cid10'] ?></td>
				<td><?php echo $cid['Cid']['descricao'] ?></td>
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
<?php if ($destino == 'cid_buscar_codigo') : ?>
	<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {

		$('tbody').attr('class', 'line-selector');
		var double = true;

		$('tr.cid-tr').click(function() {
			if(double) {
				double = false;
				var cid = $(this).attr('cid');
				var descricao = $(this).attr('descricao');
			
				var input_codigo = $('#{$input_id}');
				input_codigo.val(cid).change();
				
				var input_display = $('#{$input_display}');
				input_display.val(descricao).change().blur();
				
				close_dialog();
			}
		})
	})"); ?>
<?php endif ?>
<?php echo $this->Js->writeBuffer(); ?>