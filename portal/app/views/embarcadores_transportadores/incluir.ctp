<?php echo $this->BForm->create('EmbarcadorTransportador', array('url' => array('controller' => 'EmbarcadoresTransportadores','action' => 'incluir', isset($this->passedArgs[0])?$this->passedArgs[0]:null,isset($this->passedArgs[1])?$this->passedArgs[1]:null )));?>
	<br />
	<?php echo $this->BForm->hidden('ClienteProdutoPagador.codigo_embarcador_transportador',array('value' => isset($this->passedArgs[0])?$this->passedArgs[0]:null)) ?>
	<?php echo $this->BForm->hidden('ClienteProdutoPagador.codigo',array('value' => isset($this->passedArgs[1])?$this->passedArgs[1]:null)) ?>


	<div class='row-fluid inline parent'>

		<?php if (!isset($this->passedArgs[0])): ?>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_embarcador', 'Embarcador',true,'EmbarcadorTransportador') ?>
		<?php else: ?>
		<?php echo $this->BForm->input('codigo_cliente_embarcador', array('class' => 'input-mini', 'readonly' => true, 'label' => 'Embarcador', 'value' => isset($embarcador_transportador['ClienteEmbarcador'])?$embarcador_transportador['ClienteEmbarcador']['codigo']:null)) ?>
		<?php endif; ?>
		<?php echo $this->BForm->input('nome_embarcador',array('class' => 'input-xlarge name', 'readonly' => true, 'value' => isset($embarcador_transportador['ClienteEmbarcador'])?$embarcador_transportador['ClienteEmbarcador']['razao_social']:null)) ?>

	</div>
	<div class='row-fluid inline parent'>

		<?php if (!isset($this->passedArgs[0])): ?>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_transportador', 'Transportador',true,'EmbarcadorTransportador') ?>
		<?php else: ?>
		<?php echo $this->BForm->input('codigo_cliente_transportador', array('class' => 'input-mini', 'readonly' => true, 'label' => 'Transportador', 'value' => $embarcador_transportador['ClienteTransportador']['codigo'])) ?>
		<?php endif; ?>
		<?php echo $this->BForm->input('nome_transportador',array('class' => 'input-xlarge name', 'readonly' => true, 'value' => isset($embarcador_transportador['ClienteTransportador'])?$embarcador_transportador['ClienteTransportador']['razao_social']:null)) ?>

	</div>
	<div class="pagador-produto">
		<div class='row-fluid inline parent'>

			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_pagador', 'Pagador',true,'ClienteProdutoPagador',isset($cliente_produto_pagador['ClienteProdutoPagador']['codigo_cliente_pagador'])?$cliente_produto_pagador['ClienteProdutoPagador']['codigo_cliente_pagador']:null) ?>
			<?php echo $this->BForm->input('nome_pagador',array('class' => 'input-xlarge name', 'readonly' => true, 'value' => isset($cliente_produto_pagador['ClientePagador']['razao_social'])?$cliente_produto_pagador['ClientePagador']['razao_social']:null)) ?>
		</div>
		
		<div class='row-fluid inline parent'>
			<?php if (!isset($this->passedArgs[1])): ?>
			<?php echo $this->BForm->input('ClienteProdutoPagador.codigo_produto',array('class' => 'input-xlarge', 'label' => 'Produto', 'options' => $produtos ,'empty' => 'Todos Produtos', 'readonly'=> (isset($this->passedArgs[1])), 'value' => isset($cliente_produto_pagador['Produto']['codigo'])?$cliente_produto_pagador['Produto']['codigo']:null )) ?>
			<?php else: ?>
			<?php echo $this->BForm->input('Descricao', array('class' => 'input-xlarge', 'readonly' => true, 'label' => 'Produto', 'value' => $cliente_produto_pagador['Produto']['descricao'])) ?>
			<?php endif; ?>
			
		</div>
	</div>
	
	<div class="form-actions">
		  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		  <?php echo $html->link('Voltar',array('controller' => 'EmbarcadoresTransportadores', 'action' => 'index'), array('class' => 'btn')) ;?>
	</div>

	<?php if($mensagem): ?>
	<div class="form-actions alert-error veiculo-error" >
		<h5>Erros:</h5>
		<?php echo $mensagem ?>
	</div>
	<?php endif; ?>

<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('

	jQuery(document).ready(function()
	{	$("input.input-mini").blur(function(){
			carregar($(this));
		});		
		function carregar(obj){
			var field_name = obj.parents("div.parent:eq(0)").find("input.name");
			if(obj.val()){
				$.ajax({
					url: baseUrl + "Clientes/buscar/" +	obj.val() + "/"+  Math.random(),
					dataType: "json",
					success: function(data){
						field_name.val(data.dados.razao_social);
					}
				});
			} else {
				field_name.val("");
			}
		}
		
	});', false);