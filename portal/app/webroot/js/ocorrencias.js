function atualizaListaOcorrencias() {
    div = jQuery("div.lista");
    bloquearDiv(div);
    div.load(baseUrl + "ocorrencias/listagem/normal/" + Math.random());
}

function atualizaListaOcorrenciasConsulta() {
    div = jQuery("div.lista");
    bloquearDiv(div);
    div.load(baseUrl + "ocorrencias/listagem/consulta/" + Math.random());
}

function ocorrencias_consulta(elemento) {
    var setor = jQuery('#OcorrenciaTipo').val();
    var tipo_sla = jQuery(elemento).parent().parent().find('td:first').html();
    form = '<div id="postlink">';
    form += '<form accept-charset="utf-8" method="post" id="OcorrenciaListaOcorrenciasConsultaForm" action="/portal/ocorrencias/pre_filtro_lista_ocorrencias_consulta">';
    if (tipo_sla.toUpperCase() == 'SEM ANÁLISE DENTRO DO SLA') {
        form += '<input value="2" name="data[Ocorrencia][tipo_sla]">';
        form += '<input value="1" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
        form += '<input value="3" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
    } else if (tipo_sla.toUpperCase() == 'SEM ANÁLISE FORA DO SLA') {
        form += '<input value="1" name="data[Ocorrencia][tipo_sla]">';
        form += '<input value="1" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
        form += '<input value="3" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
    } else if (tipo_sla.toUpperCase() == 'EM ANÁLISE DENTRO DO SLA') {
        form += '<input value="2" name="data[Ocorrencia][tipo_sla]">';
        if (setor == 1) {
            form += '<input value="4" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
            form += '<input value="7" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
            form += '<input value="11" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
        } else if (setor == 2) {
            form += '<input value="4" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
        } else if (setor == 3) {
            form += '<input value="7" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
            form += '<input value="11" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
        }
            
    } else if (tipo_sla.toUpperCase() == 'EM ANÁLISE FORA DO SLA') {
        form += '<input value="1" name="data[Ocorrencia][tipo_sla]">';
        if (setor == 1) {
            form += '<input value="4" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
            form += '<input value="7" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
            form += '<input value="11" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
        } else if (setor == 2) {
            form += '<input value="4" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
        } else if (setor == 3) {
            form += '<input value="7" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
            form += '<input value="11" name="data[Ocorrencia][codigo_status_ocorrencia][]">';
        }
    }
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink form").submit();
}