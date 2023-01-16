<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Editar - Avaliação Anexo</h3>
		</div>

		
		<div class="modal-body" style="min-height: 295px;max-height: 360px;">

			<div style="float: left;width: 200px;">
				<span style="font-size: 1.2em">
					<b>Código Pedido:</b>
					<?php echo $anexo['0']['codigo_pedido']; ?>
				</span>
			</div>
			
			<div>
				<span style="font-size: 1.2em">
					<b>Código Item:</b>
					<?php echo $anexo['0']['codigo_item_pedido_exame']; ?>
				</span>
			</div>
			<hr>
			<div>
				<span style="font-size: 1.2em">
					<b>Cliente</b><br />
					<?php echo $anexo['0']['cliente_razao_social']; ?>
				</span>
			</div>
			<br/>
			<div>
				<span style="font-size: 1.2em">
					<b>Exame</b><br />
					<?php echo $anexo['0']['nome_exame']; ?>
				</span>
			</div>
 			<br />

			<div>
				<span style="font-size: 1.2em">
					<b>Arquivo</b><br />
					<?php echo basename($anexo['0']['caminho_arquivo']); ?>
				</span>
			</div>

			<hr>
				<div>
					<span style="font-size: 1.2em">
						<b>Status</b>
							<?php echo $this->BForm->input('Anexo.status', array('type' => 'radio', 'options' => array('2' => 'Pendente','1' => 'Aprovado' , '0' => 'Recusado'), 'default' => $anexo['0']['status_arquivo'], 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
					</span>
				</div>

			<br />
		
			<div id="motivo_recusa">
				<span style="font-size: 1.2em;display: none;" id="span_motivo">
					<b>Motivo<font color="red" id="descricao_obrigatoria" style="display: none;">*</font></b>
					<?php echo $this->Form->input('Anexo.motivo_recusa', array('type' => 'textarea', 'class' => 'input-small', 'label' => false, 'style' => 'height: 60px; width: 220px; font-size: 11px;', 'value' =>"")); ?>
				</span>
			</div>
			<br />

		</div>

	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="avaliacao_anexo('<?php echo $anexo[0]['codigo_anexo']; ?>',<?php echo $anexo[0]['ficha_clinica']; ?>,0);"class="btn btn-danger">FECHAR</a>
				<a href="javascript:void(0);"onclick="salvar_avaliacao_anexo(<?php echo $anexo[0]['codigo_anexo']; ?>,<?php echo $anexo[0]['ficha_clinica']; ?>);" class="btn btn-success">SALVAR</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	setup_mascaras();
	setup_datepicker();
	setup_time();

	$("input[name='data[Anexo][status]']").change(function (){
		if ($('#AnexoStatus0').is(':checked')){
			$('#span_motivo').show();
		} else {
			$('#span_motivo').hide();
		}

	});


});

function salvar_avaliacao_anexo (codigo, ficha_clinica) {

	//pega a data
	var status_arquivo		  = $("input[name='data[Anexo][status]']:checked").val();
	var motivo_recusa   	  = $('#AnexoMotivoRecusa').val();

	var div = jQuery('#modal_data');
    bloquearDiv(div);
	
	//envia via ajax a data de realizacao
	$.ajax({
		url: baseUrl + 'consultas_agendas/salvar_moderacao_status',
		type: 'POST',
		dataType: 'json',
		data: {
			"codigo"			   : codigo,
			"ficha_clinica"        : ficha_clinica,
			"status_arquivo"	   : status_arquivo,
			"motivo_recusa"		   : motivo_recusa
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
			avaliacao_anexo(codigo,ficha_clinica,0);
			atualizaLista();
		}
	});


}//fim function salvar_realizacao

</script>