<?php echo $this->element('solicitacoes_monitoramento/incluir_sm_destino') ?>
<?php
	if($retorno){
		echo $this->Javascript->codeBlock("
			$(document).ready(function(){
				$('#RecebsmRefeCodigoOrigem').val('".$retorno['Origem']['rpon_refe_codigo']."').change();
				$('#RecebsmRefeCodigoOrigemVisual').val('".$retorno['Origem']['rpon_descricao']."');
				$('#RecebsmMonitorarRetorno').prop('checked', ".($retorno['Origem']['monitorar_retorno'] ? "true" : "false").").change();
				$('#itinerario').unblock();
				$('.referencia').change(function() {
					valida_campos_rota_com_intinerario(this);
				});
			});
		");
	}
	echo $this->Javascript->codeBlock("
		$(document).ready(function(){
			setup_mascaras();
			setup_datepicker();
			setup_date();
			setup_time();
			$('.referencia').change(function() {
				valida_campos_rota_com_intinerario(this);
			});
		});
	");
?>