<style>
	.form-check.form-check-inline input {
    margin: 0px 8px 0 9px;
}
.form-check.form-check-inline {
    display: inline-block;
    margin-bottom: 15px;
}
</style>
<div class='well'>
	<?php echo $this->BForm->create('PedidoExame', array('method'=> 'post', 'type' => 'file', 'enctype' => 'multipart/form-data', 'url' => array('controller' => 'PedidosExames','action' => 'relatorio_faturamento_exportar'))); ?>

		<div class="row-fluid inline margin-top-15">
			<?php echo $this->Buonny->input_codigo_cliente_matriz($this); ?>
			<img src="/portal/img/default.gif" id="carregando" style="padding: 10px 0 0 10px; display: none;">
		</div>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('nome_cliente', array('label' => 'Nome Cliente', 'class' => 'input-xlarge', 'readonly' => true)); ?>
			<?php echo $this->Buonny->input_unidades_exames_faturar($this,"PedidoExame",$unidades); ?>	
		    <?php echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_pagador', 'label' => 'Código', 'name_display' => array('label' => 'Pagador'), 'checklogin' => false), 'PedidoExame'); ?>
		</div>
		<div class="row fluid">	
			<div class="span1" style="padding-top: 5px">
				<span class="label label-success">Periodo:</span>
			</div>
			<div class="span2" style="margin-left: 0%" >
				<?php echo $this->BForm->input('data_inicio', array('label' => false, 'place-holder' => 'Afastamento', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
			</div>
			<div class="span1" style="padding-top: 6px;margin-left: -4%">
				até
			</div>
			<div class="span2" style="margin-left: -3%" >
				<?php echo $this->BForm->input('data_fim', array('label' => false, 'place-holder' => 'Fim','type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?>
			</div>
		</div>
		<div class="row fluid">
			<div class="span12">
				<span class="label label-success">Selecione os campos que deverão ser exibidos no relatório: </span>
			</div>
		</div>
		<div class="row-fluid" style="margin-top: 7px;">
			<div class="span5">
				<?php echo $this->Form->input('from', array('label' => false, 'id' => 'multiselect', 'options' => $campos, 'class' => 'form-control', 'multiple' => true, 'size' => '8', 'style' => 'width: 100%')); ?>
			</div>

			<div class="span2">
				<button type="button" id="multiselect_rightAll" class="btn btn-block"><i class="icon-forward"></i></button>
				<button type="button" id="multiselect_rightSelected" class="btn btn-block"><i class="icon-chevron-right"></i></button>
				<button type="button" id="multiselect_leftSelected" class="btn btn-block"><i class="icon-chevron-left"></i></button>
				<button type="button" id="multiselect_leftAll" class="btn btn-block"><i class="icon-backward"></i></button>
			</div>

			<div class="span5">
				<?php echo $this->Form->input('to', array('label' => false, 'id' => 'multiselect_to', 'class' => 'form-control valida-campos', 'options' => array(), 'multiple' => true, 'size' => '8', 'style' => 'width: 100%')); ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<label class="margin-top-10"><strong>Exibição:</strong></label>
				<?php echo $this->Form->input('exibicao', array( 'value' => empty($this->data['PedidoExame']['exibicao']) ? 'excel' : $this->data['PedidoExame']['exibicao'], 'required' => true, 'legend' => false, 'type' => 'radio', 'options' => $visualizacao)); ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<label class="margin-top-10"><strong>Mostrar Exames com Prestador Particular ou Ambulatório?:</strong></label>
				<?php echo $this->Form->input('exibe_prestadores_particular_ambulatorio', array('value' => empty($this->data['PedidoExame']['exibe_prestadores_particular_ambulatorio']) ? 0 : $this->data['PedidoExame']['exibe_prestadores_particular_ambulatorio'], 'required' => true, 'legend' => false, 'type' => 'radio', 'options' => array('1' => 'Sim', '0' => 'Não'))); ?>
			</div>
		</div>
		<div class="row-fluid">
			<button class="btn btn-primary margin-top-10">Gerar relatório</button>	
			<button class="btn btn-danger margin-top-10" id="limpar-filtro">Limpar Filtros</button>	
		</div>
	<?php echo $this->Form->end(); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function($) {	
		setup_mascaras(); 
		setup_time(); 
		setup_datepicker();
		$("#multiselect").multiselectMulti();
	});

	$("#limpar-filtro").click(function(){
        $("#PedidoExameCodigoCliente").val("");                
        $("#PedidoExameNomeCliente").val("");                
        $("#PedidoExameCodigoClienteAlocacao").html("<option value=\'\'>Todos</option>");              
        $("#PedidoExameCodigoPagador").val("");                
        $("#PedidoExameCodigoPagadorName").val("");                
        $("#PedidoExameDataInicio").val("");                
        $("#PedidoExameDataFim").val("");                
        $("#PedidoExameExibicaoTela").removeAttr("checked");                
        $("#PedidoExameExibicaoExcel").removeAttr("checked");            
    });

    $(".btn-primary").click(function(event) {

    	var data_inicio = $("#PedidoExameDataInicio").val();
    	var data_fim = $("#PedidoExameDataFim").val();
    	var cliente_datafim = true;
		var cliente_datainicio = true;
		var cliente_diff_meses = true;

    	function gerarData(str) {
    		var partes = str.split("/");
    		return new Date(partes[2], partes[1] - 1, partes[0]);
		}		

		if($("input[name=\'data[PedidoExame][data_fim]\']").val().trim() == "") {
			cliente_datafim = false;
		}

		if($("input[name=\'data[PedidoExame][data_inicio]\']").val().trim() == "") {
			cliente_datainicio = false;
		}

		if ( gerarData(data_inicio) > gerarData(data_fim) ){
			cliente_datafim = false;
			cliente_datainicio = false;
    	}

    	var dataInicio = gerarData(data_inicio);
		var dataFim = gerarData(data_fim);
		var diffMilissegundos = dataFim - dataInicio;
		var diffSegundos = diffMilissegundos / 1000;
		var diffMinutos = diffSegundos / 60;
		var diffHoras = diffMinutos / 60;
		var diffDias = diffHoras / 24;
		var diffMeses = diffDias / 30;

		if (diffMeses > 24){
			cliente_diff_meses = false;
		}

    	if(!cliente_diff_meses) {
			if(!cliente_diff_meses){
				$("input[name=\'data[PedidoExame][data_fim]\']").css({borderColor: "red"});
				$("input[name=\'data[PedidoExame][data_inicio]\']").css({borderColor: "red"});
			}

			swal({
				type: "warning",
				title: "Atenção",
				text: "Período maior que 2 anos."
			});

			return false;
		}

		if(cliente_datafim && cliente_datainicio) {
		} else {
			if(!cliente_datafim){
				$("input[name=\'data[PedidoExame][data_fim]\']").css({borderColor: "red"});
			}

			if(!cliente_datainicio){
				$("input[name=\'data[PedidoExame][data_inicio]\']").css({borderColor: "red"});
			}

			swal({
				type: "warning",
				title: "Atenção",
				text: "Há campos obrigatórios que não foram preenchidos, Senão for isso, pode ser que a Data inicial esta maior que a Data Final. Por favor verifique!"
			});

			return false;
		}
	});

	$("body").on("keydown", "input, select, textarea", function(e) {
	    var self = $(this)
	      , form = self.parents("form:eq(0)")
	      , focusable
	      , next
	      ;
	    if (e.keyCode == 13) {
	        focusable = form.find("input,a,select,button,textarea").filter(":visible");
	        next = focusable.eq(focusable.index(this)+1);
	        if (next.length) {
	            next.focus();
	        } else {
	            form.submit();
	        }
	        return false;
	    }
	});
'); ?>

<?php $this->addScript($this->Javascript->codeBlock("

	jQuery('#PedidoExameCodigoCliente').change(function() {
		carregaNomeCliente();
	});

	function carregaNomeCliente() {
		var codigo_cliente = $('#PedidoExameCodigoCliente').val();		
		$.ajax({
	        type: 'POST',
	        url: baseUrl + 'pedidos_exames/carrega_nome_cliente/' + codigo_cliente,
	        dataType: 'json',
	        beforeSend: function() {
	        	$('#carregando').show();
	        },
	        success: function(data) {
	        	$('#carregando').hide();
	        	if(data.Cliente) {
	        		$('input[name=\"data[PedidoExame][nome_cliente]\"]').val(data.Cliente.nome_fantasia);
	        		$('#carregando').hide();
	        	} else {
	        		$('#carregando').hide();
	        		swal({
						type: 'warning',
						title: 'Atenção',
						text: 'Cliente não encontrado.'
					});
					$('input[name=\"data[PedidoExame][nome_cliente]\"]').val('');
	        	}
	        },
	        error: function(erro){
				$('#carregando').hide();
				swal({
					type: 'warning',
					title: 'Atenção',
					text: 'Cliente não encontrado.'
				});
				$('input[name=\"data[PedidoExame][nome_cliente]\"]').val('');
			}
	    });
	}
")) ?>