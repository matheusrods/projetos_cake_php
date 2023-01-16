<div class='well'>
	<strong>Código: </strong><?= $cliente['Cliente']['codigo']; ?>
	<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social']; ?>
	<strong>Ano: </strong><?= date('Y'); ?>
</div>
<?= $this->BForm->create('ItemPedido', array('url' => array('controller' => 'itens_pedidos', 'action' => 'incluir_pedido', $codigo_cliente))); ?>
<div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('codigo_cliente_pagador', array('value'=>$codigo_cliente)); ?>	
	<?php echo $this->BForm->hidden('manual', array('value'=>1)); ?>
	<?php echo $this->BForm->hidden('codigo_usuario_inclusao', array('value'=>$codigo_usuario_inclusao)); ?>
	<?php echo $this->BForm->hidden('lista_de_preco_codigo', array('value'=>$lista_de_preco_codigo)); ?>
	<?php echo $this->BForm->input('codigo_produto', array('label' => 'Produto da lista de preço: '.$lista_de_preco, 'class' => 'input-xlarge', 'options' => $produtos, 'empty'=>'Selecione o produto')); ?>
	<?php echo $this->BForm->input('servico_codigo', array('label' => 'Serviço', 'class' => 'input-xlarge', 'options'=>array(), 'empty'=>'Selecione um serviço')); ?>
</div>

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('quantidade', array('label' => 'Qtd. Produto', 'class' => 'input-small numeric just-number', 'value'=>1)); ?>
	<?php echo $this->BForm->input('valor_total', array('label' => 'Valor Unitário', 'class' => 'input-medium numeric moeda', 'maxlength'=>false)); ?>
	<?php echo $this->BForm->input('quantidade_parcela', array('label' => 'Qtd. Parcela', 'class' => 'input-small numeric just-number', 'value'=>1)); ?>
</div>

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('mes', array('label' => 'Selecione o mês', 'options' => $meses, 'class' => 'input-medium')); ?>	
	<?php echo $this->BForm->input('ano', array('label' => 'Selecione o ano', 'options' => $anos, 'class' => 'input-small')); ?>
	<?php echo $this->BForm->input('codigo_condicao_pagamento', array('label' => 'Selecione a condição de pagamento', 'options' => $condicoes_pagamento, 'class' => 'input-large', 'default' => '14')); ?>	
</div>

<div class='form-actions'>
	<button type="button" class="btn btn-primary js-salvar">Salvar</button>
	<?php echo $this->BForm->submit('Avançar', array('div' => 'hide', 'class' => 'btn btn-primary')); ?>

	<?= $html->link('Voltar', array('action' => 'listar', $codigo_cliente), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Javascript->codeBlock("
	setup_mascaras();
	function atualizaComboServicos(lista_de_preco, produto) {
		jQuery.ajax({
			url: baseUrl + 'itens_pedidos/lista_de_preco/'+lista_de_preco+'/'+produto+'/'+Math.random(), 
			success: function(data){
				jQuery('#ItemPedidoServicoCodigo').html(data);
				$('#ItemPedidoValorTotal').val('');
			}
		});
	}

	function pegaValor(codigo_lista_de_preco, codigo_cliente, codigo_produto, codigo_lista_de_preco_produto_servico, element_valor) {
		
		jQuery.ajax({
			url: baseUrl + 'itens_pedidos/pega_valor_servico/'+codigo_lista_de_preco+'/'+codigo_cliente+'/'+codigo_produto+'/'+codigo_lista_de_preco_produto_servico+'/'+Math.random(), 
			beforeSend: function(){
				jQuery(element_valor).val('Aguardando resposta...');
				// jQuery(element_valor).val('');
			},
			success: function(data){				
				jQuery(element_valor).val(data);
			}
		});
	}

	jQuery('#ItemPedidoServicoCodigo').change(function() {
		var codigo_lista_de_preco = $('#ItemPedidoListaDePrecoCodigo').val();
		var codigo_cliente = jQuery('#ItemPedidoCodigoClientePagador').val();
		var codigo_produto = jQuery('#ItemPedidoCodigoProduto').val();

		pegaValor(codigo_lista_de_preco, codigo_cliente, codigo_produto, $(this).val(),'#ItemPedidoValorTotal');
	});
	
	jQuery('#ItemPedidoCodigoProduto').change(function() {
		var lista_de_preco = $('#ItemPedidoListaDePrecoCodigo').val();
		$('#ItemPedidoServicoCodigo option:selected').text('Aguarde, carregando...');
		$('#ItemPedidoValorTotal').val('Aguarde, carregando...');
		atualizaComboServicos(lista_de_preco,$(this).val());		
	});

	$('.js-salvar').click(function(event) {
		$('#ItemPedidoIncluirPedidoForm').submit();
	})

")) ?>
