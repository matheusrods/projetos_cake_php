<?php if( isset($dados) && !empty($dados) ): ?>
<div class="row-fluid inline">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class="input-large" >Pedido <?php echo $codigo_pedido?> - Item <?php echo $codigo_item_pedido?></th>
            </tr>
        </thead>
        <tbody>
            <?php if( !empty($dados['ItemPedidoExame']['codigo']) && isset($dados['ItemPedidoExame']['codigo'] )): ?>
                <tr>
                    <td>
                        Dados do Exame
                        <a href="#void" id="expandir_1" onclick="mostrar_itens(<?php echo $codigo_item_pedido;?>,'1','ItemPedidoExame');" ><i id="icone_1" class="icon-plus"></i></a>

                        <div id="icone_carregar_1" class="inline well" style="display:none;"></div>

                        <?php echo $this->BForm->input('carregado_1', array('type' => 'hidden', 'value' => '0')); ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if( !empty($dados['ItemPedidoExameBaixa']['codigo']) && isset($dados['ItemPedidoExameBaixa']['codigo'] )): ?>
                <tr>
                    <td>
                        Baixa do Exame
                        <a href="#void" id="expandir_2" onclick="mostrar_itens(<?php echo $codigo_item_pedido;?>,'2','ItemPedidoExameBaixa');" ><i id="icone_2" class="icon-plus"></i></a>

                        <div id="icone_carregar_2" class="inline well" style="display:none;"></div>

                        <?php echo $this->BForm->input('carregado_2', array('type' => 'hidden', 'value' => '0')); ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php 
            $Configuracao = &ClassRegistry::init('Configuracao');
            if( $dados['ItemPedidoExame']['codigo_exame'] == $Configuracao->getChave('INSERE_EXAME_CLINICO') && !empty($dados['FichaClinica']['codigo']) && isset($dados['FichaClinica']['codigo'] )): ?>
                <tr>
                    <td>
                        Dados da Ficha Clínica
                        <a href="#void" id="expandir_3" onclick="mostrar_itens(<?php echo $codigo_item_pedido;?>,'3','FichaClinica');" ><i id="icone_3" class="icon-plus"></i></a>

                        <div id="icone_carregar_3" class="inline well" style="display:none;"></div>

                        <?php echo $this->BForm->input('carregado_3', array('type' => 'hidden', 'value' => '0')); ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if( !empty($dados['ItemPedidoExame']['codigo']) && isset($dados['ItemPedidoExame']['codigo'] )): ?>
                <tr>
                    <td>
                        Anexos do Exame
                        <a href="#void" id="expandir_4" onclick="mostrar_itens(<?php echo $codigo_item_pedido;?>,'4','AnexoExame');" ><i id="icone_4" class="icon-plus"></i></a>

                        <div id="icone_carregar_4" class="inline well" style="display:none;"></div>

                        <?php echo $this->BForm->input('carregado_4', array('type' => 'hidden', 'value' => '0')); ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php 

            $Configuracao = &ClassRegistry::init('Configuracao');
            if( $dados['ItemPedidoExame']['codigo_exame'] == $Configuracao->getChave('INSERE_EXAME_CLINICO') && !empty($dados['FichaClinica']['codigo']) && isset($dados['FichaClinica']['codigo'] )): ?>
                <tr>
                    <td>
                        Anexos da Ficha Clínica
                        <a href="#void" id="expandir_5" onclick="mostrar_itens(<?php echo $codigo_item_pedido;?>,'5','AnexoFichaClinica');" ><i id="icone_5" class="icon-plus"></i></a>

                        <div id="icone_carregar_5" class="inline well" style="display:none;"></div>

                        <?php echo $this->BForm->input('carregado_5', array('type' => 'hidden', 'value' => '0')); ?>
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
                    url: '/portal/consultas_agendas/get_log_tabela/' + codigo_item + '/' + tabela,
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

                                        if(tabela == 'ItemPedidoExame'){
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Realização do Exame</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.data_realizacao_exame+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                           
                                           if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Comparecimento</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.compareceu+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Recebimento Digital</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.recebimento_digital+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Envio Recebimento</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.recebimento_enviado+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Usuário Alteração</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.nome_usuario+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Data Alteração</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.data_alteracao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Ação</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.acao_sistema+'\" class=\"input-small\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                        }

                                        if(tabela == 'ItemPedidoExameBaixa'){
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Realização da Baixa</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.data_realizacao_exame+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Resultado</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.resultado+'\" class=\"input-small\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Usuário Alteração</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.nome_usuario+'\" class=\"input-large\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Data Alteração</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.data_alteracao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Ação</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.acao_sistema+'\" class=\"input-small\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                        }

                                        if(tabela == 'FichaClinica'){
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Aptidão</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.parecer+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Usuário Alteração</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.nome_usuario+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Data Alteração</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.data_alteracao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Ação</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.acao_sistema+'\" class=\"input-small\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                        }

                                        if(tabela == 'AnexoExame'){
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Caminho do Arquivo</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.caminho_arquivo+'\" class=\"input-xlarge\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Usuário Alteração</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.nome_usuario+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Data Alteração</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.data_inclusao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>'; 

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Ação</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.acao_sistema+'\" class=\"input-small\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                        }

                                        if(tabela == 'AnexoFichaClinica'){
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Caminho do Arquivo</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.caminho_arquivo+'\" class=\"input-xlarge\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                            
                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Usuário Alteração</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.nome_usuario+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Data Alteração</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.data_inclusao+'\" class=\"input-medium\" readonly=\"readonly\" id=\"\" type=\"text\"></div>'; 

                                            if(key == 0) {
                                                detalhes += '<div class=\"control-group input text required \"><label >Ação</label>';
                                            } else {
                                                detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                            }
                                            detalhes += '<input name=\"\" value=\"'+this.acao_sistema+'\" class=\"input-small\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
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