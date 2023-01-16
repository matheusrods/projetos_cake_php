<div class="modal-dialog modal-sm" style="position: static;">
	<div class="modal-content" id="modal_data">
		<div class="modal-header" style="text-align: center;">
			<h3>Imprimir Relatório PCMSO</h3>
		</div>

		<div class="modal-body" style="min-height: 93px;max-height: 360px;">
			
			<?php echo $this->BForm->hidden('GerenciarPCMSO.codigo_unidade', array('value' => $codigo_unidade)); ?>
            
        <div style="float:left;">
            <span style="font-size: 1.2em">
                <b>Gerar documento com Setor e Cargo sem Funcionários?</b>
                <?php echo $this->BForm->input('GerenciarPCMSO.setor_cargo', array('type' => 'radio', 'options' => array('1' => 'Sim','0' => 'Não'), 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
            </span>
			</div>
			<br />

            <div style="float:left;">
                <span style="font-size: 1.2em">
                    <b>Gerar documento com Corpo Clínico?</b>
                    <?php echo $this->BForm->input('GerenciarPCMSO.corpo_clinico', array('type' => 'radio', 'options' => array('1' => 'Sim','0' => 'Não'), 'legend' => false, 'label' => array('class' => 'radio inline input-xsmall'))) ?>
                </span>
            </div>
		</div>


	    <div class="modal-footer">
	    	<div class="right">
				<a href="javascript:void(0);"onclick="parametros_relatorio_pcmso(<?php echo $codigo_unidade; ?>, 0);"class="btn btn-danger">CANCELAR</a>
				<a id="gerar_relatorio_pcmso_ok" href="javascript:void(0);" class="btn btn-success">GERAR</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	setup_mascaras();
	setup_datepicker();
	setup_time();

  	var habilitarBotao = ( 
		$('#GerenciarPCMSOSetorCargo0').is(":checked") || $('#GerenciarPCMSOSetorCargo1').is(":checked") &&
		$('#GerenciarPCMSOCorpoClinico0').is(":checked") || $('#GerenciarPCMSOCorpoClinico1').is(":checked") 
	);
	
	var botaoOk = $('#gerar_relatorio_pcmso_ok');
	
	if( false == habilitarBotao ){
			botaoOk.addClass( "disabled" );
	}

	$('input[type=radio]').change(function() {       
		botaoOk.removeClass( "disabled" );
		habilitarBotao = true;
  	});

	botaoOk.click(function() {

		var codigo_unidade = $('#GerenciarPCMSOCodigoUnidade').val();
		
		if( true == habilitarBotao ){

			var check_corpo_clinico = 0;
			var check_setor_cargo = 0;

			if ($('#GerenciarPCMSOCorpoClinico0').is(':checked')) {
				check_corpo_clinico = 1;
			}

			if ($('#GerenciarPCMSOCorpoClinico1').is(':checked')) {
				check_corpo_clinico = 1;
			}

			if ($('#GerenciarPCMSOSetorCargo0').is(':checked')) {
				check_setor_cargo = 1;
			}

			if ($('#GerenciarPCMSOSetorCargo1').is(':checked')) {
				check_setor_cargo = 1;
			}

			if(check_corpo_clinico == true && check_setor_cargo == true) {

				var corpo_clinico = '';
				if( $('#GerenciarPCMSOCorpoClinico0').is(":checked") ){
					corpo_clinico = 0; // nao
				} else if( $('#GerenciarPCMSOCorpoClinico1').is(":checked") ){
					corpo_clinico = 1; // sim
				}

				var setor_cargo = '';
				if( $('#GerenciarPCMSOSetorCargo0').is(":checked") ){
					setor_cargo = 0; // nao
				} else if( $('#GerenciarPCMSOSetorCargo1').is(":checked") ){
					setor_cargo = 1; // sim
				}

				gerar_relatorio(codigo_unidade, setor_cargo, corpo_clinico);
			} else {

				if(check_setor_cargo == false) {
					new mensagem('Gerar documento com Setor e Cargo sem Funcionários é necessário preencher!');
					return;
				}

				if(check_corpo_clinico == false) {
					new mensagem('Gerar documento com Corpo Clínico é necessário preencher!');
					return;
				}
			}
		}
  	});

});

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

function gerar_relatorio(codigo_unidade, setor_cargo, corpo_clinico) {
	var div = jQuery('#modal_data');
    bloquearDiv(div);

	var url = baseUrl + "/clientes_implantacao/imprimir_relatorio/" + codigo_unidade + "/" + setor_cargo + "/" + corpo_clinico;
	window.location.href = url; 

	parametros_relatorio_pcmso(codigo_unidade, 0);
}//fim function gerar_relatorio

</script>