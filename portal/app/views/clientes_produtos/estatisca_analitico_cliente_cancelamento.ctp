<?php 
	echo $this->Paginator->options(array('update' => 'div.lista')); 
?>
<div class="lista">
<table class='table table-striped'>
	<thead>
		<th class="numeric"><?php echo $this->Paginator->sort('Código Cliente', 'codigo_cliente'); ?></th>
		<th><?php echo $this->Paginator->sort('Razão Social', 'codigo_cliente'); ?></th>
		<th><?php echo $this->Paginator->sort('Produto', 'codigo_cliente'); ?></th>
		<th><?php echo $this->Paginator->sort('Motivo Cancelamento', 'codigo_cliente'); ?></th> 
	</thead>
	<tbody>
		<?php foreach ($estatistica_analitico as $dado): ?>
				<tr>
					<td class="numeric"><?= $dado['ClienteProduto']['codigo_cliente']; ?></td>
					<td><?= $dado['Cliente']['razao_social']; ?></td>
					<td><?= $dado['Produto']['descricao']; ?></td>
					<td><?= $dado['MotivoCancelamento']['descricao']; ?></td>
				</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<td><strong>Total</strong></td>
			<td class="input-xlarge"><strong><?php echo $this->Paginator->counter('{:count}'.' Registro(s)');?></strong></td>
			<td></td>
			<td></td>
		</tr>
	</tfoot>
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
</div>