
<?php
$codigo_prestador = !empty($this->data['HistoricoSm']['codigo_prestador']) ? $this->data['HistoricoSm']['codigo_prestador'] : 0;
?>

<div id="historicos-sms-prestadores" class="grupo"></div> 
<?= $this->Javascript->codeBlock("
	$(function(){
		var element_div      = '#historicos-sms-prestadores';
	    var div = jQuery(element_div);
	    bloquearDiv(div);
	    div.load(baseUrl + 'historicos_sms_prestadores/prestador_por_atendimento/' + $codigo_atendimento + '/' + $codigo_sm + '/' + $codigo_prestador + '/' + Math.random() );
	});");?>