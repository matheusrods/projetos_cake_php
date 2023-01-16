<?php echo $this->BForm->create('MatrizFilial', array('url' => array('controller' => 'MatrizesFiliais','action' => 'incluir', isset($this->passedArgs[0])?$this->passedArgs[0]:null)));?>
	<div class='row-fluid inline parent'>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_matriz', 'Matriz',true,'MatrizFilial') ?>
		<?php echo $this->BForm->input('nome_matriz',array('class' => 'input-xlarge name', 'readonly' => true)) ?>

	</div>
	<div class='row-fluid inline parent'>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_filial', 'Filial',true,'MatrizFilial') ?>
		<?php echo $this->BForm->input('nome_filial',array('class' => 'input-xlarge name', 'readonly' => true)) ?>

	</div>
	<div class='row-fluid inline parent'>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_pagador', 'Pagador',true,'MatrizProdutoPagador') ?>
		<?php echo $this->BForm->input('nome_pagador',array('class' => 'input-xlarge name', 'readonly' => true)) ?>
	</div>
	<div class='row-fluid inline parent'>
		<?php echo $this->BForm->input('MatrizProdutoPagador.codigo_produto',array('class' => 'input-xlarge', 'label' => 'Produto', 'options' => $produtos ,'empty' => 'Todos Produtos')) ?>
	</div>
	<div class="form-actions">
		  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		  <?php echo $html->link('Voltar',array('controller' => 'MatrizesFiliais', 'action' => 'index'), array('class' => 'btn')) ;?>
	</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
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
	jQuery(document).ready(function(){
		$("input[id*=CodigoCliente]").each(function(){
			carregar($(this));
		});
		$("input[id*=CodigoCliente]").blur(function(){
			carregar($(this));
		});
	});', false);