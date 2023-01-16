var documentos_prestador = new Object();

$(document).ready(function(){
    var codigo_fornecedor = $('#ConsultaCodigoFornecedor').val();        
    if(codigo_fornecedor){
        preenche_name_fornecedor(codigo_fornecedor);
    }

    jQuery('#ConsultaSituacaoAV').on("click", function(){
        if(jQuery(this).is(':checked')){
            jQuery('div#data_periodo').show();
        }else{
            jQuery('div#data_periodo').hide();
        }
    });
        
    jQuery('#ConsultaDataInicio').on("change", function(){
        if(jQuery('#ConsultaSituacaoAV').is(':checked')){
            if(moment().diff(moment(this.value, ['DD/MM/YYYY', 'YYYY-MM-DD'], true), "days") > 0){
                swal('ATENÇÃO!', 'A data de inicio do à vencer, não pode ser menor que a data atual!', 'warning');
                this.value = jQuery(this).attr('oldvalue');
            }else{
                jQuery(this).attr('oldvalue', this.value);
            }
        }
    })
    
    $('.datepickerjs').datepicker({
        dateFormat: 'dd/mm/yy',
        showOn : 'button',
        buttonImage : baseUrl + 'img/calendar.gif',
        buttonImageOnly : true,
        buttonText : 'Escolha uma data',
        dayNames : ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sabado'],
        dayNamesShort : ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
        dayNamesMin : ['D','S','T','Q','Q','S','S'],
        monthNames : ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort : ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
        onClose : function() {}
    }).mask("99/99/9999");
});

$(document).on('blur', '#ConsultaCodigoFornecedor', function() { 
    var codigo_fornecedor = $('#ConsultaCodigoFornecedor').val();
    if (codigo_fornecedor) {
        preenche_name_fornecedor(codigo_fornecedor);
    }
});

function preenche_name_fornecedor(codigo_fornecedor){
    var input = $('#ConsultaCodigoFornecedorCodigo');
    $.ajax({
        url:baseUrl + 'consultas/get_fornecedores/' + codigo_fornecedor + '/' + Math.random(),
        dataType: 'json',
        beforeSend: function() {
            bloquearDiv(input.parent());
        },
        success: function(data) {
            // console.log(data);
            if (data.sucesso) {
                var input_name_display = $('#ConsultaCodigoFornecedorCodigo').val(data.dados.razao_social);
            } else {
                swal('ATENÇÃO!', 'Prestador Não encontrado', 'warning');
                var input_name_display = $('#ConsultaCodigoFornecedorCodigo').val('');
            }
        },
        complete: function() {
            input.parent().unblock();
        }
    });
}



