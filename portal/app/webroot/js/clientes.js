jQuery(document).ready(function () {
    jQuery('#ClienteCodigoClienteTipo').change(
        function () {
            jQuery.ajax({
                'url': baseUrl + 'clientes_sub_tipos/combo/' + jQuery(this).val() + '/' + Math.random(),
                'success': function (data) {
                    jQuery('#ClienteCodigoClienteSubTipo').html(data);
                }
            });
        }
    );

    codigo_cliente = jQuery("#ClienteCodigo").val();

    if (window.vizualizar) {
        carrega_contatos_cliente_visualizar(codigo_cliente);
        carrega_historico_cliente_visualizar(codigo_cliente);
        carrega_endereco_cliente_visualizar(codigo_cliente);
    } else {
        carrega_contatos_cliente(codigo_cliente);
        carrega_historico_cliente(codigo_cliente);
        carrega_endereco_cliente(codigo_cliente);
    }


    $(document).on("click", ".dialog_cliente_endereco", function (e) {
        e.preventDefault();
        open_dialog(this, "Endereço", 960);
    });

    $("#ClienteCpfMedicoPcmso").each(function (indice) {
        var val_cpf_medico = $(this).val();
        if (val_cpf_medico == '') {
            $('#CadastroCpfMedicoButton').removeClass('hide');
            $('#CadastroCpfMedicoButton').addClass('show');
        } else {
            $('#CadastroCpfMedicoButton').addClass('hide');
            $('#CadastroCpfMedicoButton').removeClass('show');
        }
    });

    $("#ClienteCodigoMedico").each(function (indice) {
        var valor = $(this).val();
        if (valor == '') {
            $('#CadastroCpfMedicoButton').addClass('hide');
            $('#CadastroCpfMedicoButton').removeClass('show');
        } else {
            $('#CadastroCpfMedicoButton').removeClass('hide');
            $('#CadastroCpfMedicoButton').addClass('show');
        }
    });


    /**
    $('#ClienteCnae').keydown(function(e) {
            if (e.keyCode == 13) {
                    e.preventDefault();
                    carrega_cnae($(this).val());
            }
    }).change(function() {
            carrega_cnae($(this).val());
    })
    **/

    /**
    * [enviaDados description]
    *
    * funcao para nao deixar enviar duas requisições de submit para o servidor gerando duplicidade de dados de clientes.
    * @return {[type]} [description]
    */
    enviaDados = function () {

        var retorno = true;//retorno true 
        var codigo_empresa = $('#ClienteCodigoEmpresaSession').val();//codigo empresa da sessao

        $("#div_salvar").html("<div class='btn btn-primary'>Gravando...</div>");

        if (codigo_empresa) {
            if (codigo_empresa != 5) {//cadastro do cpf que só poder obrigatorio para todas as empresas menos para a 5
                $("input[name^='data[Cliente][cpf_medico_pcmso]']").each(function (index2, value2) {

                    if (value2.value.trim() == '') {
                        $("input[name^='data[Cliente][cpf_medico_pcmso]']").css({ borderColor: 'red' });

                        swal({
                            type: 'warning',
                            title: 'Atenção',
                            text: 'O campo CPF do Médico é obrigatório, no momento ele esta vazio, favor atualizar.'
                        });
                        retorno = false;
                    }
                });
            }
        }

        $("input[name^='data[Cliente][cnae]']").each(function (index2, value2) {

            if (value2.value.trim() == '') {
                $("input[name^='data[Cliente][cnae]']").css({ borderColor: 'red' });

                swal({
                    type: 'warning',
                    title: 'Atenção',
                    text: 'O campo CNAE é obrigatório!'
                });
                retorno = false;
            }
        });


        if (retorno == true) {

            if ($("#ClienteEditarForm").length) {
                $("#ClienteEditarForm").submit();
            } else if ($('#ClienteIncluirTomadorServicoForm').length) {
                $('#ClienteIncluirTomadorServicoForm').submit();
            } else {
                $("#ClienteIncluirForm").submit();
            }
        } else {
            $("#div_salvar").html("<a href=\"javascript:void(0);\" onclick=\"enviaDados();\" class=\"btn btn-primary\" id=\"button_submit\"><i class=\"glyphicon glyphicon-share\"></i> Salvar</a>");
        }

        return
    }
});

