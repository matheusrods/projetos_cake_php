<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Editar - Dados da Realização do Exame</h3>
		</div>

		<div class="modal-body" style="min-height: 295px;max-height: 360px;">

			<div style="float: left;width: 200px;">
				<span style="font-size: 1.2em">
					<b>Código Pedido:</b>
					<?php echo $pedido['PedidoExame']['codigo']; ?>
				</span>
			</div>
			
			<div>
				<span style="font-size: 1.2em">
					<b>Código Item:</b>
					<?php echo $pedido['ItemPedidoExame']['codigo']; ?>
				</span>
			</div>
			<br /><br />

			<div>
				<span style="font-size: 1.2em">
					<b>Cliente:</b><br />
					<?php echo $pedido['Cliente']['razao_social']; ?>
				</span>
			</div>
			<br />

			<?php 
			$Configuracao = &ClassRegistry::init('Configuracao');
			if($pedido['Exame']['codigo'] == $Configuracao->getChave('INSERE_EXAME_CLINICO')): ?>

				<hr>

				<div style="float:left;width: 200px;">
					<span style="font-size: 1.2em">
						<b>Exame:</b><br />
						<?php echo $pedido['Exame']['descricao']; ?>
					</span>
				</div>

				<div>
					<span style="font-size: 1.2em">
						<b>Aptidão</b>
						<?php echo $this->BForm->input('FichaClinica.parecer', array('label' => false, 'class' => 'input-small parecer', 'options' => array('1' => 'Apto', '0' => 'Inapto'), 'type' => 'select', 'default' => 1, 'value' => $pedido['FichaClinica']['parecer'])) ?>
					</span>
				</div>
				<div>
					<span style="font-size: 1.2em">
						<b>Médico</b>
						<?php echo $this->BForm->input('FichaClinica.codigo_medico', array('label' => false, 'class' => 'input-large medico', 'options' => $medicos, 'type' => 'select', 'value' => $pedido['FichaClinica']['codigo_medico'])) ?>
					</span>
				</div>

				<hr>

			<?php else: ?>

				<div>
					<span style="font-size: 1.2em">
						<b>Exame:</b><br />
						<?php echo $pedido['Exame']['descricao']; ?>
					</span>
				</div>

				<hr>

			<?php endif; ?>
			<br />
			
			<div style="float:left;width:200px;">
				<span style="font-size: 1.2em">
					<b>Data de Atendimento</b>
					<?php echo $this->BForm->input('ItemPedidoExame.data_realizacao_exame', array('type' => 'text', 'label' => '', 'class' => 'data data-exame input-small', 'value' => AppModel::dbDateToDate($pedido['ItemPedidoExame']['data_realizacao_exame']))); ?>
				</span>
			</div>

			<div>
				<span style="font-size: 1.2em">
					<b>Comparecimento:</b>
					<br />
					<?php echo $this->BForm->input('ItemPedidoExame.compareceu', array('type' => 'radio', 'options' => array('1' => 'Sim','0' => 'Não'), 'class' => 'compareceu-exame', 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall comparecimento'), 'value' => $pedido['ItemPedidoExame']['compareceu'])) ?>
				</span>
			</div>
			<br />

			<hr>

			<div style="float:left;width:200px;">
				<span style="font-size: 1.2em">
					<b>Data conclusão de Exame</b>
					<?php echo $this->BForm->input('ItemPedidoExameBaixa.data_realizacao_exame', array('type' => 'text', 'label' => '', 'class' => 'data data-exame input-small', 'value' => AppModel::dbDateToDate($pedido['ItemPedidoExameBaixa']['data_realizacao_exame']))); ?>
				</span>
			</div>
			<br />

		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="editar_realizacao_datas(<?php echo $codigo_item_pedido; ?>, 0);"class="btn btn-danger">FECHAR</a>
				<a id="ItemPedidoExameBaixaOk" href="javascript:void(0);" class="btn btn-success">SALVAR</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	setup_mascaras();
	setup_datepicker();
	setup_time();

	$(function() {
		var check1 = 0;
		$("input:radio[name='data[ItemPedidoExame][compareceu]']").click(function() {
	        if ($('#ItemPedidoExameCompareceu1').is(':checked')) {
	            if(check1 != 0) { 
	                $(this).prop('checked', false);
	                check1 = 0;
	            } else {
	                check1 = 1;
	            }
	        }

	        if ($('#ItemPedidoExameCompareceu0').is(':checked')) {
	            if(check1 != 0) { 
	                $(this).prop('checked', false);
	                check1 = 0;
	            } else {
	                check1 = 1;
	            }
	        }
		});
	});

	if($('#ItemPedidoExameBaixaResultado').val() == 2){
		$('#descricao_obrigatoria').show();
	}

	$('#ItemPedidoExameBaixaResultado').change(function(){
		if($('#ItemPedidoExameBaixaResultado').val() == 2){
			$('#descricao_obrigatoria').show();
		} else {
			$('#descricao_obrigatoria').hide();
		}
	});

  	var habilitarBotao = ( $('#ItemPedidoExameCompareceu0').is(":checked") || $('#ItemPedidoExameCompareceu1').is(":checked") );
	
	var botaoOk = $('#ItemPedidoExameBaixaOk');
	
	if( false == habilitarBotao ){
			botaoOk.addClass( "disabled" );
	}

	$('input[type=radio]').change(function() {       
		botaoOk.removeClass( "disabled" );
		habilitarBotao = true;
  	});

	botaoOk.click(function() {
		if( true == habilitarBotao ){
				salvar_realizacao(<?php echo $codigo_item_pedido; ?>);
		}
  	});


	//ao selecionar o comparecimento
	$('.comparecimento').click(function(){
		nao_comparecimento();
	});

	nao_comparecimento();


});

