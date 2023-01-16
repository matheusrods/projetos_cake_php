<div class = 'form-procurar'>
	<?= $this->element('/filtros/viagens_faturamento_total') ?>
</div>
<div class='lista'></div>

<?php echo $this->Javascript->codeBlock("
	function listar_sms(pagador,tipo){
	    form_id = ('formresult' + Math.random()).replace('.','');
		$('#postlink').remove();
	    form = '<div id=\"postlink\" style=\"display:none;\">';
	    form += '<form accept-charset=\"utf-8\" method=\"post\" target=\"'+form_id+'\" action=\"/portal/viagens_faturamento/listar_sms/1\">'
	    form += '<input type=\"text\" id=\"ViagemFaturamentoPagador\" value=\"\" name=\"data[ViagemFaturamento][pagador]\">';
	    form += '<input type=\"text\" id=\"ViagemFaturamentoEmbarcador\" value=\"\" name=\"data[ViagemFaturamento][embarcador]\">';
	    form += '<input type=\"text\" id=\"ViagemFaturamentoTransportador\" value=\"\" name=\"data[ViagemFaturamento][transportador]\">';
	    form += '<input type=\"text\" id=\"ViagemFaturamentoMesFaturamento\" value=\"\" name=\"data[ViagemFaturamento][mes_faturamento]\">';
	    form += '<input type=\"text\" id=\"ViagemFaturamentoAnoFaturamento\" value=\"\" name=\"data[ViagemFaturamento][ano_faturamento]\">';
	    form += '<input type=\"text\" id=\"ViagemFaturamentoAnoMonitorada\" value=\"\" name=\"data[ViagemFaturamento][monitorada]\">';
	    form += '<input type=\"text\" id=\"ViagemFaturamentoAnoFrota\" value=\"\" name=\"data[ViagemFaturamento][frota]\">';
	    form += '</form>';
	    form += '</div>';
	    jQuery('body').append(form);
	    jQuery(\"#postlink #ViagemFaturamentoPagador\").val(pagador);
	    jQuery(\"#postlink #ViagemFaturamentoMesFaturamento\").val(jQuery('#ViagemFaturamentoSubtotalMesFaturamento').val());
	    jQuery(\"#postlink #ViagemFaturamentoAnoFaturamento\").val(jQuery('#ViagemFaturamentoSubtotalAnoFaturamento').val());
	    switch(tipo){
	    	case '1':
	    		jQuery(\"#postlink #ViagemFaturamentoAnoMonitorada\").val('1');
	    		jQuery(\"#postlink #ViagemFaturamentoAnoFrota\").val('1');
	    		break;
	    	case '2':
	    		jQuery(\"#postlink #ViagemFaturamentoAnoMonitorada\").val('2');
	    		jQuery(\"#postlink #ViagemFaturamentoAnoFrota\").val('1');
	    		break;
    		case '3':
	    		jQuery(\"#postlink #ViagemFaturamentoAnoFrota\").val('1');
	    		break;
	    	case '4':
	    		jQuery(\"#postlink #ViagemFaturamentoAnoFrota\").val('2');
	    		break;
	    }
	    var janela = window_sizes();
		window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	    jQuery(\"#postlink form\").submit();
	}

	function subtotal(codigo_cliente){
	    form_id = ('formresult' + Math.random()).replace('.','');
		$('#postlink').remove();
	    form = '<div id=\"postlink\" style=\"display:none;\">';
	    form += '<form accept-charset=\"utf-8\" method=\"post\" target=\"'+form_id+'\" action=\"/portal/viagens_faturamento/embarcador_transportador/1\">'
	    form += '<input type=\"text\" id=\"ViagemFaturamentoSubtotalPagador\" value=\"\" name=\"data[ViagemFaturamentoSubtotal][pagador]\">';
	    form += '<input type=\"text\" id=\"ViagemFaturamentoSubtotalEmbarcador\" value=\"\" name=\"data[ViagemFaturamentoSubtotal][embarcador]\">';
	    form += '<input type=\"text\" id=\"ViagemFaturamentoSubtotalTransportador\" value=\"\" name=\"data[ViagemFaturamentoSubtotal][transportador]\">';
	    form += '<input type=\"text\" id=\"ViagemFaturamentoSubtotalMesFaturamento\" value=\"\" name=\"data[ViagemFaturamentoSubtotal][mes_faturamento]\">';
	    form += '<input type=\"text\" id=\"ViagemFaturamentoSubtotalAnoFaturamento\" value=\"\" name=\"data[ViagemFaturamentoSubtotal][ano_faturamento]\">';
	    form += '</form>';
	    form += '</div>';
	    jQuery('body').append(form);
	    jQuery(\"#postlink #ViagemFaturamentoSubtotalPagador\").val(codigo_cliente);
	    jQuery(\"#postlink #ViagemFaturamentoSubtotalMesFaturamento\").val(jQuery('#ViagemFaturamentoTotalMesFaturamento').val());
	    jQuery(\"#postlink #ViagemFaturamentoSubtotalAnoFaturamento\").val(jQuery('#ViagemFaturamentoTotalAnoFaturamento').val());
	    var janela = window_sizes();
		window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	    jQuery(\"#postlink form\").submit();
	}

	function placas(codigo_cliente,tipo){
	    form_id = ('formresult' + Math.random()).replace('.','');
		$('#postlink').remove();
	    form = '<div id=\"postlink\" style=\"display:none;\">';
	    switch(tipo){
	    	case '1':
	    		form += '<form accept-charset=\"utf-8\" method=\"post\" target=\"'+form_id+'\" action=\"/portal/viagens_faturamento/listar_placas/1/1\">';
	    		break;
	    	case '2':
	    		form += '<form accept-charset=\"utf-8\" method=\"post\" target=\"'+form_id+'\" action=\"/portal/viagens_faturamento/listar_placas/0/1\">';
	    		break;
	    }
	    form += '<input type=\"text\" id=\"ViagemFaturamentoTotalPagador\" value=\"\" name=\"data[ViagemFaturamentoTotal][pagador]\">';
	    form += '<input type=\"text\" id=\"ViagemFaturamentoTotalMesFaturamento\" value=\"\" name=\"data[ViagemFaturamentoTotal][mes_faturamento]\">';
	    form += '<input type=\"text\" id=\"ViagemFaturamentoTotalAnoFaturamento\" value=\"\" name=\"data[ViagemFaturamentoTotal][ano_faturamento]\">';
	    form += '</form>';
	    form += '</div>';
	    jQuery('body').append(form);
	    jQuery(\"#postlink #ViagemFaturamentoTotalPagador\").val(codigo_cliente);
	    jQuery(\"#postlink #ViagemFaturamentoTotalMesFaturamento\").val(jQuery('#ViagemFaturamentoTotalMesFaturamento').val());
	    jQuery(\"#postlink #ViagemFaturamentoTotalAnoFaturamento\").val(jQuery('#ViagemFaturamentoTotalAnoFaturamento').val());
	    var janela = window_sizes();
		window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	    jQuery(\"#postlink form\").submit();
	}
"); ?>