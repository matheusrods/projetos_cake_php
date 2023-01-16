<?php echo $this->BForm->create('ItemPedido', array('url' => array('controller' => 'itens_pedidos', 'action' => 'editar', $this->passedArgs[0], $this->passedArgs[1]))); ?>		
	<div class='well'>
		<p><strong>Lista de preço: </strong><?= $lista_de_preco_descricao; ?></p>
		<p><strong>Produto: </strong><?= $descricaoProduto; ?></p>
		<p><strong>Serviço: </strong><?= $descricaoServicoPedido; ?></p>
	</div>
<div class='row-fluid inline'>	
	<?php echo $this->BForm->hidden('codigo', array('value'=>$this->passedArgs[1])); ?>
	<?php echo $this->BForm->hidden('DetalheItemPedidoManual.codigo', array('value'=>$detalhe['DetalheItemPedidoManual']['codigo'])); ?>
	<?php echo $this->BForm->input('quantidade', array('label' => 'Qtd. Produto', 'class' => 'input-small numeric', 'value'=> $detalhe['DetalheItemPedidoManual']['quantidade'])); ?>
	<?php echo $this->BForm->input('valor', array('label' => 'Valor Unitário', 'class' => 'input-small numeric moeda', 'value'=> $this->Buonny->moeda($detalhe['DetalheItemPedidoManual']['valor']))); ?>	
</div>
<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('action' => 'listar', $this->passedArgs[0]), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>

<?php $this->addScript($this->Javascript->codeBlock("setup_mascaras();")); ?>