jQuery("#ClienteCpfMedicoPcmso").change(function () {
    var cpf_medico_val = $("#ClienteCpfMedicoPcmso").val();
    if (cpf_medico_val != '') {
        $('#CadastroCpfMedicoButton').removeClass('show');
        $('#CadastroCpfMedicoButton').addClass('hide');
    } else {
        $('#CadastroCpfMedicoButton').removeClass('hide');
        $('#CadastroCpfMedicoButton').addClass('show');
    }
});

jQuery("#ClienteCodigoMedicoPcmso").change(function () {
    var codigo_medico = $("#ClienteCodigoMedicoPcmso").val();
    if (codigo_medico != '') {
        $("#botaoOk").each(function (indice) {
            var src = $(this).prop('href');
            $(this).prop('href', src + '/' + codigo_medico);
        });

    }
});

function carrega_endereco_cliente(codigo_cliente) {
    var div = jQuery("#endereco-cliente");
    bloquearDiv(div);
    div.load(baseUrl + 'clientes_enderecos/listar/' + codigo_cliente + '/' + Math.random());
}

function carrega_endereco_cliente_visualizar(codigo_cliente) {
    var div = jQuery("#endereco-cliente");
    bloquearDiv(div);
    div.load(baseUrl + 'clientes_enderecos/listar_visualizar/' + codigo_cliente + '/' + Math.random());
}

function carrega_historico_cliente(codigo_cliente) {
    var div = jQuery("#historico-cliente");
    bloquearDiv(div);
    div.load(baseUrl + 'clientes_historicos/listar/' + codigo_cliente + '/' + Math.random());
}

function carrega_historico_cliente_visualizar(codigo_cliente) {
    var div = jQuery("#historico-cliente");
    bloquearDiv(div);
    div.load(baseUrl + 'clientes_historicos/listar_visualizar/' + codigo_cliente + '/' + Math.random());
}

function carrega_operacoes_cliente(codigo_cliente) {
    var div = jQuery("#operacoes-cliente");
    bloquearDiv(div);
    div.load(baseUrl + 'clientes_operacoes/operacoes_por_cliente/' + codigo_cliente + '/' + Math.random());
}

function carrega_contatos_cliente(codigo_cliente) {
    var div = jQuery("#contatos-cliente");
    bloquearDiv(div);
    div.load(baseUrl + 'clientes_contatos/contatos_por_cliente/' + codigo_cliente + '/' + Math.random());
}

function carrega_contatos_cliente_visualizar(codigo_cliente) {
    var div = jQuery("#contatos-cliente");
    bloquearDiv(div);
    div.load(baseUrl + 'clientes_contatos/contatos_por_cliente_visualizar/' + codigo_cliente + '/' + Math.random());
}

function carrega_procuracoes_cliente(codigo_cliente) {
    var div = jQuery("#procuracoes-cliente");
    bloquearDiv(div);
    div.load(baseUrl + 'clientes_procuracoes/procuracoes_por_cliente/' + codigo_cliente + '/' + Math.random());
}

function carrega_relacionamentos_cliente(codigo_cliente) {
    var div = jQuery("#relacionamentos-cliente");
    bloquearDiv(div);
    div.load(baseUrl + 'clientes_relacionamentos/relacionamentos_por_cliente/' + codigo_cliente + '/' + Math.random());
}

function exclui_cliente_endereco(codigo_cliente_endereco, codigo_cliente) {
    if (confirm('Deseja realmente excluir ?'))
        jQuery.ajax({
            type: 'POST',
            url: baseUrl + 'clientes_enderecos/excluir/' + codigo_cliente_endereco + '/' + Math.random()
            , success: function (data) {
                carrega_endereco_cliente(codigo_cliente);
            }
        });
}

