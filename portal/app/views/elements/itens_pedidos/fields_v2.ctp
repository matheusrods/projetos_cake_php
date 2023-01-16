<div class="row-fluid margin-top-10">
	<div class="span4">
		<?php  echo $this->BForm->input('Pedido.codigo_vendedor', array('label' => 'Vendedor:', 'class' => 'span12', 'options' => $vendedores, 'empty' => 'Selecione o vendedor')); ?>
	</div>
	<div class="span3">
		<?php echo $this->BForm->input('Pedido.codigo_condicao_pagamento', array('label' => 'Forma de recebimento:', 'class' => 'span12', 'options' => $formas_pagto, 'empty' => 'Selecione a forma de recebimento')); ?>
	</div>
	<div class="span5">
			<div class="span3">
				<!--input type="text" id="js-vl-unit" maxlength="10" class="span5 moeda numeric"-->

				<?php echo $this->BForm->input('Pedido.valor_desconto', array('label' => 'Desconto:', 'class' => 'span12 moeda numeric','value' =>  (isset($this->data['Pedido']['valor_desconto'])) ?  $this->data['Pedido']['valor_desconto'] : ''  )); ?>

				</div>	
	</div>
</div>

<div class="margin-top-20">
	<h4>Insira os serviços:</h4>
	<div class="js-entrada">
		<div class="row-fluid">
			<div class="span6"><label>Serviço:</label></div>
			<div class="span1"><label>Quantidade:</label></div>
			<div class="span2 numeric"><label>Valor unitário:</label></div>
			<div class="span2 numeric"><label>Valor total:</label></div>
		</div>	
		<div class="row-fluid app-begin padding-top-5">
			<div class="span6">
				<input type="text" id="js-desc" placeholder="Digite o serviço" class="span12 js-servico margin-left-5">
			</div>
			<input type="hidden" id="js-codigo-servico">
			<input type="hidden" id="js-codigo-produto">
			<div class="span1">
				<input type="text" id="js-quant" class="span12 just-number numeric">
			</div>
			<div class="span2">
				<input type="text" id="js-vl-unit" maxlength="10" class="span12 moeda numeric">
			</div>	
			<div class="span2">
				<input type="text" id="js-vl-tot" class="span12 moeda numeric" readonly="readonly">
			</div>	
			<div class="span1">
				<button type="button" class="btn btn-mini btn-success margin-top-4 js-add-resultado">Confirmar</button>
			</div>				
		</div>
	</div>
</div>
<div class="inseridos">
	<?php if(!empty($this->data['ItemPedido'])) {
		$valor_total = 0.00;
		$c = 0;
		foreach ($this->data['ItemPedido'] as $key => $item_pedidos) { 
			foreach ($item_pedidos as $key2 => $itens) { ?>
			<div class="row-fluid div-table">
				<div class="span6 padding-left-10 desc"><?php  echo $itens['descricao'] ?></div>
				<div class="span1 padding-left-10 quant"><?php echo number_format($itens['quantidade'], 0) ?></div>
				<div class="span2 padding-left-10 vl-unit" data-preco="<?php echo ($itens['valor_unitario'] * $itens['quantidade']); ?>"><?php echo $this->Buonny->moeda($itens['valor_unitario'], array('places' => 2, 'nozero' => false)); ?></div>
				<div class="span2 padding-left-10 vl-tot"><?php echo $this->Buonny->moeda(($itens['valor_unitario'] * $itens['quantidade']), array('places' => 2, 'nozero' => false)); ?></div>
				<div class="span1" style="text-align:inherit;padding-top:initial">
					<button type="button" class="btn btn-mini btn-danger margin-top-4 js-remover-resultado"<i class="icon-remove icon-white"></i></button>
				</div>
				<?php if(!empty($itens['codigo_item'])) { ?>
				<input type="hidden" name="data[ItemPedido][<?php echo $key ?>][<?php echo $key2 ?>][codigo_item]" id="data[ItemPedido][<?php echo $itens['codigo_item'] ?>][codigo_item]" value="<?php echo $itens['codigo_item'] ?>">
				<?php } ?>

				<?php if(!empty($itens['codigo_detalhe'])) { ?>
				<input type="hidden" name="data[ItemPedido][<?php echo $key ?>][<?php echo $key2 ?>][codigo_detalhe]" id="data[ItemPedido][<?php echo $itens['codigo_item'] ?>][codigo_detalhe]" value="<?php echo $itens['codigo_detalhe'] ?>">
				<?php } ?>
				<input type="hidden" name="data[ItemPedido][<?php echo $key ?>][<?php echo $key2 ?>][descricao]" value="<?php echo $itens['descricao'] ?>">
				<input type="hidden" name="data[ItemPedido][<?php echo $key ?>][<?php echo $key2 ?>][codigo_servico]" value="<?php echo $itens['codigo_servico'] ?>">
				<input type="hidden" name="data[ItemPedido][<?php echo $key ?>][<?php echo $key2 ?>][quantidade]" value="<?php echo $itens['quantidade'] ?>">
				<input type="hidden" name="data[ItemPedido][<?php echo $key ?>][<?php echo $key2 ?>][valor_unitario]" value="<?php echo $itens['valor_unitario'] ?>">
			</div>	
			<?php
			$valor_total = $valor_total + ($itens['valor_unitario'] * $itens['quantidade']);
			$c++;
		} 
	} 
} ?>
</div>
<div class="row-fluid margin-top-8 hide in">
	<div class="span6 padding-left-10"><strong>TOTAL</strong></div>
	<div class="span1 padding-left-10"></div>
	<div class="span2 padding-left-10"></div>
	<div class="span2 padding-left-10 text-right js-valor-total" style="font-weight: bold;"></div>
</div>

<div class='form-actions'>
	<button type="button" class="btn btn-primary js-salvar">Salvar</button>
	<?php echo $this->BForm->submit('Avançar', array('div' => 'hide', 'class' => 'btn btn-primary js-submeter')); ?>
	<?php echo $this->Html->link('Voltar', array('action' => 'listar_v2', $codigo_cliente), array('class' => 'btn btn-default')); ?>
</div>

<div id="memoria" class="hide">
	<div class="row-fluid div-table">
		<div class="span6 padding-left-10 desc"></div>
		<div class="span1 padding-left-10 quant"></div>
		<div class="span2 padding-left-10 vl-unit"></div>
		<div class="span2 padding-left-10 vl-tot"></div>
		<div class="span1" style="text-align:inherit;padding-top:initial"><button type="button" class="btn btn-mini btn-danger margin-top-4 js-remover-resultado"><i class="icon-remove icon-white"></i></button></div>
	</div>		
</div>

<input type="hidden" id="key" disabled="disabled" value="<?php echo ((isset($c))? $c : 0 ) ?>">
<input type="hidden" id="valor_total" disabled="disabled" value="<?php echo ((isset($valor_total))? $valor_total : 0) ?>">

<?php echo $this->Javascript->codeblock("
	$(document).ready(function() {
		setup_mascaras();
	});
	", false); ?>
	<?php $this->addScript($this->Buonny->link_js('itens_pedidos.js')); ?>