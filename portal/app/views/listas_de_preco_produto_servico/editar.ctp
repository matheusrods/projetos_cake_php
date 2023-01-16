<div class='well'>
	<strong>Código da Lista de Preço: </strong><?= $lista_de_preco['ListaDePreco']['codigo'] ?>
	<strong>Lista de Preço: </strong><?= $lista_de_preco['ListaDePreco']['descricao'] ?>
</div>

<?php echo $this->BForm->create('ListaDePrecoProdutoServico', array('url' => array('controller' => 'listas_de_preco_produto_servico', 'action' => 'editar', $this->passedArgs[0], $this->passedArgs[1] ))); ?>
<div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('codigo'); ?>
	<?php echo $this->BForm->hidden('tem_controle_de_volume'); ?>
	<?php echo $this->BForm->input('Produto.descricao', array('label' => 'Produto', 'class' => 'input-xlarge', 'readonly' => true)); ?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('Servico.descricao', array('label' => 'Servico', 'class' => 'input-xlarge', 'readonly' => true)); ?>
	<?php echo $this->BForm->input('valor', array('label' => 'Valor', 'class' => 'input-medium numeric moeda', 'maxlength' => 14, 'value' => $this->Buonny->moeda($this->data['ListaDePrecoProdutoServico']['valor'], array('edit' => true)))); ?>
	<?php echo $this->BForm->input('valor_maximo', array('label' => 'Valor Máximo', 'class' => 'input-medium numeric moeda', 'maxlength' => 14, 'value' => $this->Buonny->moeda($this->data['ListaDePrecoProdutoServico']['valor_maximo'], array('edit' => true)))); ?>
	<?php echo $this->BForm->input('valor_venda', array('label' => 'Valor Venda', 'class' => 'input-medium numeric moeda', 'maxlength' => 14, 'value' => $this->Buonny->moeda($this->data['ListaDePrecoProdutoServico']['valor_venda'], array('edit' => true)))); ?>
</div>
<div class='row-fluid inline'>
	<label>Tipo de Atendimento:</label>
	<?php echo $this->BForm->input('tipo_atendimento', array('div' => true, 'legend' => false, 'options' => array('0' => 'Ordem de Chegada', '1' => 'Hora Marcada'), 'type' => 'radio')); ?>
</div>

<div id='controle-de-volume' style='<?= ($this->data['ListaDePrecoProdutoServico']['tem_controle_de_volume']) ? '' : 'display:none' ?>'>
	<div class='row-fluid inline' onclick='javascript:return false'>
		<?php echo $this->BForm->input('tipo_premio_minimo', array('legend' => false, 'type' => 'radio', 'label' => array('class' => 'radio inline'), 'options' => array(1 => 'por Produto', 2 => 'por Serviço'))); ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('valor_premio_minimo', array('label' => 'Prêmio Mínimo (R$)', 'class' => 'input-medium numeric moeda', 'value' => $this->Buonny->moeda($this->data['ListaDePrecoProdutoServico']['valor_premio_minimo'], array('edit' => true)))); ?>
		<?php echo $this->BForm->input('qtd_premio_minimo', array('label' => 'Prêmio Mínimo (Qtd)', 'class' => 'input-medium numeric')); ?>
	</div>
</div>
<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('controller' => 'listas_de_preco_produto', 'action' => 'index', $this->passedArgs[0]), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Javascript->codeBlock("setup_mascaras();")) ?>