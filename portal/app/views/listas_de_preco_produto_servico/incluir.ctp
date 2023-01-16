<div class='well'>
	<strong>Código da Lista de Preço: </strong><?= $lista_de_preco['ListaDePreco']['codigo'] ?>
	<strong>Lista de Preço: </strong><?= $lista_de_preco['ListaDePreco']['descricao'] ?>
</div>

<?php echo $this->BForm->create('ListaDePrecoProdutoServico', array('url' => array('controller' => 'listas_de_preco_produto_servico', 'action' => 'incluir', $this->passedArgs[0]))); ?>
<?php echo $this->element('listas_de_preco_produto_servico/fields'); ?>