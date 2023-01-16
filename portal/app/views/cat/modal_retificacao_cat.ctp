<div class="modal-dialog modal-lg" style="position: static;">
	<div class="modal-content">
		<div class="modal-header" style="text-align: center;">			
			<h4 class="modal-title" >Retificação CAT</h4>
		</div>

		<div class="modal-body" id="modal-body" style="min-height: 295px;max-height: 360px;">
			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('codigo', array('type' => 'hidden', 'value' => $codigo_cat)); ?>
				<?php echo $this->BForm->input('codigo_funcionario', array('type' => 'hidden', 'value' => $codigo_funcionario)); ?>
				<?php echo $this->BForm->input('evento_retificacao', array('label' => 'Identificação do evento:', 'options' => $evento_options,'empty' => 'Selecione', 'class' => 'input-medium')) ?>
			</div>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('recibo_retificacao', array('label' => 'Número do recibo:', 'class' => 'input-medium')) ?>
			</div>
			<div class='row-fluid inline'>
				<?php echo $this->BForm->input('motivo_retificacao', array('type' => 'textarea', 'label' => 'Motivo da retificação:', 'class' => 'input-medium', 'style' => 'margin: 0px 0px 10px; width: 472px; height: 103px;')); ?>
			</div>
		</div>

	    <div class="modal-footer">
	    	<div class="right" id="acao_button">
				<a href="/portal/cat/lista_cat/<?php echo $codigo_funcionario_setor_cargo; ?>" class="btn btn-default btn-lg"><i class="glyphicon glyphicon-fast-backward"></i> Voltar</a>
				<a href="javascript:void(0);" onclick="salvar_retificacao(<?php echo $codigo_cat; ?>);" id="SalvarOk"  class="btn btn-success">SALVAR</a>		
			</div>
		</div>
	</div>
</div>

<?php echo $this->BForm->end(); ?>

<script type="text/javascript">
$(document).ready(function() {
	setup_mascaras();
	setup_datepicker();
	setup_time();

	var habilitarButton = false;
	var buttonSave = $('#SalvarOk');	
	
	$('#evento_retificacao').on('change', function() {
		if(this.value != '') {
			habilitarButton = true;
			buttonSave.removeClass( "disabled" );
		} else {
			habilitarButton = false;
			buttonSave.removeClass( "disabled" );
		}
	});

	if( false == habilitarButton ) {
		buttonSave.addClass( "disabled" );
	}

	buttonSave.click(function() {
		if( true == habilitarButton ) {
			salvar_retificacao(<?php echo $codigo_cat; ?>);
		}
  	});
});

function salvar_retificacao(codigo_cat) {

	var evento_retificacao   = $('#evento_retificacao').val();
	var recibo_retificacao   = $('#recibo_retificacao').val();
	var motivo_retificacao   = $('#motivo_retificacao').val();
	var codigo_funcionario   = $('#codigo_funcionario').val();
	var retorno = true;

	if(evento_retificacao.trim() == ''){

		$( "#evento_retificacao" ).focus();
		
		swal({
			type: "warning",
			title: "Atenção",
			text: "Favor, informar a Identificação do evento.",
		});

		retorno = false;
	}

	if(evento_retificacao == 2){
		if(recibo_retificacao == ''){
			swal({
				type: "warning",
				title: "Atenção",
				text: "Se for Retificação, é obrigatório preenchimento do numero do Recibo."
			});

			jQuery("input[name='data[recibo_retificacao]']").css('box-shadow','0 0 5px 1px red');				
			
			retorno = false;
		}
	}

	if(retorno == true){

		$('#evento_retificacao').prop('disabled', true);
		$('#recibo_retificacao').prop('disabled', true);
		$('#motivo_retificacao').prop('disabled', true);
		$("#acao_button").html("<img src=\"/portal/img/default.gif\" style=\"padding: 10px;\"> Registrando Retificação...");

		// envia via ajax
		$.ajax({
			url: baseUrl + 'cat/salvar_retificacao',
			type: 'POST',
			dataType: 'json',
			data: {
				"codigo"             : codigo_cat,
				"evento_retificacao" : evento_retificacao,
				"codigo_funcionario" : codigo_funcionario,
				"recibo_retificacao" : recibo_retificacao,				
				"motivo_retificacao" : motivo_retificacao
			}
		})
		.done(function(data) {			
			
			if(data.retorno == 'false') {
				swal({
					type: 'warning',
					title: 'Atenção',
					text: data.mensagem,
				});
			} else {
				swal({
					type: 'success',
					title: 'Sucesso',
					text: 'Retificação registrada com sucesso, agora você pode atualizar a sua CAT.'
				});

				modal_retificacao(codigo_cat, 0);			
			}
		});
	}
	
}//fim function salvar_retificacao
</script>