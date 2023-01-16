<?php if (empty($cliente)): ?>
	<div class='form-procurar'>	
		<div class='well'>
			<?php echo $this->BForm->create('ItemPedido', array('autocomplete' => 'off', 'url' => array('controller' => 'itens_pedidos', 'action' => 'listar'))) ?>
			<div class="row-fluid inline">
				<?php echo $this->Buonny->input_codigo_cliente($this); ?>
			</div>
			<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
			<?php echo $this->BForm->end();?>
		</div>
	</div>
<?php else: ?>
	<div class='well'>
		<strong>Pedido: </strong><?= $codigo_pedido ?>
		<strong>Mes: </strong><?= $meses[$mes_referencia]; ?>
		<strong>Ano: </strong><?= date('Y'); ?>
	</div>
	<table class='table'>
		<thead>
			<th>Produto</th>
			<th class='input-mini numeric'>Qtd</th>
			<th class='input-mini numeric'>Val. Unitário</th>
			<th class='input-small numeric'>Val. Total</th>			
		</thead>
		<tbody>
			<?php if( !empty($produtos) ): ?>

				<?php foreach($produtos as $key => $value):?>
					<tr>
						<td><?php echo $value['Produto']['descricao']; ?></td>
						<td class="numeric"><?php echo $value['ItemPedido']['quantidade']; ?></td>
						<td class="numeric"><?php echo $this->Buonny->moeda($value[0]['valor_unitario']); ?></td>
						<td class="numeric"><?php echo $this->Buonny->moeda($value['ItemPedido']['quantidade'] * $value[0]['valor_unitario']); ?></td>						
					</tr>
				<?php endforeach; ?>

			<?php endif; ?>
		</tbody>
	</table>
	<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
	<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
	
<?php endif ?>