<?php if( isset($dados) && !empty($dados) ): ?>
<div class="row-fluid inline">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class="input-large" >Cod. Pedido Exame: <?php echo $cod_item_pedido['PedidoExame']['codigo'];?></th>
            </tr>
        </thead>
        <tbody>
            <?php if( !empty($dados['ItemPedidoExame']['codigo']) && isset($dados['ItemPedidoExame']['codigo'] )): ?>
                <tr>
                    <td>
                        Dados da Ficha Clínica
                        <a href="#void" id="expandir_1" onclick="mostrar_itens(<?php echo $cod_item_pedido['ItemPedidoExame']['codigo'];?>,'1','dadosFichaClinica');" ><i id="icone_1" class="icon-plus"></i></a>

                        <div id="icone_carregar_1" class="inline well" style="width:3657px;display:none;overflow:scroll;max-height: 400px;font-size: 15px;"></div>

                        <?php echo $this->BForm->input('carregado_1', array('type' => 'hidden', 'value' => '0')); ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if( !empty($dados['ItemPedidoExame']['codigo']) && isset($dados['ItemPedidoExame']['codigo'] )): ?>
                <tr>
                    <td>
                        Respostas da Ficha Clínica
                        <a href="#void" id="expandir_2" onclick="respostas_ficha_clinica(<?php echo $codigo_ficha;?>,'2','RespostasFichaClinica');" ><i id="icone_2" class="icon-plus"></i></a>

                        <div id="icone_carregar_2" class="inline well" style="width:1532px;display:none;overflow:scroll;max-height: 400px;font-size: 15px;"></div>

                        <?php echo $this->BForm->input('carregado_2', array('type' => 'hidden', 'value' => '0')); ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert">Nenhum registro encontrado</div>
<?php endif; ?>

