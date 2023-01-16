<div class='well'>

	<div id='filtros'>
		<?php echo $bajax->form('Recebsm', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Recebsm', 'element_name' => 'inicio_viagem'), 'divupdate' => '.form-procurar')) ?>
			<div class='row-fluid inline'>
				<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true,'Recebsm') ?>
				<?php echo $this->BForm->input('pedido_cliente', 
					array('class' => 'input-small', 'label' => 'Pedido Cliente', 'placeholder' => 'Pedido Cliente',
	  					  'after'=>$this->Html->link('<i class="icon-search"></i>', 'javascript:void(0)', array('escape' => false, 'title' => 'Procurar placa por pedido', 'onclick'=>'javascript: consulta_placa_pedido();')).
	  					  "&nbsp;<img src=\"/portal/img/loading.gif\" style=\"display:none;\" id=\"RecebsmLoading\" />"
					)
				) ?>
				<?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'label' => 'Placa Veículo', 'placeholder' => 'Placa')) ?>
				<?php echo $this->Buonny->input_validade_checklist($this, $regras_aceite_sm, 'Recebsm','racs_validade_checklist','Regra Aceite SM','Selecione',true) ?>
				<?php echo $this->BForm->input('checklist_dias_validos', array('label' => 'Qtd. Dias Regra','type' => 'text','class' => 'input-small numeric just-number', 'placeholder' => 'dias', 'readonly'=>true)) ?>
			</div>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
				<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
			</div>
			<?php echo $this->BForm->hidden('data_hora',array('value'=>date('Y-m-d H:i:s'))); ?>
			<?php echo $this->BForm->hidden('data_inicial',array('value'=>date('Y-m-d'))); ?>
			<?php echo $this->BForm->hidden('data_final',array('value'=>date('Y-m-d'))); ?>
		<?php echo $this->BForm->end() ?>
	</div>
	
</div>
<?php echo $this->Javascript->codeBlock('
	$("#RecebsmCodigoCliente").blur();
	function consulta_placa_pedido() {
		var pedido = $("#RecebsmPedidoCliente").val();
		var codigo_cliente = $("#RecebsmCodigoCliente").val();

		$(".help-block").remove();
		$("#RecebsmPedidoCliente").removeClass("form-error");
		$("#RecebsmPedidoCliente").parents().find(".error").removeClass("error");			

		if (pedido=="") {
			$("#RecebsmPlaca").val("");
			return false;
		}
		if (codigo_cliente=="") {
			$("#RecebsmPlaca").val("");
			return false;
		}	
		$.ajax({
			url: baseUrl + "viagens/retorna_placa_pedido/" + codigo_cliente + "/" + pedido + "/" + Math.random(),
			dataType: "json",
			beforeSend: function(){
				$(".help-block").remove();
				$("#RecebsmPedidoCliente").removeClass("form-error");
				$("#RecebsmPedidoCliente").parents().find(".error").removeClass("error");	
				$("#RecebsmLoading").show();
			},					
			success: function(data){
				$("#RecebsmLoading").hide();
				if(data){
					$("#RecebsmPlaca").val(data.veic_placa);
				} else {
					$("#RecebsmPedidoCliente").addClass("form-error").parent().addClass("error").append("<div id=\"lbl-error\" class=\"help-block\">Pedido não encontrado</div>");
				}
			},
		});
	}

	$(document).ready(function(){
		$("#RecebsmCodigoCliente").change();
		setup_mascaras();

		var codigo_cliente = $("#RecebsmCodigoCliente").val();
		var placa = $("#RecebsmPlaca").val();
		
		if (codigo_cliente!="" && placa!="") inicioViagem();
		
		$("#limpar-filtro").click(function(){	
			bloquearDiv($(".form-procurar"));
			$(".form-procurar").load(baseUrl + "/filtros/limpar/model:Recebsm/element_name:inicio_viagem/" + Math.random());
		});

		$("#RecebsmCodigoCliente").change(function() {
			$("#RecebsmPedidoCliente").val("");
			$("#RecebsmPlaca").val("");
		});

		$("#RecebsmRacsValidadeChecklist").change();
		$("#RecebsmPedidoCliente").blur(function(){
			consulta_placa_pedido();
		});	
	});', false);
?>
