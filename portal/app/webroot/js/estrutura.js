var estrutura = new Object();
estrutura = {
    addEmail: function() {
        var id = $('#emails_no_exists > div').length;

        $('#modelos_exists #modelo_emails #emails_no_exists > div')
        .clone()
        .attr('id', 'email_id_' + id)
        .appendTo('#emails_no_exists')
        .show().find('input, select')
        .each(function(index, element) {
            $(element).attr('name', $(element).attr('name').replace('[X]', '[' + id + ']'));
            $(element).attr('id', $(element).attr('id').replace('X', id));
            $(element).attr('data-codigo', $(element).attr('data-codigo').replace('X', id));
            $(element).attr('class', $(element).attr('class').replace('X', id));
        });
    },
    addEmailExists: function() {
         // var id = $('#emails .input-group').length;
        var id = $('#emails_exists > div').length;

        $('#horario_periodo_edit #emails_exists > div')
        .clone()
        .attr('id', 'email_id_' + id)
        .appendTo('#emails_exists')
        .show()
        .find('input, select')
        .each(function(index, element) {
            $(element).attr('name', $(element).attr('name').replace('[X]', '[' + id + ']'));
            $(element).attr('id', $(element).attr('id').replace('X', id));
            $(element).attr('data-codigo', $(element).attr('data-codigo').replace('X', id));
            $(element).attr('class', $(element).attr('class').replace('X', id));
        });
    },
}

jQuery(document).ready(function(){
    atualizaLista();
});

    function modal_exportacao(codigo_cliente,mostra) {
        if(mostra) {
            
            var div = jQuery("div#exportarBaseDados");
            bloquearDiv(div);
            div.load(baseUrl + "clientes_implantacao/modal_exportacao_funcionarios/" + codigo_cliente);
    
            $("#exportarBaseDados").css("z-index", "1050");
            $("#exportarBaseDados").modal("show");

        } else {
            $(".modal").css("z-index", "-1");
            $("#exportarBaseDados").modal("hide");
        }

    }

    var i = 1;
    $('body').on('click', '.js-add-email', function() {
        var html = $(this).parents('.js-encapsulado')
        .find('.js-memory')
        .html().replace(/xx/g, i)
        .replace(/Xx/g, i)
        .replace(/disabled="disabled"/g, '');

        $(this).parents('.js-encapsulado').append(html).find('.inputs-config.hide').show();
        $(this).removeClass('js-add-email').addClass('js-remove-email').attr('data-original-title', 'Remover Email').children('i').removeClass('icon-plus').addClass('icon-minus');
        $('[data-toggle="tooltip"]').tooltip();
        i++;
    });//FINAL
        
    $('body').on('click', '.js-remove-email', function() {
        $(this).parents('.inputs-config').remove();
    });//FINAL click

$(".liberacao_select_all").on("change", function(){

    if ($(this).is(":checked")) {               
        $('.select_liberacao').prop('checked','checked');
    } else {
        $('.select_liberacao').removeProp('checked');
    }
})

$(document).on('change', '.descricao-email', function(e) {
    e.preventDefault();
    var data = $(this).data('codigo');
    var valor = $(this).val();
    $('.check_email_' + data).attr('value', valor);
});

$(".descricao-email").each(function(indice){
    var id = $(this).prop('id');        
    var data = $(this).data('codigo');
    var valor = $(this).val();

    if(valor != ''){
        $('.check_email_' + data).attr('value', valor);
    }
});

$('#export_base_por_email').click(function() {

    var codigo_matriz = $('#codigo_cliente_h').val();        
    var unidades = $("#unidades").val();

    if (unidades.length == 0) {
        swal('Atenção!', 'Selecione uma unidade para filtrar!', 'error');            
        return false;
    }

    var status = '';

    $(".checkbox_content input").each(function(){
        if ($(this).is(":checked")) {
            status += $(this).val() + ',';
        }
    });

    var emails = '';

    $(".email_checando input").each(function(){
        if ($(this).is(":checked")) {
            emails += $(this).val() + ',';
        }
    });

    var checados = [];
    $.each($(".email_checando input"), function(){
        if ($(this).is(":checked")) {
            checados.push($(this).val());
            var data = $(this).data('codigo');
        }         
    });

    if(!checados.length){ 
        swal('Erro!', 'É necessario escolher o Email. :)', 'error');
        return false;
    }

    const url = unidades +'|'+ status.slice(0, -1);

    var email_contato = emails.slice(0, -1);
    email_contato = email_contato.replace(',on', '');

    var div = jQuery('#exportarBaseDados');
    bloquearDiv(div);

    $.ajax({
        url: baseUrl + 'clientes_implantacao/enviar_email_exportacao_funcionarios',
        type: 'POST',
        dataType: 'json',
        data: {
            "codigo_matriz"   : codigo_matriz,
            "url"             : url,
            "emails"          : email_contato,
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
                text: 'Email enviado!'
            });

            $("#fechar_modal").click();
            desbloquearDiv(div);
        }
    });
});

function manipula_modal(id, mostra) {
    if(mostra) {
        $(".modal").css("z-index", "-1");
    
        $("#" + id).css("z-index", "1050");
        $("#" + id).modal("show");
    } else {
        $("#" + id).css("z-index", "-1");
        $("#" + id).modal("hide");
    }
}

function salvar_vias_aso(codigo){
    if($.isNumeric($("#GrupoEconomicoViasAso").val())){
        $.ajax({
            type: "POST",
            url: "/portal/clientes_implantacao/atualiza_vias_aso",
            dataType: "json",
            data: {
              codigo: codigo,
              qtd_vias: $("#GrupoEconomicoViasAso").val()
               },
            beforeSend: function() {
               $("#rodape_botoes").html("<img src=\"/portal/img/default.gif\" style=\"padding: 10px;\"> Carregando");
            },
            success: function(data) {
                if(data == "1") {
                    atualizaLista();
                    manipula_modal("modal_vias_aso", 0);
                } else {
                    manipula_modal("modal_vias_aso", 0);
                    atualizaLista();
                    swal({
                        type: "error",
                        title: "Atenção",
                        text: "Não foi possível alterar a quantidade de vias, tente novamente."
                    });
                }
            },
            error: function(erro) {
                manipula_modal("modal_vias_aso", 0);
                atualizaLista();
                swal({
                    type: "error",
                    title: "Atenção",
                    text: "Não foi possível alterar a quantidade de vias, tente novamente mais tarde."
                });
            }
        });
    } else {
       $("#erro_modal").show();
    }
}

function atualizaLista() {
    var div = jQuery("#lista");
    var codigo_cliente = $('#codigo_cliente_h').val();
    var referencia = $('#referencia_h').val();
    var terceiros = $('#terceiros').val();

    bloquearDiv(div);
    div.load(baseUrl + "clientes_implantacao/estrutura_listagem/" + codigo_cliente + '/' + referencia + '/' + terceiros); 
}

$(function(){

    $("#exportar_base_de_dados").on("click", function($e){

        var unidades = $("#unidades").val();

        if (unidades.length == 0) {
            alert("Selecione uma unidade para filtrar!");
            return false;
        }

        var parametros = '';

        $(".checkbox_content input").each(function(){
            if ($(this).is(":checked")) {
                parametros += $(this).val() + ',';
            }
        });

        const url = unidades +'|'+ parametros.slice(0, -1);

        var codigo_matriz = $('#codigo_cliente_h').val();    

        window.location.href = "/portal/grupos_economicos/exportar_funcionario/ "+ codigo_matriz + '/implantacao/' + url + "";

        $("#fechar_modal").click();
    })
})