<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){
        setup_mascaras();

        mostrar_itens = function(codigo_item,x,tabela) {

            //troca o icone
            if($('#icone_'+x).hasClass('icon-plus')) {

                $('#icone_'+x).removeClass('icon-plus');
                $('#icone_'+x).addClass('icon-minus');

                $('#icone_carregar_'+x).show();

            } else {

                $('#icone_'+x).removeClass('icon-minus');
                $('#icone_'+x).addClass('icon-plus');

                $('#icone_carregar_'+x).hide();
            }

            carregamento = $('#carregado_'+x).val();
            if(carregamento != 1) {

                $('#icone_carregar_'+x).show();

                $.ajax({
                    type: 'GET',
                    url: '/portal/fichas_clinicas/get_log_ficha_clinica/' + codigo_item + '/' + tabela,
                    dataType: 'json',
                    beforeSend: function() {
                        $('#icone_carregar_'+x).html('<img src=\"/portal/img/default.gif\">');
                    },
                    success: function(dados) {
                        if(dados) {
                            $('icone_carregar_'+x).html('');
                            $('#carregado_'+x).val('1');

                            var detalhes = '';

                            if(dados == 'erro'){
                                detalhes += '<input name=\"\" value=\"Nenhum registro presente.\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';        
                                detalhes += '<div class=\"clear\"></div>';
                            } else {

                                $.each(dados, function(key, val){
                                    $.each(val, function(){

                                        if(tabela == 'dadosFichaClinica'){

                                            //***codigo da ficha***
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Código</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.codigo_fichas_clinicas+'\" class=\"input-mini\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim do codigo da ficha

                                            //****codigo do pedido de exame****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Cód. Pedido de Exame</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.codigo_pedido_exame+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim do codigo_pedido_exame

                                            //****codigo do medico****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Código do Médico</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.codigo_medico+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim do codigo do medico

                                            //****nome do medico****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Código do Médico</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.nome_medico+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim nome do medico

                                            //****tipo conselho****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Tipo Conselho</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.tipo_conselho+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim tipo conselho

                                            //****numero conselho****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Número Conselho</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.numero_conselho+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim numero conselho

                                            //****uf conselho****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >UF Conselho</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.conselho_uf+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim uf conselho
                                            
                                            //****incluido por****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Incluído por</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.incluido_por+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim incluido por

                                            //****hora inicio atend****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Hora inicio atendimento</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.hora_inicio+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim hora inicio atend

                                            //****hora fim atend****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Horário Fim atendimento</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.hora_fim+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim hora fim atend

                                            //****Pá sistólica****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Pa sistólica</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.pa_sistolica+'\" class=\"input-mini\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim Pá sistólica

                                            //****Pá diastolica****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Pa diastólica</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.pa_diastolica+'\" class=\"input-mini\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim Pá diastolica

                                            //****Pulso****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Pulso</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.pulso+'\" class=\"input-mini\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim Pá Pulso

                                            //****circunferencia abdominal****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Circunferência Abdominal</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.circunferencia_abdominal+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim circunferencia abdominal

                                            //****circunferencia quadril****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Circunferência quadril</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.circunferencia_quadril+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim circunferencia quadril

                                            //****Peso****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Peso</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.peso+'\" class=\"input-mini\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim Peso

                                            //****Altura****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Altura</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.altura+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim Altura


                                            //****imc****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >IMC</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.imc+'\" class=\"input-mini\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim imc

                                             //****observacao****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Observação</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.observacao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim observacao

                                            //***parecer****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Aptidão</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.parecer+'\" class=\"input-mini\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim do parecer

                                            //***parecer_altura****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Parecer altura</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.parecer_altura+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim do parecer_altura

                                            //***parecer_espaco_confinado****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Parecer espaço confinado</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.parecer_espaco_confinado+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim do parecer_espaco_confinado

                                            //***Ativo****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Ativo</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.ativo+'\" class=\"input-mini\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim do Ativo
                                            
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Ação sistema</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.acao_sistema+'\" class=\"input-small\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Usuário Inclusão</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.usuario_inclusao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Usuário Alteração</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.usuario_alteracao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Data Alteração</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.data_alteracao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                        }
                    
                                        detalhes += '<div class=\"clear\"></div>';
                                    });
                                });

                            }

                            $('#icone_carregar_'+x).html(detalhes); 

                        } else {
                            swal({type: 'error', title: 'Houve um erro.', text: 'Houve um erro ao tentar carregar os dados do pedido!'});
                        }
                    },
                    complete: function(dados) {

                    }
                });
            }//fim if

        }//fim mostrar_itens

        respostas_ficha_clinica = function(codigo_ficha,x,tabela) {

            //troca o icone
            if($('#icone_'+x).hasClass('icon-plus')) {

                $('#icone_'+x).removeClass('icon-plus');
                $('#icone_'+x).addClass('icon-minus');

                $('#icone_carregar_'+x).show();

            } else {

                $('#icone_'+x).removeClass('icon-minus');
                $('#icone_'+x).addClass('icon-plus');

                $('#icone_carregar_'+x).hide();
            }

            carregamento = $('#carregado_'+x).val();
            if(carregamento != 1) {

                $('#icone_carregar_'+x).show();

                $.ajax({
                    type: 'GET',
                    url: '/portal/fichas_clinicas/get_respostas_log_ficha_clinica/' + codigo_ficha + '/' + tabela,
                    dataType: 'json',
                    beforeSend: function() {
                        $('#icone_carregar_'+x).html('<img src=\"/portal/img/default.gif\">');
                    },
                    success: function(dados) {
                        if(dados) {
                            $('icone_carregar_'+x).html('');
                            $('#carregado_'+x).val('1');

                            var detalhes = '';

                            if(dados == 'erro'){
                                detalhes += '<input name=\"\" value=\"Nenhum registro presente.\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';        
                                detalhes += '<div class=\"clear\"></div>';
                            } else {

                                $.each(dados, function(key, val){
                                    $.each(val, function(){

                                        if(tabela == 'RespostasFichaClinica'){

                                            //***codigo questao***
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Código</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.codigo_ficha_clinica_questao+'\" class=\"input-mini\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim codigo questao

                                            //***perguntas***
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Perguntas</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.pergunta+'\" class=\"input-xlarge\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim do codigo da ficha

                                            //****respostas****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Respostas</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.resposta+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim do respostas

                                            //****parentesco****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Parentesco</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.parentesco+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim do respostas

                                            //****usuario inclusao****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Usuário Inclusão</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.usuario_inclusao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim usuario inclusao

                                            //****usuario alteracao****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Usuário Alteração</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.usuario_alteracao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim usuario alteracao

                                            //****data inclusao****
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Data Alteração</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.data_inclusao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            //fim data inclusao
                                        }
                    
                                        detalhes += '<div class=\"clear\"></div>';
                                    });
                                });

                            }

                            $('#icone_carregar_'+x).html(detalhes); 

                        } else {
                            swal({type: 'error', title: 'Houve um erro.', text: 'Houve um erro ao tentar carregar os dados do pedido!'});
                        }
                    },
                    complete: function(dados) {

                    }
                });
            }//fim if

        }//fim mostrar_itens

    });", false);
?>