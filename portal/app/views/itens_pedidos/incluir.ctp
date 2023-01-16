<?php echo $this->BForm->create('ItemPedido', array('url' => array('controller' => 'itens_pedidos', 'action' => 'incluir'))); ?>
<div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('codigo_cliente_pagador', array('value'=>$cliente_codigo)); ?>
	<?php echo $this->BForm->hidden('manual', array('value'=>1)); ?>
	<?php echo $this->BForm->hidden('codigo_usuario_inclusao', array('value'=>$codigo_usuario_inclusao)); ?>
	<?php echo $this->BForm->input('codigo_produto', array('label' => 'Produto da lisa de preço: '.$lista_de_preco, 'class' => 'input-xlarge', 'options' => $produtos)); ?>	
	<?php echo $this->BForm->input('quantidade', array('label' => 'Qtd. Produto', 'class' => 'input-small numeric moeda')); ?>	
	<?php echo $this->BForm->input('valor_total', array('label' => 'Val. unitário parcela', 'class' => 'input-medium numeric moeda')); ?>
	<?php echo $this->BForm->input('quantidade_parcela', array('label' => 'Qtd. Parcela', 'class' => 'input-small numeric moeda')); ?>	
</div>

<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>	
	<?= $html->link('Voltar', array('action' => 'listar', $cliente_codigo), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>

<div class='well'>
	<strong>Código: </strong><?= $cliente['Cliente']['codigo']; ?>
	<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social']; ?>
	<strong>Ano: </strong><?= date('Y'); ?>
	<strong>Mês: </strong><?= $meses[(int) date('m')]; ?>
</div>

<table class='table'>
	<thead>
		<th>Produto</th>
		<th class='input-mini numeric'>Qtd</th>
		<th class='input-mini numeric'>Val. Unitário</th>
		<th class='input-small numeric'>Val. Total</th>
		<th class='action-icon'></th>
		<th class='action-icon'></th>
	</thead>
	<tbody>
		<?php if( !empty($itens_pedido) ): ?>

			<?php foreach($itens_pedido as $key => $value):?>
				<tr>
					<td><?php echo $value['Produto']['descricao']; ?></td>
					<td class="numeric"><?php echo $value['ItemPedido']['quantidade']; ?></td>
					<td class="numeric"><?php echo $this->Buonny->moeda($value[0]['valor_unitario']); ?></td>
					<td class="numeric"><?php echo $this->Buonny->moeda($value['ItemPedido']['quantidade'] * $value[0]['valor_unitario']); ?></td>
					<td class='action-icon'><?= $this->Html->link('', array('controller' => 'itens_pedidos', 'action' => 'editar', $cliente_codigo, $value[0]['codigo_item_pedido']), array('class' => 'icon-edit', 'title' => 'Editar')) ?></td>
					<td class='action-icon'><?= $this->Html->link('', array('controller' => 'itens_pedidos', 'action' => 'excluir', $cliente_codigo, $value[0]['codigo_pedido'], $value[0]['codigo_item_pedido']), array('class' => 'icon-trash', 'title' => 'Excluir')) ?></td>
				</tr>
			<?php endforeach; ?>

		<?php endif; ?>
	</tbody>
</table>

<?php 
$this->addScript(

	$this->Javascript->codeBlock("
		
		$('#ItemPedidoCodigoProduto').change(function(){

			pegaValor( $(this).val() );
		})

		function pegaValor( codigo ) {

			$.ajax({
				url : '/portal/itens_pedidos/pega_valor_produto/'+codigo,
				type : 'GET',
				dataType : 'TEXT',

				beforeSend : function(){ 
					$('#ItemPedidoValorTotal').attr( 'readonly', 'readonly' );
				},

				success : function(data){
					
					$('#ItemPedidoValorTotal').val(data);
					$('#ItemPedidoValorTotal').removeAttr( 'readonly' );
				},

				error : function(){

				}
			})
		}

	")) 
?>