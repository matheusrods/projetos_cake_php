<div class='well'>
	<strong>Nome do arquivo: </strong><?= $pedidos_exame[0][0]['nome_arquivo'] ?>
	<strong>Data inclusão: </strong><?= $pedidos_exame[0][0]['data_inclusao'] ?>
</div>
<table class='table table-striped'>
	<thead>
		<th>Unidade</th>
		<th class='numeric input-small'>Pedidos de Exame</th>
	</thead>
	<?php $total = 0 ?>
	<tbody>
		<?php foreach ($pedidos_exame as $key => $value): ?>
			<?php $total += $value[0]['qtd_pedidos_exame'] ?>
			<tr>
				<td><?= $value[0]['nome_empresa'] ?></td>
				<td class='numeric input-small'><?= $value[0]['qtd_pedidos_exame'] ?></td>
			</tr>
		<?php endforeach ?>
	</tbody>
	<tfoot>
		<tr>
			<td></td>
			<td class='numeric'><?= $total ?></td>
		</tr>
	</tfoot>
</table>
<?= $this->BForm->create('ImportacaoPedidosExame', array('url' => array('controller' => 'importar', 'action' => 'eliminar_importacao_pedido_exame', $this->passedArgs[0], $this->passedArgs[1]))) ?>
<?= $this->BForm->submit('Excluir', array('div' => false, 'class' => 'btn btn-primary')) ?>
<?= $this->Html->link('Voltar',array('controller'=>'importar','action'=>'importar_pedido_exame', $this->passedArgs[0]) , array('class' => 'btn')); ?>
<?= $this->BForm->end() ?>