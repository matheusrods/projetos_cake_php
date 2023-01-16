<?php echo $this->BForm->create('ClienteProdutoPagador', array('url' => array('controller' => 'clientes_produtos_pagadores','action' => 'editar')));?>
	<br>
	<?php echo $this->BForm->hidden('codigo_embarcador_transportador',array('value' => $embarcador_transportador['EmbarcadorTransportador']['codigo'])) ?>
	<div class='row-fluid inline parent'>
		<?php echo $this->BForm->input('codigo_cliente_embarcador', array('class' => 'input-mini',  'label'=> 'Embarcador', 'readonly' => true, 'value' => $embarcador_transportador['ClienteEmbarcador']['codigo'] )) ?>
		<?php echo $this->BForm->input('nome_embarcador',array('class' => 'input-xlarge name', 'readonly' => true, 'value' => $embarcador_transportador['ClienteEmbarcador']['razao_social'])) ?>
	</div>
	<div class='row-fluid inline parent'>
		<?php echo $this->BForm->input('codigo_cliente_transportador', array('class' => 'input-mini',  'label'=> 'Transportador', 'readonly' => true, 'value' => $embarcador_transportador['ClienteTransportador']['codigo'] )) ?>
		<?php echo $this->BForm->input('nome_transportador',array('class' => 'input-xlarge name', 'readonly' => true, 'value' => $embarcador_transportador['ClienteTransportador']['razao_social'])) ?>
	</div>
	<div class='row-fluid inline parent'>
		<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_pagador', 'Pagador',true,'ClienteProdutoPagador') ?>
		<?php echo $this->BForm->input('nome_pagador',array('class' => 'input-xlarge name', 'readonly' => true)) ?>
	</div>

	<div class='row-fluid inline parent'>
		<?php echo $this->BForm->input('codigo_produto',array('class' => 'input-xlarge', 'options' => $produto,  'empty' => 'Todos Produtos', 'label' => 'Produto')) ?>
	</div>
	
	<div class="form-actions">
		  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		  <?php echo $html->link('Voltar',array('controller' => 'EmbarcadoresTransportadores', 'action' => 'index'), array('class' => 'btn')) ;?>
	</div>


<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){

		$("input.input-mini").blur(function(){
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