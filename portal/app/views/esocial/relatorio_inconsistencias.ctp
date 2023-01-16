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
	<?php echo $this->BForm->create('Esocial', array('method'=> 'post', 'type' => 'file', 'enctype' => 'multipart/form-data', 'url' => array('controller' => 'esocial','action' => 'relatorio_inconsistencias_exportar'))); ?>

		<div class="row-fluid inline margin-top-15">
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'Esocial'); ?>
			<?php echo $this->BForm->input('codigo_cliente_alocacao', array('label' => 'Unidades', 'class' => 'input-xlarge','options' => $unidades, 'empty' => 'Selecione a Unidade'));  ?>
			<?php echo $this->BForm->input('codigo_setor', array('label' => 'Setor', 'class' => 'input-xlarge','options' => $setores, 'empty' => 'Selecione o Setor')); ?>
			<div class="span1" style="padding-top: 31px;margin-left: 2px;">
				<span class="label label-success">Periodo:</span>
			</div>
			<div class="span2" style="margin-left: -1%;margin-top: 24px;" >
				<?php echo $this->BForm->input('data_inicio', array('label' => false, 'place-holder' => 'Afastamento', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
			</div>
			<div class="span1" style="margin-left: -5%;margin-top: 29px;">
				até
			</div>
			<div class="span2" style="margin-left: -4%;margin-top: 24px;" >
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
				<?php echo $this->Form->input('from', array('label' => false, 'id' => 'multiselect', 'options' => (array)$campos, 'class' => 'form-control', 'multiple' => true, 'size' => '8', 'style' => 'width: 100%')); ?>
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
	<?php echo $this->Form->end(); ?>
	
	<div class="row-fluid">
		<button class="btn btn-primary margin-top-10" id="submit_relatorio">Gerar relatório</button>	
		<button class="btn btn-danger margin-top-10" id="limpar-filtro">Limpar Filtros</button>	
	</div>
</div>

<script type="text/javascript">

	jQuery(document).ready(function($) {	
		setup_mascaras(); 
		setup_time(); 
		setup_datepicker();
		$("#multiselect").multiselectMulti();
	});

	$("#limpar-filtro").click(function(){
        $("#EsocialCodigoCliente").val("");                    
        $("#EsocialCodigoClienteAlocacao").html("<option value=''>Selecione a Unidade</option>");                         
        $("#EsocialCodigoSetor").html("<option value=''>Selecione o Setor</option>");                         
        $("#EsocialDataInicio").val("");                
        $("#EsocialDataFim").val("");
    });

    $("#submit_relatorio").click(function(event) {

    	var data_inicio = $("#EsocialDataInicio").val();
    	var data_fim = $("#EsocialDataFim").val();
    	var codigo_cliente = $("#EsocialCodigoCliente").val();
    	var cliente_datafim = true;
		var cliente_datainicio = true;
		var cliente_diff_meses = true;
		var contar_multiselect = $('#multiselect_to option').size();

		var retorno = true;

    	function gerarData(str) {
    		var partes = str.split("/");
    		return new Date(partes[2], partes[1] - 1, partes[0]);
		}		

		if($("input[name='data[Esocial][data_fim]']").val().trim() == "") {
			cliente_datafim = false;
		}

		if($("input[name='data[Esocial][data_inicio]']").val().trim() == "") {
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

		if (diffMeses > 12){
			cliente_diff_meses = false;
		}

    	if(!cliente_diff_meses) {
			if(!cliente_diff_meses){
				$("input[name='data[Esocial][data_fim]']").css({borderColor: "red"});
				$("input[name='data[Esocial][data_inicio]']").css({borderColor: "red"});
			}

			swal({
				type: "warning",
				title: "Atenção",
				text: "Período maior que 1 ano."
			});

			retorno = false;
		}

		if(!cliente_datafim && !cliente_datainicio) {

			if(!cliente_datafim){
				$("input[name='data[Esocial][data_fim]']").css({borderColor: "red"});
			}

			if(!cliente_datainicio){
				$("input[name='data[Esocial][data_inicio]']").css({borderColor: "red"});
			}

			swal({
				type: "warning",
				title: "Atenção",
				text: "Há campos obrigatórios que não foram preenchidos, Senão for isso, pode ser que a Data inicial esta maior que a Data Final. Por favor verifique!"
			});

			retorno = false;
		} else if (codigo_cliente == ""){
			swal({
				type: "warning",
				title: "Atenção",
				text: "Informe o Código do cliente."
			});
			$("input[name='data[Esocial][codigo_cliente]']").css({borderColor: "red"});
			retorno = false;
		} else if(contar_multiselect == 0){
			swal({
				type: "warning",
				title: "Atenção",
				text: "Favor selecionar um campo para apresentar."
			});
			$("input[name='data[Esocial][to][]']").css({borderColor: "red"});
			$('#multiselect_to').focus();
			retorno = false;
		}

		if(retorno == true){
			$("#EsocialRelatorioInconsistenciasForm").submit();
		}
	});
</script>