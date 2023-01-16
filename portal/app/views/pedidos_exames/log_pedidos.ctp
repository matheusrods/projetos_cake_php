<?php if( isset($get_pedido) && !empty($get_pedido) ): ?>
<div class="row-fluid inline">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class="input-large" >Cod. Pedido Exame: <?php echo $get_pedido[0]['PedidoExame']['codigo'];?></th>
            </tr>
        </thead>
        <tbody>
            <?php if( !empty($get_pedido[0]['PedidoExame']['codigo']) && isset($get_pedido[0]['PedidoExame']['codigo'] )): ?>
                <tr>
                    <td>
                        Log do Pedido
                        <a href="#void" id="expandir_1" onclick="mostrar_itens(<?php echo $get_pedido[0]['PedidoExame']['codigo'];?>,'1','dadosPedidoExame');" ><i id="icone_1" class="icon-plus"></i></a>

                        <div id="icone_carregar_1" class="inline well" style="width:2902px;display:none;overflow:scroll;max-height: 400px;font-size: 15px;"></div>

                        <?php echo $this->BForm->input('carregado_1', array('type' => 'hidden', 'value' => '0')); ?>
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

        mostrar_itens = function(codigo_pedido,x,tabela) {

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
                    url: '/portal/pedidos_exames/get_log_pedidos/' + codigo_pedido + '/' + tabela,
                    dataType: 'json',
                    beforeSend: function() {
                        $('#icone_carregar_'+x).html('<img src=\"/portal/img/default.gif\">');
                    },
                    success: function(dados) {
                        if(dados) {
                            // console.log(dados);
                            $('icone_carregar_'+x).html('');
                            $('#carregado_'+x).val('1');

                            var detalhes = '';

                            if(dados == 'erro'){
                                detalhes += '<input name=\"\" value=\"Nenhum registro presente.\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';        
                                detalhes += '<div class=\"clear\"></div>';
                            } else {

                                $.each(dados, function(key, val){
                                    $.each(val, function(){

                                        if(tabela == 'dadosPedidoExame'){

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >FUNCIONÁRIO</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.nome_funcionario+'\" class=\"input-xlarge\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >DATA DO PEDIDO</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.data_inclusao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >BAIXA ULTIMO EXAME</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.data_baixa+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >USUÁRIO BAIXA</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.usuario_baixa+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >TIPO DO PEDIDO</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.tipo_pedido+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >STATUS</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.status+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >DATA ATUALIZAÇÃO</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.data_alteracao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >USUÁRIO ATUALIZAÇÃO</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.usuario_alteracao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >TIPO PERFIL</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.tipo_perfil+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >EMISSÃO DO PEDIDO</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.data_inclusao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >USUÁRIO EMISSÃO</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.usuario_emissao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >DATA NOTIFICAÇÃO</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.data_notificacao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >EMAIL CLIENTE</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.cliente_email+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >EMAIL CLÍNICA</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.clinica_email+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >EMAIL FUNCIONÁRIO</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.funcionario_email+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >AÇÃO SISTEMA</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }

                                            detalhes += '<input name=\"\" value=\"'+this.acao_sistema+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
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