function nao_comparecimento() {
	if( $('#ItemPedidoExameCompareceu0').is(":checked") ){

		$("#FichaClinicaParecer option[value='branco']").remove();
		$("#FichaClinicaCodigoMedico option[value='branco']").remove();

		//aplicar a regra para o não comparecimento pc-40
		$('#FichaClinicaParecer').append('<option value="branco" selected="selected"> </option>');
		$('#FichaClinicaCodigoMedico').append('<option value="branco" selected="selected"> </option>');
		$('#ItemPedidoExameDataRealizacaoExame').val("");
		$('#ItemPedidoExameBaixaDataRealizacaoExame').val("");

		$("#ItemPedidoExameBaixaResultado").val( $('option:contains("Selecione")').val() );
		// $('#ItemPedidoExameBaixaDescricao').val("");
		
		$('#FichaClinicaParecer').prop('disabled', 'disabled');
		$('#FichaClinicaCodigoMedico').prop('disabled', 'disabled');
		$('#ItemPedidoExameDataRealizacaoExame').prop('disabled', 'disabled');
		$('#ItemPedidoExameBaixaDataRealizacaoExame').prop('disabled', 'disabled');

		$(".data-exame").datepicker("disable");
	} 
	else if( $('#ItemPedidoExameCompareceu1').is(":checked") ){

		$("#FichaClinicaParecer option[value='branco']").remove();
		$("#FichaClinicaCodigoMedico option[value='branco']").remove();

		//habilita os campos novamente
		$('#FichaClinicaParecer').prop('disabled', false);
		$('#FichaClinicaCodigoMedico').prop('disabled', false);
		$('#ItemPedidoExameDataRealizacaoExame').prop('disabled', false);
		$('#ItemPedidoExameBaixaDataRealizacaoExame').prop('disabled', false);
		$(".data-exame").datepicker("enable");
		
	}
}


function parseStringToDate(str) {
	var mdy = str.split('/');
	return new Date(mdy[2], mdy[1] - 1, mdy[0]);
}

var mensagem = function(mensagem, tipo, titulo){
	
	this.tipo = tipo || 'warning'
	this.titulo = titulo || 'Atenção'

	
		return swal({
			type: this.tipo,
			title: this.titulo,
			text: mensagem
		});
	
}

function isValidDate(d) {
  return d instanceof Date && !isNaN(d);
}

