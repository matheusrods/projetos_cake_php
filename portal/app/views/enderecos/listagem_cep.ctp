
<?php 
    echo $paginator->options(array('update' => 'div#lista-cep')); 
	$total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
	<thead>
		<tr>
			<th><?php echo $this->Paginator->sort('CEP', 'endereco_cep') ?></th>
			<th><?php echo $this->Paginator->sort('Logradouro', 'endereco_logradouro') ?></th>
			<th><?php echo $this->Paginator->sort('Bairro', 'endereco_bairro') ?></th>
			<th><?php echo $this->Paginator->sort('Cidade', 'endereco_cidade') ?></th>
			<th><?php echo $this->Paginator->sort('Estado', 'endereco_estado') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($ceps as $cep): ?>
			<tr>
				<td>
					<?php echo $this->BForm->hidden('endereco_cep', array('value'=>$cep['VEndereco']['endereco_cep'])); ?>
					<?php echo $this->BForm->hidden('endereco_codigo', array('value'=>$cep['VEndereco']['endereco_codigo'])); ?>
					<?php echo preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep['VEndereco']['endereco_cep']) ?>
				</td>
				<td><?php echo $cep['VEndereco']['endereco_logradouro'] ?></td>
				<td><?php echo $cep['VEndereco']['endereco_bairro'] ?></td>
				<td><?php echo $cep['VEndereco']['endereco_cidade'] ?></td>
				<td><?php echo $cep['VEndereco']['endereco_estado'] ?></td>
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
<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() {
	$('tbody').attr('class', 'line-selector');
	var double = true;
	$('tr').click(function() {
		if(double){
			double = false;
			var endereco_cep = $(this).find('#endereco_cep').val();
			var endereco_codigo = $(this).find('#endereco_codigo').val();
			var input = $('#{$input_id}');
			input.val(endereco_cep);
			input.blur();
			buscar_cep(input, '.evt-endereco-codigo', endereco_codigo);
			close_dialog();
		}
	})
})"); ?>
<?php echo $this->Js->writeBuffer(); ?>