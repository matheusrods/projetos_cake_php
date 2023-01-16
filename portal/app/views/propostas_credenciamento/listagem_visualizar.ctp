<?php 
	echo $paginator->options(array('update' => 'div#lista-credenciados-visualizar')); 
	$total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
			<th><?php echo $this->Paginator->sort('Razão Social', 'razao_social') ?></th>
			<th><?php echo $this->Paginator->sort('Documento', 'codigo_documento') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($propostas_credenciamento as $proposta): ?>
			<tr class="credenciado-tr" codigo="<?php echo $proposta['PropostaCredenciamento']['codigo'] ?>" nome="<?php echo $proposta['PropostaCredenciamento']['razao_social'] ? $proposta['PropostaCredenciamento']['razao_social'] : $proposta['PropostaCredenciamento']['nome_fantasia'] ?>" >
				<td class="input-mini"><?php echo $proposta['PropostaCredenciamento']['codigo'] ?></td>
				<td><?php echo $proposta['PropostaCredenciamento']['razao_social'] ? $proposta['PropostaCredenciamento']['razao_social'] : $proposta['PropostaCredenciamento']['nome_fantasia'] ?></td>
				<td><?php echo $buonny->documento($proposta['PropostaCredenciamento']['codigo_documento']) ?></td>
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
<?php if ($destino == 'credenciados_buscar_codigo'): ?>
	<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {
		$('tbody').attr('class', 'line-selector');
		var double = true;
		$('tr.credenciado-tr').click(function() {
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