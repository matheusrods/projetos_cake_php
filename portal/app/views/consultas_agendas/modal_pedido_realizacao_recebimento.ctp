<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Editar - Dados da Realização do Exame</h3>
		</div>

		<div class="modal-body" style="min-height: 150px;">

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

			<div>
				<span style="font-size: 1.2em">
					<b>Exame:</b><br />
					<?php echo $pedido['Exame']['descricao']; ?>
				</span>
			</div>

			<hr>

			<span style="font-size: 1.2em">
				<b>Recebimento Digitalizado:</b>
				<?php echo $this->BForm->input('ItemPedidoExame.recebimento_digital', array('type' => 'radio', 'options' => array('1' => 'Sim','0' => 'Não'), 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'), 'value' => $pedido['ItemPedidoExame']['recebimento_digital'])) ?>
			</span>

			<span style="font-size: 1.2em">
				<b>Enviado ao Cliente:</b>
				<?php echo $this->BForm->input('ItemPedidoExame.recebimento_enviado', array('type' => 'radio', 'options' => array('1' => 'Sim','0' => 'Não'), 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'), 'value' => $pedido['ItemPedidoExame']['recebimento_enviado'])) ?>
			</span>

		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);" onclick="editar_realizacao_recebimento(<?php echo $codigo_item_pedido; ?>, 0);" class="btn btn-danger">FECHAR</a>
				<a href="javascript:void(0);" onclick="salvar_realizacao(<?php echo $codigo_item_pedido; ?>);" class="btn btn-success">SALVAR</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	setup_mascaras(); 
	setup_datepicker(); 
	setup_time(); 
});

function salvar_realizacao(codigo_item_pedido) {

	var recebimento_digital	  = '';
	if( $('#ItemPedidoExameRecebimentoDigital0').is(":checked") ){
		recebimento_digital = 0;
	} else if( $('#ItemPedidoExameRecebimentoDigital1').is(":checked") ){
		recebimento_digital = 1;
	}

	var recebimento_enviado   = '';
	if( $('#ItemPedidoExameRecebimentoEnviado0').is(":checked") ){
		recebimento_enviado = 0;
	} else if( $('#ItemPedidoExameRecebimentoEnviado1').is(":checked") ){
		recebimento_enviado = 1;
	}

	var div = jQuery('#modal_data');
    bloquearDiv(div);

	//envia via ajax a data de realizacao
	$.ajax({
		url: baseUrl + 'consultas_agendas/salvar_realizacao_recebimento',
		type: 'POST',
		dataType: 'json',
		data: {"codigo_item_pedido": codigo_item_pedido, "recebimento_digital": recebimento_digital, "recebimento_enviado": recebimento_enviado},
	})
	.done(function(data) {
		
		if(data.retorno == 'false') {
			swal({
				type: 'warning',
				title: 'Atenção',
				text: 'Erro ao atualizar dados da realização.'
			});
		} 
		else {

			swal({
				type: 'success',
				title: 'Sucesso',
				text: 'Dados atualizados com sucesso.'
			});

			editar_realizacao_recebimento(codigo_item_pedido, 0);
			atualizaLista();
		}
	});


}//fim function salvar_realizacao

</script>