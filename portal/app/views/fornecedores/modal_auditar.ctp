<style>
label{
	font-weight: bold;
}
</style>

	<div>
		
		<div class="row-fluid inline" style="margin-bottom:5px;">
			<div class="span4">
			    <label>Código Pedido:</label>
				<?php $codigo_pedido_exame = $dados[0]['codigo_pedido_exame'];
				echo $dados[0]['codigo_pedido_exame']; ?>
			</div>
			<div class="span4">
				<label>Recebimento Físico:</label> <!-- Perguntar se físico é baseado na ficha clinica -->
				<?= $this->BForm->input('recebimento_fisico', array('disabled'=>true,'value'=> !empty($dados[0]['codigo_anexo_ficha_clinica']) ? 1 : 0,'type' => 'radio', 'options' => array('0' => 'Não', '1' => 'Sim'), 'legend' => false, 'title' => 'Físico', 'label' => array('value'=>"Físico", 'class' => 'radio inline input-xsmall'))) ?>
			</div>
			<div class="span4">
			<label>Eletrônico:</label>
				<?= $this->BForm->input('recebimento_eletronico', array('disabled'=>true, 'value'=> !empty($dados[0]['codigo_anexo_exame']) ? 1 : 0,'type' => 'radio', 'options' => array('0' => 'Não', '1' => 'Sim'), 'legend' => false, 'title' => 'Eletrônico', 'label' => array('value'=>"Eletrônico",'class' => 'radio inline input-xsmall'))) ?>
			</div>

		</div>
		
		<div class="form-group  col-md-12">
			<label>Exame:</label>
			<?php echo $dados[0]['exame']; ?>
		</div>
		<br />

		<div class="row-fluid inline">
			<div class="span6">
				<label>Data Baixa:</label>
				<?php echo AppModel::dbDateToDate($dados[0]['data_baixa']); ?>
			</div>
			<div class="span6">
				<label>Valor:</label>
				<?php echo $this->Buonny->moeda($dados[0]['valor']); ?>
			</div>
		</div>

		<div class="form-group  col-md-12">
			<label>Status:</label>
			<?php echo $this->BForm->input('AuditoriaExames.codigo_status_auditoria_exames', array('label' => false, 'class' => 'input-medium', 'options' => $status_auditoria, 'type' => 'select', 'default' => 1, 'value' => $dados[0]['codigo_status_auditoria'])) ?>
		</div>			
		<br />
		<div class="motivo-obrigatoria hidden">

		
			<!-- 
				Tipos de Glosas
				Descrição
				Data da glosa
				Data de vencimento
				Data de Pagamento
				Status
				Valor -->


			


			<div class="row-fluid inline">
				<div class="span4">
					<?= $this->BForm->input('data_vencimento', array('value' => $glosas['data_vencimento'], 'label' => 'Data Vencimento', 'placeholder' => '', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
				</div>
				<div class="span4">
					<?= $this->BForm->input('data_pagamento', array('value' => $glosas['data_pagamento'],'label' => 'Data Pagamento', 'placeholder' => '', 'type' => 'text', 'class' => 'datepicker data date input-small form-control', 'multiple')); ?> 
				</div>
				<div class="span4">
					<?= $this->BForm->input('valor', array('value' => $glosas['valor'],'label' => 'Valor (R$)', 'placeholder' => '', 'type' => 'text', 'class' => 'input-small numeric moeda form-control', 'multiple')); ?> 
				</div>
			</div>

		</div>
	</div>



<?php echo $this->Javascript->codeBlock('

$(document).ready(function() {
	setup_mascaras();
	setup_datepicker();
	setup_time();

	var codigoStatus = $("#AuditoriaExamesCodigoStatusAuditoriaExames");
	
	selecaoPagamentoBloqueado(codigoStatus.val() == 2);

	codigoStatus.change(function(){
		
		selecaoPagamentoBloqueado(codigoStatus.val() == 2);

	});

	function selecaoPagamentoBloqueado( situacao )
	{
		
		var classFieldArr = $(".motivo-obrigatoria");
        
        $.each(classFieldArr, function(index, field){ 
			if(situacao){
				$(field).removeClass("hidden");
			} else {
				$(field).addClass("hidden");
			}
		});

	}

	var botaoOk = $("#ItemPedidoExameBaixaOk");
	botaoOk.click(function(event) {
		event.preventDefault();
		return salvar_realizacao('.$codigo_item_pedido_exame.', '.$codigo_pedido_exame.');
  	});

	
});


function salvar_realizacao(codigo_item_pedido, codigo_pedido_exame) {

	//pega a data
	var status		  = $("#AuditoriaExamesCodigoStatusAuditoriaExames").val();
	var motivo		  = $("#AuditoriaExamesMotivo").val();
	var data_glosa		     = $("#data_glosa").val();
	var codigo_tipo_glosa	 = $("#codigo_tipo_glosa").val();
	var data_vencimento		 = $("#data_vencimento").val();
	var data_pagamento		 = $("#data_pagamento").val();
	var valor		         = $("#valor").val();
	
	//item bloqueado
	if(status == 2){
		
		// verifica se o motivo esta em branco
		if (!motivo || motivo.trim() == ""){
			$( "#AuditoriaExamesMotivo" ).focus();
			mensagem("Preencha com o Motivo do bloqueio do pagamento.");
			return;
		}

		if (!data_glosa || data_glosa.trim() == ""){
			mensagem("Preencha com uma data da glosa.");
			return;
		}

		if (!valor || valor.trim() == ""){
			mensagem("Preencha com um valor da glosa.");
			return;
		}
	}//fim bloqueado

	var div = jQuery("#modal_data");
    bloquearDiv(div);
	
	//envia via ajax a data de realizacao
	$.ajax({
		url: baseUrl + "fornecedores/salvar_auditoria",
		type: "POST",
		dataType: "json",
		async: false,
		data: {
			"codigo_item_pedido" : codigo_item_pedido,
			"codigo_pedido_exame": codigo_pedido_exame,
			"status"			 : status,
			"motivo"			 : motivo,
			"data_glosa"         : data_glosa,
			"codigo_tipo_glosa"  : codigo_tipo_glosa,
			"data_vencimento"    : data_vencimento,
			"data_pagamento"     : data_pagamento,
			"valor"              : valor
		}

	})
	.done(function(data) {

		desbloquearDiv(div);

		if(data.retorno == false) {

			// mensagem(data.mensagem);
			alert(data.mensagem);
		} else {
			
			// mensagem(data.mensagem, "success", "Sucesso");
			alert(data.mensagem);
			auditar(codigo_item_pedido, 0);
		
			atualizaListaAE();
		}

	})
	.fail(function() {
		alert("Houve uma falha no processo, por favor tente novamente.");
		// mensagem("Houve uma falha no processo, por favor tente novamente.", "error", "Erro");
	});


}//fim function salvar_realizacao

 function atualizaListaAE() {
    //verifica se existe algum codigo para pesquisar
    var div = jQuery("div.lista");
    bloquearDiv(div);
    div.load(baseUrl + "fornecedores/auditoria_exames_listagem/"+ Math.random());
}

'); ?>  