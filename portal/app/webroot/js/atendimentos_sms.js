function atendimentos_consulta(elemento) {
    var setor = jQuery('#AtendimentoSmTipo').val();
    var tipo_sla = jQuery(elemento).parent().parent().find('td:first').html();
    form = '<div id="postlink">';
    form += '<form accept-charset="utf-8" method="post" id="AtendimentoSmAtendimentoSmsConsultaForm" action="/portal/atendimentos_sms/pre_filtro_atendimentos_consulta">';
    if (tipo_sla.toUpperCase() == 'SEM ANÁLISE DENTRO DO SLA') {
        form += '<input value="2" name="data[AtendimentoSmConsulta][tipo_sla]">';
        form += '<input value="1" name="data[AtendimentoSmConsulta][sla]">';
        if (setor == 2) {
            form += '<input value="1" name="data[AtendimentoSmConsulta][codigo_passo_atendimento]">';
        } else if (setor == 3) {
            form += '<input value="2" name="data[AtendimentoSmConsulta][codigo_passo_atendimento]">';
        }
    } else if (tipo_sla.toUpperCase() == 'SEM ANÁLISE FORA DO SLA') {
        form += '<input value="1" name="data[AtendimentoSmConsulta][tipo_sla]">';
        form += '<input value="2" name="data[AtendimentoSmConsulta][sla]">';
        if (setor == 2) {
            form += '<input value="1" name="data[AtendimentoSmConsulta][codigo_passo_atendimento]">';
        } else if (setor == 3) {
            form += '<input value="2" name="data[AtendimentoSmConsulta][codigo_passo_atendimento]">';
        }
    } else if (tipo_sla.toUpperCase() == 'EM ANÁLISE DENTRO DO SLA') {
        form += '<input value="2" name="data[AtendimentoSmConsulta][tipo_sla]">';
        form += '<input value="3" name="data[AtendimentoSmConsulta][sla]">';
        form += '<input value="1" name="data[AtendimentoSmConsulta][status_atendimento][]">';
        form += '<input value="2" name="data[AtendimentoSmConsulta][status_atendimento][]">';
        if (setor == 2) {
            form += '<input value="1" name="data[AtendimentoSmConsulta][codigo_passo_atendimento]">';
        } else if (setor == 3) {
            form += '<input value="2" name="data[AtendimentoSmConsulta][codigo_passo_atendimento]">';
        }
    } else if (tipo_sla.toUpperCase() == 'EM ANÁLISE FORA DO SLA') {
        form += '<input value="1" name="data[AtendimentoSmConsulta][tipo_sla]">';
        form += '<input value="4" name="data[AtendimentoSmConsulta][sla]">';
        form += '<input value="1" id="AtendimentoSmConsultaStatusAtendimento" name="data[AtendimentoSmConsulta][status_atendimento][]">';
        form += '<input value="2" id="AtendimentoSmConsultaStatusAtendimento" name="data[AtendimentoSmConsulta][status_atendimento][]">';
        if (setor == 2) {
            form += '<input value="1" name="data[AtendimentoSmConsulta][codigo_passo_atendimento]">';
        } else if (setor == 3) {
            form += '<input value="2" name="data[AtendimentoSmConsulta][codigo_passo_atendimento]">';
        }
    }
    form += '</form>';
    form += '</div>';
    jQuery('body').append(form);
    jQuery("#postlink form").submit();
}