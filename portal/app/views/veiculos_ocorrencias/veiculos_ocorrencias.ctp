<div class="form-ocorrencias_veiculos">
    <?= $this->element('/filtros/veiculos_ocorrencias2') ?>
</div>
<div class='veiculos-ocorrencias'>
</div>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php echo $this->Javascript->codeBlock('
    function deixar_pendente_ocorrencia_veiculo(codigo_ocorrencia) {
        bloquearDiv(jQuery(".ui-dialog"));
        jQuery.ajax({
            "url": baseUrl + "veiculos_ocorrencias/deixar_pendente/"+codigo_ocorrencia+"/"+Math.random(),
            "success": function(data) {
                close_dialog();
            }
        });
    }

    function testar_terminal() {
        $(".msg-teste-terminal").remove();
        $("#TTermTerminalTermNumeroTerminal").removeClass("form-error").parent().removeClass("error").find(".error-message").remove()
        bloquearDiv(jQuery(".ui-dialog"));
        jQuery.ajax({
            "url": baseUrl + "veiculos_ocorrencias/testar_terminal/"+$("#TVtecVersaoTecnologiaVtecCodigo").val()+"/"+$("#TTermTerminalTermNumeroTerminal").val()+"/"+Math.random(),
            "success": function(data) {
                data = $.parseJSON(data);
                if(data){
                    ultima = new Date(data.TUposUltimaPosicao.upos_data_comp_bordo_formatted);
                    hoje = new Date();
                    var msec = hoje - ultima;
                    var days = Math.floor(msec / 1000 / 60 / 60 / 24);
                    msec -= days * 1000 * 60 * 60 * 24;
                    var hh = Math.floor(msec / 1000 / 60 / 60);
                    msec -= hh * 1000 * 60 * 60;
                    var mm = Math.floor(msec / 1000 / 60);
                    msec -= mm * 1000 * 60;
                    var ss = Math.floor(msec / 1000);
                    msec -= ss * 1000;
                    if(data.status){
                        $("#TTermTerminalTermNumeroTerminal").parent().append("<div class=\"msg-teste-terminal\" style=\"color:#468847\"><strong>Última posição: </strong>"+data.TUposUltimaPosicao.upos_data_comp_bordo+"<BR>"+days+" dias, "+hh+" horas, "+mm+" minutos e "+ss+" segundos atrás.</div>");
                    }else{
                        $("#TTermTerminalTermNumeroTerminal").parent().addClass("error").append("<div class=\"msg-teste-terminal help-block error-message\">Terminal não possui posição nas últimas 5 horas<BR><strong>Última posição: </strong>"+days+" dias, "+hh+" horas, "+mm+" minutos e "+ss+" segundos atrás.</div>");
                    }
                }else{
                    $("#TTermTerminalTermNumeroTerminal").parent().addClass("error").append("<div class=\"msg-teste-terminal help-block error-message\">Terminal não encontrado</div>");
                }
                jQuery(".ui-dialog").unblock();
            }
        });
    }

    function sem_conta_ade(){
        $("#TVtecVersaoTecnologiaVtecCodigo").removeClass("form-error").parent().removeClass("error").find(".error-message").remove()
        $("#TTermTerminalTermNumeroTerminal").removeClass("form-error").parent().removeClass("error").find(".error-message").remove()

        ovei_codigo = $("#TOveiOcorrenciaVeiculoOveiCodigo").val();
        tecnologia = $("#TTecnTecnologiaTecnCodigo").val();
        versao_tecnologia = $("#TVtecVersaoTecnologiaVtecCodigo").val();
        numero_terminal = $("#TTermTerminalTermNumeroTerminal").val();
        erro = false;
        if(versao_tecnologia == ""){
            $("#TVtecVersaoTecnologiaVtecCodigo").parent().addClass("error").append("<div class=\"help-block error-message\">Informe a Versão da Tecnologia</div>");
            erro = true;
        }
        if(numero_terminal == ""){
            $("#TTermTerminalTermNumeroTerminal").parent().addClass("error").append("<div class=\"help-block error-message\">Informe a Versão da Tecnologia</div>");
            erro = true;
        }
        if(tecnologia == 8 && !erro){
            bloquearDiv(jQuery(".ui-dialog"));
            $.post(baseUrl + "veiculos_ocorrencias/sem_conta_ade/" + Math.random(),
                "data[TOveiOcorrenciaVeiculo][ovei_codigo]="+ovei_codigo+"&data[TTecnTecnologia][tecn_codigo]="+tecnologia+"&data[TVtecVersaoTecnologia][vtec_codigo]="+versao_tecnologia+"&data[TTermTerminal][term_numero_terminal]="+numero_terminal,
                function(data){
                    data = $.parseJSON(data);
                    if(data.sucesso){
                        close_dialog();
                    }else{
                        $("#TTermTerminalTermNumeroTerminal").parent().addClass("error").append("<div class=\"help-block error-message\">"+data.erro+"</div>");
                    }
                    jQuery(".ui-dialog").unblock();
                }
            );
        }
    }

    function enviar_rma(codigo_ocorrencia){
        bloquearDiv(jQuery(".ui-dialog"));
        jQuery.ajax({
            "url": baseUrl + "veiculos_ocorrencias/enviar_rma/"+codigo_ocorrencia+"/"+Math.random(),
            "success": function(data) {
                data = $.parseJSON(data);
                if(data){
                    close_dialog();
                }else{
                    jQuery(".ui-dialog").unblock();
                }
            }
        });
    }

    function close_dialog_ocorrencia_veiculo(){
        if(jQuery("#modal_dialog .alert-success").length > 0){
            close_dialog();
        }else{
            return false;
        }
    }

    function abrir_dialog(link,codigo_ocorrencia){
        $(".alert-error").remove();
        var result = false;
        jQuery.ajax({
            "url": baseUrl + "veiculos_ocorrencias/verificar_permissao_ocorrencia/"+codigo_ocorrencia+"/"+Math.random(),
            "async": false,
            "success": function(data){
                data = $.parseJSON(data);
                if((data === true) || data == null){
                    result = true;
                }else{
                    $(".veiculos-ocorrencias").prepend("<div class=\"alert alert-error\">Esta ocorrência já está sendo analisada pelo usuário " +data+"</div>");
                }

            }
        });

        if(result){
            return open_dialog(link, "Tratar Veículo sem Posição", 605,undefined,atualizaListaVeiculosOcorrencias2);
        }else{
            return false;
        }
    }
'); ?>