function salvar_realizacao(codigo_item_pedido) {

	//pega a data
	var parecer				  = $('#FichaClinicaParecer').val();
	var codigo_medico		  = $('#FichaClinicaCodigoMedico').val();
	var data_realizacao_exame = $('#ItemPedidoExameDataRealizacaoExame').val();
	var data_resultado_exame  = $('#ItemPedidoExameBaixaDataRealizacaoExame').val();
	// var resultado_do_exame    = $('#ItemPedidoExameBaixaResultado').val();
	// var descricao_alteracao   = $('#ItemPedidoExameBaixaDescricao').val();

	var compareceu 			  = '';
	if( $('#ItemPedidoExameCompareceu0').is(":checked") ){
		compareceu = 0; // nao
	} else if( $('#ItemPedidoExameCompareceu1').is(":checked") ){
		compareceu = 1; // sim
	}

	// se ItemPedidoExameCompareceu0 e ItemPedidoExameCompareceu1 não estiver selecionado
	// 		desabilita botão de salvar
  // se ItemPedidoExameCompareceu1 for selecionado
	// 		obriga a digitação de data de realização
	//		permite data de baixa vazio
	
  // se ItemPedidoExameCompareceu0 for selecionado
	// 		não obriga preencher as datas

	var hoje = new Date();
	// formata data dd/mm/yy removendo o tempo h:m:s
	var hoje_ddmmaaaa = new Date(hoje.getFullYear(), hoje.getMonth(), hoje.getDate(), 0, 0, 0 );

	// Se comparecimento = Sim
	if (compareceu == 1)
	{
			// verifica se data realizacao é válida
			if (data_realizacao_exame == ""){
				$( "#ItemPedidoExameDataRealizacaoExame" ).focus();
				new mensagem('Preencha o campo Data Realização com uma Data válida.');
				return;
			}
  }
	// se a data realização foi preenchido
	if (data_realizacao_exame!= '') 
	{
			var data_realizacao_exame_parsed = parseStringToDate(data_realizacao_exame);
			
			if (!isValidDate(data_realizacao_exame_parsed)){
				$( "#ItemPedidoExameDataRealizacaoExame" ).focus();
				new mensagem('Preencha o campo Data Realização com uma Data válida.');
				return;
			}
			// valida se data realização é maior que hoje
			if(data_realizacao_exame_parsed.getTime() > hoje_ddmmaaaa.getTime()){
				$( "#ItemPedidoExameDataRealizacaoExame" ).focus();
				new mensagem('Data de Realização não pode ser maior que hoje.');
				return;
			}
	}

  // se data resultado foi preenchido
	if (data_resultado_exame != '') 
	{
			var data_resultado_exame_parsed = parseStringToDate(data_resultado_exame);

			if (!isValidDate(data_resultado_exame_parsed)){
				$( "#ItemPedidoExameBaixaDataRealizacaoExame" ).focus();
				new mensagem('Preencha o campo Data Resultado com uma Data válida.');
				return;
			}

			// verifica se data resultado é maior que hoje
			if(data_resultado_exame_parsed.getTime() > hoje_ddmmaaaa.getTime()){
				$( "#ItemPedidoExameBaixaDataRealizacaoExame" ).focus();
				new mensagem('Data Resultado não pode ser maior que hoje.');
				return;
			}

			// verifica se data realização é maior que data resultado
			if(data_realizacao_exame_parsed.getTime() > data_resultado_exame_parsed.getTime() ){
				$( "#ItemPedidoExameBaixaDataRealizacaoExame" ).focus();
				new mensagem('Data Resultado não pode ser menor que Data Realização.');
				return;
			}

			//trecho comentado a pedido da demanda PC-1116 que visa Retirar o campo resultado do exame e nao torna-lo mais obrigatorio.
			
			// é obrigatório preencher
			// if(!resultado_do_exame){
			// 	$( "#ItemPedidoExameBaixaResultado" ).focus();
			// 	new mensagem('Escolha um resultado para o exame.');
			// 	return;
			// }
			
			// se for exame alterado é obrigatório ter uma descrição
			// if(descricao_alteracao.trim() == ''){
			// 	$( "#ItemPedidoExameBaixaDescricao" ).focus();
			// 	new mensagem('Para a inclusão de uma baixa com resultado alterado, é necessária a inclusão de uma descrição da anormalidade.');
			// 	return;
			// }

	}

	var div = jQuery('#modal_data');
    bloquearDiv(div);
	
	//envia via ajax a data de realizacao
	$.ajax({
		url: baseUrl + 'consultas_agendas/salvar_realizacao_data',
		type: 'POST',
		dataType: 'json',
		data: {
			"codigo_item_pedido"   : codigo_item_pedido,
			"parecer"			   : parecer,
			"data_realizacao_exame": data_realizacao_exame,
			"data_resultado_exame" : data_resultado_exame,
			// "resultado_do_exame"   : resultado_do_exame,
			"compareceu"   		   : compareceu,
			// "descricao_alteracao"  : descricao_alteracao,
			"codigo_medico"		   : codigo_medico,
		}

	})
	.done(function(data) {
		
		if(data.retorno == 'false') {
			swal({
				type: 'warning',
				title: 'Atenção',
				text: data.mensagem,
			});
			
		desbloquearDiv(div);

		} else {
			swal({
				type: 'success',
				title: 'Sucesso',
				text: 'Dados atualizados com sucesso.'
			});

			editar_realizacao_datas(codigo_item_pedido, 0);
			atualizaLista();
		}
	});


}//fim function salvar_realizacao

</script>