function excluir_cliente_operacao(codigo_cliente_operacao, codigo_cliente) {
    if (confirm('Deseja realmente excluir ?'))
        jQuery.ajax({
            type: 'POST',
            url: baseUrl + 'clientes_operacoes/excluir/' + codigo_cliente_operacao
            , success: function (data) {
                carrega_operacoes_cliente(codigo_cliente);
            }
        });
    $("#dialogo_excluir")
        .empty()
        .text('Deseja realmente excluir esta operação?')
        .dialog({
            resizable: false,
            height: 140,
            modal: true,
            buttons: {
                "Ok": function () {
                    $.post(baseUrl + 'clientes_operacoes/excluir/', {
                        "data[ClienteOperacao][codigo]": codigo_cliente_operacao
                    }, function () {
                        carrega_operacoes_cliente(codigo_cliente);
                    }
                    );
                    $(this).dialog("close");
                },
                "Cancelar": function () {
                    $(this).dialog("close");
                }
            }
        });
}

function reativar_cliente_procuracao(codigo_procuracao, codigo_cliente) {
    $("#dialogo_excluir")
        .empty()
        .text('Deseja realmente reativar o período desta procuração?')
        .dialog({
            resizable: false,
            height: 140,
            modal: true,
            buttons: {
                "Ok": function () {
                    $.post(baseUrl + 'clientes_procuracoes/reativar/', {
                        "data[ClienteProcuracao][codigo]": codigo_procuracao
                    }, function () {
                        carrega_procuracoes_cliente(codigo_cliente);
                    }
                    );
                    $(this).dialog("close");
                },
                "Cancelar": function () {
                    $(this).dialog("close");
                }
            }
        });
}

function excluir_cliente_contato(codigo_cliente_contato, codigo_cliente) {
    if (confirm('Deseja realmente excluir ?'))
        jQuery.ajax({
            type: 'POST',
            url: baseUrl + 'clientes_contatos/excluir/' + codigo_cliente_contato + '/' + Math.random()
            , success: function (data) {
                carrega_contatos_cliente(codigo_cliente);
            }
        });
}

function finalizar_cliente_procuracao(codigo_procuracao, codigo_cliente) {
    $("#dialogo_excluir")
        .empty()
        .text('Deseja realmente finalizar o período desta procuração?')
        .dialog({
            resizable: false,
            height: 140,
            modal: true,
            buttons: {
                "Ok": function () {
                    $.ajax(baseUrl + 'clientes_procuracoes/inativar/', {
                        "data[ClienteProcuracao][codigo]": codigo_procuracao
                    }, function () {
                        carrega_procuracoes_cliente(codigo_cliente);
                    }
                    );
                    $(this).dialog("close");
                },
                "Cancelar": function () {
                    $(this).dialog("close");
                }
            }
        });
}

function excluir_cliente_relacionamento(codigo_cliente_relacionamento, codigo_cliente) {
    if (confirm('Deseja realmente excluir ?'))
        jQuery.ajax({
            type: 'POST',
            url: baseUrl + 'clientes_relacionamentos/excluir/' + codigo_cliente_relacionamento
            , success: function (data) {
                carrega_relacionamentos_cliente(codigo_cliente);
            }
        });
}

function mostra_detalhe(link, codigo) {
    jQuery('.detalhe' + codigo).toggle();
    if (jQuery(link).text() == 'detalhar') {
        jQuery(link).text('esconder');
    } else {
        jQuery(link).text('detalhar');
    }
}

function mostrar_historico(link) {
    jQuery('#historico-cliente').slideToggle('slow');
    tag = jQuery(link).find('i');
    if (tag.hasClass('icon-eye-open')) {
        tag.removeClass('icon-eye-open');
        tag.addClass('icon-eye-close');
    } else {
        tag.removeClass('icon-eye-close');
        tag.addClass('icon-eye-open');

    }
}

$(document).on("keydown", "#ClienteRelacionamentoCodigoClienteRelacao", function (e) {
    var mensagem_razao_social;
    if (e.keyCode == '13' || e.keyCode == '9') {
        e.preventDefault();

        $.get(baseUrl + 'clientes/buscar/' + $(this).val() + '/' + Math.random(), function (retorno) {
            if (retorno.sucesso == true) {
                $("#ClienteRelacionamentoRazaoSocial").val(retorno.dados.razao_social);
                $("#ClienteRelacionamentoCodigoTipoRelacionamento").focus();
            } else {
                $("#ClienteRelacionamentoRazaoSocial").val("Cliente não encontrado!");
            }
        }, 'json');
        $("#ClienteRelacionamentoRazaoSocial").val(mensagem_razao_social);
    }
})