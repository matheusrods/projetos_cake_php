var proposta = new Object();
proposta = {
    mostraTabela: function() {
        $('#modal_tabela_padrao').modal('show');
    },
    mostraPagto: function(element) {
        if ($(element).val() == '1') {
            $('#pagto_deposito').hide();
        } else {
            $('#pagto_deposito').show();
        }
    },
    controlaServico: function(elemento, codigo) {
        var tipos = { 60: ".tipo_engenharia", 59: ".tipo_exame" };

        if ($('input[name="data[PropostaCredProduto][60]"]').is(":checked") && $('input[name="data[PropostaCredProduto][59]"]').is(":checked")) {
            $(tipos[60]).fadeIn();
            $(tipos[59]).fadeIn();
        } else if ($('input[name="data[PropostaCredProduto][60]"]').is(":checked") && !$('input[name="data[PropostaCredProduto][59]"]').is(":checked")) {
            $(tipos[60]).fadeIn();
            $(tipos[59]).fadeOut();
        } else if (!$('input[name="data[PropostaCredProduto][60]"]').is(":checked") && $('input[name="data[PropostaCredProduto][59]"]').is(":checked")) {
            $(tipos[60]).fadeOut();
            $(tipos[59]).fadeIn();
        } else {
            $(tipos[60]).fadeOut();
            $(tipos[59]).fadeOut();
        }

        /**
        if($(elemento).is(":checked")) {
        	$(tipos[codigo]).fadeIn();
        } else {
        	$(tipos[codigo]).fadeOut();
        }
        **/
    },
    // controlaHdiferenciado : function(elemento, codigo) {
    // 	var tipos = {1: ".servicoDiferenciado", 0: ".servicoDiferenciado"};
    // 	$(tipos[1]).fadeOut();
    // 	$(tipos[0]).fadeOut();

    // 	if($('input[name="data[Horario][horario_atendimento_diferenciado][1]"]').is(":checked") ) {
    // 		$(tipos[1]).fadeIn();
    // 	} else if($('input[name="data[Horario][horario_atendimento_diferenciado][0]"]').is(":checked") ) {
    // 		$(tipos[0]).fadeOut();
    // 	} else {
    // 		$(tipos[1]).fadeOut();
    // 		$(tipos[0]).fadeOut();
    // 	}
    // },
    populaCamposExames: function() {
        var exames = "";
        $('.checkbox_exames').each(function(i, element_exames_disponiveis) {
            if (element_exames_disponiveis.checked) {
                var adiciona = true;
                $('#exames select').each(function(i, element_exames_selecionados) {
                    if ($(element_exames_disponiveis).val() == $(element_exames_selecionados).val())
                        adiciona = false;
                });
                if (adiciona)
                    exames = exames + $(element_exames_disponiveis).val() + ", ";
            }
        });

        if (exames.length) {
            codigos = exames.substring(0, (exames.length - 2));
            $.ajax({
                type: 'POST',
                url: "/portal/propostas_credenciamento/retorna_tabela_padrao",
                dataType: "json",
                data: "json=1&exames=" + codigos,
                beforeSend: function() { $('#modal_carregando').modal('show'); },
                success: function(json) {
                    if ($('#exames .input-group').length > 1) {
                        proposta.addExame();
                    }

                    $.each(json, function(index, campo) {
                        proposta.preencheExame(campo.codigo, campo.nome, campo.valor);
                        proposta.addExame();
                    });

                    proposta.removeExame();
                },
                complete: function() { $('#modal_carregando').modal('hide'); }
            });
        }
    },
    addMedico: function() {
        var id = $('#corpo_clinico .input-group').length;

        $('#modelos #modelo_corpo_clinico .input-group').clone().appendTo('#corpo_clinico').show().find('input, select').each(function(index, element) {
            $(element).attr('name', $(element).attr('name').replace('[X]', '[' + id + ']'));
            $(element).attr('id', $(element).attr('id').replace('X', id));
        });
    },
    preencheExame: function(codigo, nome, valor) {
        var id = $('#exames .input-group').length - 1;

        $('select[name="data[PropostaCredExame][' + id + '][codigo_exame]"]').val(codigo);
        $('input[name="data[PropostaCredExame][' + id + '][valor]"]').val(valor);
    },
    addExame: function() {
        var id = $('#exames .input-group').length;

        $('#modelos #modelo_exames .input-group').clone().appendTo('#exames').show().find('input, select').each(function(index, element) {
            $(element).attr('name', $(element).attr('name').replace('[X]', '[' + id + ']'));
            $(element).attr('id', $(element).attr('id').replace('X', id));
        });
    },
    addEngenharia: function() {
        var id = $('#engenharias .input-group').length;

        $('#modelos #modelo_engenharias .input-group').clone().appendTo('#engenharias').show().find('input, select').each(function(index, element) {
            $(element).attr('name', $(element).attr('name').replace('[X]', '[' + id + ']'));
            $(element).attr('id', $(element).attr('id').replace('X', id));
        });
    },
    removeExame: function(element, codigo) {
        $('#exames .input-group:last-child').remove();
    },
    removeEngenharia: function(element, codigo) {
        $('#engenharias .input-group:last-child').remove();
    },
    addPeriodo: function() {
        var id = parseInt($('.form-group #periodos > div').last().attr('id').replace(/[\a-z]+(\_)/g, '')) + 1;
        $('#modelos #modelo_periodo #periodos > div').clone().attr('id', 'periodo_' + id).appendTo('#periodos').show().find('input, checkbox').each(function(index, element) {
            $(element).attr('name', $(element).attr('name').replace('X', id));
            $(element).attr('id', $(element).attr('id').replace('X', id));
        });
    },
    addEndereco: function(modelEndereco, modelProposta, etapa) {
        var contador = $('#enderecos .form-group').length + 1;

        $('#modelos #modelo_endereco .form-group').clone().appendTo('#enderecos').show().find('input, select').each(function(index, element) {
            $(element).attr('name', $(element).attr('name').replace('[X]', '[' + contador + ']'));
            $(element).attr('id', $(element).attr('id').replace('X', contador));
        });

        $('#enderecos .form-group:last a').bind('click', function() {
            proposta.buscaCEP(contador, modelEndereco, modelProposta);
        });

        $('#PropostaCredEndereco' + contador + 'CodigoEstadoEndereco').bind('change', function() {
            proposta.buscaCidade(this, null, 'PropostaCredEndereco' + contador + 'CodigoEstadoEndereco', 'PropostaCredEndereco' + contador + 'CodigoCidadeEndereco', null, null, contador);
        });

        $('#enderecos .form-group:last').find('#titulo_X').text('Filial');
        $('#enderecos .form-group:last').find('#titulo_X').removeAttr('id').attr('id', 'titulo_' + contador);
        $('#enderecos .form-group:last').find('#cnpj_loading_X').removeAttr('id').attr('id', 'cnpj_loading_' + contador);
        $('#enderecos .form-group:last').find('#pesquisa_cep_X').removeAttr('id').attr('id', 'pesquisa_cep_' + contador);
        $('#enderecos .form-group:last').find('#carregando_X').removeAttr('id').attr('id', 'carregando_' + contador);
        $('#enderecos .form-group:last').find('#cidade_combo_X').removeAttr('id').attr('id', 'cidade_combo_' + contador);
        $('#enderecos .form-group:last').find('#carregando_cidade_X').removeAttr('id').attr('id', 'carregando_cidade_' + contador);
        $('#enderecos .form-group:last').find('#link_X').removeAttr('id').attr('id', 'link_' + contador);
        $('#enderecos .form-group:last').attr('id', 'filial_' + contador);
        $('#endereco_filial').show();

        $('input[name="data[' + modelEndereco + '][' + contador + '][codigo_documento]"]').attr('class', 'form-control cnpj');
        $('input[name="data[' + modelEndereco + '][' + contador + '][codigo_documento]"]').bind('blur', function() {
            proposta.validaCNPJ(this, 0, etapa, '', contador);
        });

        setup_mascaras();
    },
    removeEndereco: function(id) {
        $('#enderecos .form-group').each(function(index, element) {
            if (parseInt($(element).attr('id').replace("filial_", "")) == parseInt(id.replace("link_", ""))) {
                $(element).fadeOut().remove();
            }
        });
    },
    buscaCEP: function(contador, modelEndereco, modelProposta) {
        var erCep = /^\d{5}-\d{3}$/;
        var cepCliente = $.trim($('#' + modelEndereco + contador + 'Cep').val());

        if (cepCliente != '') {
            cepCliente = cepCliente.replace('-', '');
            proposta.mostraCarregando(contador);

            if (cepCliente.length == 8) {
                $.ajax({
                    type: 'POST',
                    url: "/portal/enderecos/buscar_endereco_cep/" + cepCliente,
                    dataType: "json",
                    beforeSend: function() { $('#pesquisa_cep_' + contador).hide();
                        $('#carregando_' + contador).show(); },
                    success: function(json) {
                        if (json.VEndereco) {

                            proposta.completaEndereco(modelEndereco, json.VEndereco.endereco_tipo + ' ' + json.VEndereco.endereco_logradouro, json.VEndereco.endereco_bairro, json.VEndereco.endereco_codigo_cidade, json.VEndereco.endereco_cidade, json.VEndereco.endereco_estado, contador, null, null, null, null, null, modelProposta);
                            proposta.escondeCarregando(contador);
                        } else {
                            proposta.escondeCarregando(contador);
                            alert('Cep não encontrado, digite na mão!');
                        }
                    },
                    complete: function() { $('#carregando_' + contador).hide(); }
                });
            } else if (cepCliente.length > 0) {
                alert('cep inválido');
            }
        }
    },
    buscaCidade: function(estado, idEstado, idComboEstado, idComboCidade, cod_cidade, desc_cidade, contador) {

        if (!idEstado) {
            idEstado = $(estado).val();
        }

        $.ajax({
            type: 'POST',
            url: "/portal/enderecos/carrega_combo_cidade/" + idEstado,
            dataType: "html",
            beforeSend: function() {
                $('#cidade_combo_' + contador).hide();
                $('#' + idComboCidade).hide();
                $('#carregando_cidade_' + contador).show();
            },
            success: function(retorno) {
                $('#' + idComboCidade).html(retorno);

                if (desc_cidade) {

                    $('#' + idComboCidade + ' option').filter(function() {
                        $(this).each(function(index, value) {
                            $(value).context.text = proposta.replaceSpecialChars($(value).context.text);
                        });
                    });

                    $('#' + idComboCidade).val($('#' + idComboCidade + ' option').filter(function() {
                        return $(this).html().toUpperCase() == desc_cidade;
                    }).val());
                } else {
                    $('#' + idComboCidade).val(cod_cidade);
                }
            },
            complete: function() {
                $('#carregando_cidade_' + contador).hide();
                $('#cidade_combo_' + contador).show();
                $('#' + idComboCidade).show();
            }
        });
    },
    replaceSpecialChars: function(str) {
        str = str.replace(/[ÀÁÂÃÄÅ]/, "A");
        str = str.replace(/[àáâãäå]/, "a");
        str = str.replace(/[ÈÉÊË]/, "E");
        str = str.replace(/[Ç]/, "C");
        str = str.replace(/[ç]/, "c");
        return str;
    },
    validaCNPJ: function(element, redireciona, etapa, codigo_proposta, contador) {
        var cnpj = $(element).val().replace(/[^0-9]/g, '');

        if (cnpj.length == 14) {
            $.ajax({
                type: 'POST',
                url: "/portal/propostas_credenciamento/verifica_cnpj",
                data: "cnpj=" + cnpj + "&codigo=" + codigo_proposta,
                dataType: "json",
                beforeSend: function() {
                    $('#cnpj_loading_' + contador).fadeIn();
                    $('#link_auto_completar_cnpj').hide();
                },
                success: function(json) {
                    // ja esta cadastrado na base?
                    if (json.resultado) {
                        // status é: "Pré Cadastro" ?
                        if (parseInt(json.codigo_status) == 1) {
                            if (redireciona) {
                                $('#modal').modal('show');
                                $('#modal .modal-header').html('<b>CNPJ CADASTRADO</b>');
                                $('#modal .modal-body').html('<b>' + json.nome_fantasia + ',</b> você será encaminhado para próxima etapa do cadastro!');

                                // redireciona para completar cadastro
                                window.location = document.location.origin + "/portal/propostas_credenciamento/etapa2/" + json.codigo;
                            } else {
                                $('#modal').modal('show');
                                $('#modal .modal-header').html('<b>CNPJ JÁ CADASTRADO</b>');
                                $('#modal .modal-body').html('Este CNPJ já esta cadastrado em nosso sistema, não é permitido cadastro duplicado! <br /><br /> <a href="javascript:void(0);" class="btn btn-danger right" onclick="$(\'#modal\').modal(\'hide\');" style="margin-right: 5px;"><i class="icon-white icon-remove-sign"></i> Fechar</a><div class="clear"></div>');
                                $(element).val('');
                            }
                        } else {
                            $('#modal').modal('show');
                            $('#modal .modal-header').html('<b>CNPJ JÁ CADASTRADO</b>');

                            if (etapa != 'incluir')
                                $('#modal .modal-body').html('<b>' + json.nome_fantasia + ',</b> você já tem uma proposta cadastrada com status: <b>' + json.status_descricao + '</b> utilize os dados de login e senha enviado para seu e-mail, para se logar e dar sequência em seu cadastro! <br /><br /><br /> <a href="javascript:void(0);" onclick="$(\'#modal\').modal(\'hide\');" class="btn btn-success" class="btn btn-success"> Fechar </a>');
                            else
                                $('#modal .modal-body').html('A empresa: <b>' + json.nome_fantasia + ',</b> já tem uma proposta cadastrada com status: <b>' + json.status_descricao + '</b>, para visualizar a proposta <a href="/portal/propostas_credenciamento/editar/' + json.codigo + '" target="_blank">Clique Aqui</a><br /><br /><br /> <a href="javascript:void(0);" onclick="$(\'#modal\').modal(\'hide\');" class="btn btn-success" class="btn btn-success"> Fechar </a>');

                            $(element).val('');
                        }

                    } else {
                        if (json.valido) {
                            $('#link_auto_completar_cnpj').fadeIn();
                        } else {
                            $('#modal_BO').modal('show');
                            $('#modal_BO #msg_error').html('CNPJ Inválido');
                            $('#botao_fechar').remove();
                            $('#modal_BO .modal-body').append('<a href="javascript:void(0);" class="btn btn-success" onclick="$(\'#modal_BO\').modal(\'hide\'); $(\'#modal\').modal(\'hide\');" id="botao_fechar"><i class="icon-white icon-ok-sign"></i> Tentar Novamente </a>');
                        }
                    }
                },
                complete: function() {
                    $('#cnpj_loading_' + contador).hide();
                }
            });
        }
    },
    carregaCNPJ: function() {
        $('#modal').modal('hide');
        $('#modal_receita input[name="data[texto_captcha]"]').val('');
        $('#modal_receita').modal('show');
    },
    trocaCaptcha: function() {
        $('#troca_imagem').hide();
        $('#img_captcha').remove();
        $('#carregando_captcha').show();

        $('#modal_receita .modal-body').prepend('<img border="0" id="img_captcha" src="/portal/multi_empresas/getcaptcha?' + Math.random() + '">');

        $('#img_captcha').one('load', function() {
            $('#carregando_captcha').hide();
            $('#troca_imagem').show();
            $('input[name="data[texto_captcha]"]').val('');
        });
    },
    enviaCaptcha: function(element, contador, etapa) {

        if (etapa == 'incluir')
            var cnpj = $('input[name="data[PropostaSemValidacao][codigo_documento]"]').val().replace(/[^0-9]/g, '');
        else
            var cnpj = $('input[name="data[PropostaCredenciamento][codigo_documento]"]').val().replace(/[^0-9]/g, '');

        var captcha = $('input[name="data[texto_captcha]"]').val();

        $.ajax({
            type: 'POST',
            url: "/portal/propostas_credenciamento/retorno_receita",
            data: "cnpj=" + cnpj + "&captcha=" + captcha,
            dataType: "json",
            beforeSend: function() {
                $('#carregando_receita').show();
            },
            success: function(json) {

                if ($.trim(json.status) == 'OK') {
                    var modelProposta = ((etapa == 'etapa1') || (etapa == 'etapa2')) ? 'PropostaCredenciamento' : 'PropostaSemValidacao';
                    var modelEndereco = ((etapa == 'etapa1') || (etapa == 'etapa2')) ? 'PropostaCredEndereco' : 'PropostaCredEndereco2';

                    proposta.completaEndereco(modelEndereco, json[7], json[11], null, json[12], json[13], 0, json[8], json[10], json[2], json[3], json[14], modelProposta);

                    $('#modal_receita').modal('hide');
                    $('#carregando_receita').hide();
                } else {
                    if (contador == 0) {
                        $('#modal_receita').modal('show');
                        proposta.enviaCaptcha(element, 1, etapa);

                    } else {
                        $('#modal_receita').modal('hide');
                        $('#carregando_receita').hide();

                        $('#msg_error').html(json.status);
                        $('#modal_BO').modal('show');

                        $('#botao_fechar').remove();
                        $('#modal_BO .modal-body').append('<a href="javascript:void(0);" class="btn btn-success" onclick="proposta.tentarNovoCaptcha();" id="botao_fechar"><i class="icon-white icon-ok-sign"></i> Tentar Novamente </a>')
                    }
                }
            },
            complete: function() {
                if (contador == 1) {
                    $('#modal_receita').modal('hide');
                    $('#carregando_receita').hide();
                }
            }
        });

    },
    tentarNovoCaptcha: function() {
        $('#modal').modal('hide');
        $('#modal_BO').modal('hide');

        proposta.limpaCookies();
        proposta.trocaCaptcha();

        $('#troca_imagem').show();
        $('#modal_receita').modal('show');
    },
    limpaCookies: function() {
        $.post('/portal/propostas_credenciamento/limpa_cookie');
    },
    completaEndereco: function(ModelEndereco, logradouro, bairro, cod_cidade, desc_cidade, estado, contador, numero, cep, razao_social, nome_fantasia, email, ModelProposta) {
        idEstado = $('select[name="data[' + ModelEndereco + '][' + contador + '][codigo_estado_endereco]"] option').filter(function() { return $(this).html() == estado; }).val();
        if (estado != '********') {
            $('select[name="data[' + ModelEndereco + '][' + contador + '][codigo_estado_endereco]"]').val(idEstado).attr('readonly', 'readonly');
            if ($('select[name="data[' + ModelEndereco + '][' + contador + '][estado]"]') != null) {
                $('select[name="data[' + ModelEndereco + '][' + contador + '][estado]"]').val(estado).attr('readonly', 'readonly')
            }
        }
        if (logradouro != '********')
            $('input[name="data[' + ModelEndereco + '][' + contador + '][logradouro]"]').val(logradouro);

        if (bairro != '********')
            $('input[name="data[' + ModelEndereco + '][' + contador + '][bairro]"]').val(bairro).attr('readonly', 'readonly');;
        let input_cidade = $('input[name="data[' + ModelEndereco + '][' + contador + '][cidade]"]');
        if (input_cidade.is('select')) {
            if ($('#PropostaCredEndereco0CodigoEstadoEndereco').length) {
                var idComboCidade = 'PropostaCredEndereco' + contador + 'CodigoCidadeEndereco';
                var idComboEstado = 'PropostaCredEndereco' + contador + 'CodigoEstadoEndereco';
            } else {
                var idComboCidade = 'PropostaCredEndereco2' + contador + 'CodigoCidadeEndereco';
                var idComboEstado = 'PropostaCredEndereco2' + contador + 'CodigoEstadoEndereco';
            }

            proposta.buscaCidade(null, idEstado, idComboEstado, idComboCidade, cod_cidade, desc_cidade, contador);
        } else
        if (desc_cidade != '********') input_cidade.val(desc_cidade);

        input_cidade.attr('readonly', 'readonly')
        if ((cep != null) && (cep != '********'))
            $('input[name="data[' + ModelEndereco + '][' + contador + '][cep]"]').val(cep);

        if ((razao_social != null) && (razao_social != '********'))
            $('input[name="data[' + ModelProposta + '][razao_social]"]').val(razao_social);

        if ((nome_fantasia != null) && (nome_fantasia != '********'))
            $('input[name="data[' + ModelProposta + '][nome_fantasia]"]').val(nome_fantasia);

        if ((numero != null) && (numero != '********'))
            $('input[name="data[' + ModelEndereco + '][' + contador + '][numero]"]').val(numero);

    },
    escondeCarregando: function(contador) {
        $('#carregando_' + contador).hide();
    },
    mostraCarregando: function(contador) {
        $('#carregando_' + contador).show();
    },
    checkAll_Exames: function(element) {
        if ($(element).text() == 'Marcar Todos') {
            $('#modal_tabela_padrao input:checkbox').prop('checked', true);
            $(element).text('Desmarcar Todos');
        } else {
            $('#modal_tabela_padrao input:checkbox').prop('checked', false);
            $(element).text('Marcar Todos');
        }
    },
    addHorario: function() {
        // var qtd_horarios = parseInt($('#periodos_horario_diferenciado > table').last().attr('id').replace(/X/g, '')) + 1;
        var qtd_horarios = $('#periodos_horario_diferenciado > table').length;

        $('#modelos #horario_periodo #periodos_horario_diferenciado > table')
            .clone()
            // .last()
            .attr('id', 'horarioDif_' + qtd_horarios)
            .appendTo('#periodos_horario_diferenciado')
            .show()
            .find('input, checkbox, select')
            .each(function(index, element) {
                $(element).attr('name', $(element).attr('name').replace(/X/g, qtd_horarios));
                $(element).attr('id', $(element).attr('id').replace(/X/g, qtd_horarios));
            });
    },
    addHorarioEdit: function() {
        // var qtd_horarios = parseInt($('#periodos_horario_diferenciado > table').last().attr('id').replace(/X/g, '')) + 1;
        var qtd_horarios = $('#periodos_horario_diferenciado_edit > table').length;

        $('#modelos #horario_periodo_edit #periodos_horario_diferenciado_edit > table')
            .clone()
            // .last()
            .attr('id', 'horarioDif_' + qtd_horarios)
            .appendTo('#periodos_horario_diferenciado_edit')
            .show()
            .find('input, checkbox, select')
            .each(function(index, element) {
                $(element).attr('name', $(element).attr('name').replace(/X/g, qtd_horarios));
                $(element).attr('id', $(element).attr('id').replace(/X/g, qtd_horarios));
            });
    },
    addHorarioDiferenciadoEtapa2: function() {
        // var id = parseInt($('.form-group #periodos_horario_diferenciado > div').last().attr('id').replace(/[\a-z]+(\_)/g, '')) + 1;
        var id = $('.form-group #periodos_horario_diferenciado > div').length;
        $('#modelos #modelo_periodo_diferenciado #periodos_horario_diferenciado > div')
            .clone()
            .attr('id', 'horarioDif_' + id)
            .appendTo('#periodos_horario_diferenciado')
            .show()
            .find('input, checkbox, select')
            .each(function(index, element) {
                $(element).attr('name', $(element).attr('name').replace(/X/g, id));
                $(element).attr('id', $(element).attr('id').replace(/X/g, id));
            });
    },
}
$(document).ready(function() {
    $("[id$=Cep]").blur(function() {

        let name = $(this).attr('name')
        let regex = /\[([a-z0-9]+)\]\[([a-z0-9]+)\]\[([a-z0-9]+)\]/gi;
        let m = regex.exec(name);
        proposta.buscaCEP(m[2], m[1], m[3])

    }).blur();
});