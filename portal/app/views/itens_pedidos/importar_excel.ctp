<div class='form-procurar well'>
	<?php echo $this->BForm->create('ItemPedido', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'itens_pedidos', 'action' => 'importar_excel'))); ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('tipo_arquivo', array('label' => 'Tipo de Arquivo', 'class' => 'input-medium', 'options' => array('1' => 'ADE Autotrac'), 'empty' => 'Tipo de arquivo')); ?>
		</div>

		<div class="row-fluid inline">
			<?php echo $this->BForm->input('arquivo', array('type'=>'file', 'label' => false)); ?>
		</div>

		<?php echo $this->BForm->submit('Importar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?php echo $this->BForm->end(); ?>
</div>
<?php if (isset($resultado)): ?>
<?php echo $this->BForm->create('ItemPedido', array('id' => 'ItemPedidoPagadoresForm', 'url' => array('controller' => 'itens_pedidos', 'action' => 'criar_pedido_autotrac',$mes_referencia, $ano_referencia))); ?>
<div class='well'>
	<strong>Mês de Referência:</strong> <?php echo $mes_referencia ?>
	<strong>Ano de Referência:</strong> <?php echo $ano_referencia ?>
	<strong>Valor Unitário (R$):</strong> <?php echo $this->Buonny->moeda($valor_unitario) ?>
</div>

<table class="table table-striped">
	<thead>
		<tr>
			<th class="input-large">CNPJ Transportador</th>
			<th class="input-xxlarge">Nome Transportador</th>
			<th class="input-medium">Código Pagador</th>
			<th class="input-medium text-right">Qtd de Terminais</th>
			<th class="input-medium text-right">Valor a Pagar (R$)</th>
		</tr>
	</thead>
	<tbody>
	<?php $total  = 0 ?>
	<?php $indice = 0 ?>
	<?php foreach ($resultado as $cnpj => $value):  ?>
		<tr>
			<td><?php echo $this->Buonny->documento($cnpj); ?></td>
			<td><?php echo $value['nome']; ?></td>
			<td><?php echo $this->BForm->input("ItemPedido.{$indice}.codigo_cliente_pagador", array('class' => 'input-mini', 'type' => 'text', 'label' => false, 'div' => false, 'value' => $value['pagador'])); ?></td>
			<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function(){ $('#ItemPedido<?= $indice ?>CodigoClientePagador').search_clientes();}) 
			//]]>
			</script>
			<?php echo $this->BForm->hidden("ItemPedido.{$indice}.quantidade", array('value' => $value['quantidade'])); ?>
			<td class="text-right"><?php echo $value['quantidade']; ?></td>
			<td class="text-right"><?php echo $this->BForm->input("ItemPedido.{$indice}.valor_total", array('class' => 'numeric moeda input-mini', 'label' => false, 'div' => false, 'value' => $this->Buonny->moeda($value['valor_total']))); ?></td>
		</tr>
		<?php $total += $value['valor_total'] ?>
		<?php $indice++; ?>
	<?php endforeach ?>
	</tbody>
</table>
<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar Pedidos', array('div' => false, 'class' => 'btn btn-success')); ?>
</div>
<?php echo $javascript->codeblock("

	jQuery(document).ready(function() 
		{
			setup_mascaras();
			$('#ItemPedidoPagadoresForm').submit(function() {

				var retorno = true;
				$('form#ItemPedidoPagadoresForm :input').each(function(){ 

					if (!$(this).val() && retorno){
						alert('Favor preencher todos os pagadores!');
						retorno = false;
						
					}
				}); 

				return retorno;
					
			});
			
		});
	"); ?> 

<?php endif ?>