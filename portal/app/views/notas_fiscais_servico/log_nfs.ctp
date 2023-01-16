<?php if( isset($dados) && !empty($dados) ): ?>
<div class="row-fluid inline">
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th >Num. Nota Fiscal</th>
                <th >CNPJ Fornecedor</th>
                <th >Fornecedor</th>
                <th >Fornecedor Razão Social</th>
                <th >Data Emissão</th>
                <th >Data Vencimento</th>
                <th >Data Pagamento</th>
                <th >Valor</th>
                <th >Chave Rastreamento</th>
                <th >Quantos dias</th>
                <th >Baixa Boleto Data</th>
                <th >Baixa Boleto Descrição</th>
                <th >Liberação Data</th>
                <th >Observação</th>
                <th >Status</th>
                <th >Tipo Recebimento</th>
                <th >Forma Pagamento</th>
                <th >Motivo Acrescimo</th>
                <th >Usuário Auditoria</th>
                <th >Tipo Serviço</th>
                <th >Motivo Desconto</th>
                <th >Usuário inclusão</th>
                <th >Data Inclusão</th>
                <th >Usuário Alteração</th>
                <th >Data Alteração </th>
                <th >Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php if( !empty($dados) && isset($dados )): ?>
                <?php foreach($dados AS $dado): ?>
                    <tr>
                        <td ><?php echo $dado['NotaFiscalServicoLog']['numero_nota_fiscal']; ?></td>
                        <td ><?php echo $dado['Fornecedor']['codigo_documento']; ?></td>
                        <td ><?php echo $dado['Fornecedor']['nome']; ?></td>
                        <td ><?php echo $dado['Fornecedor']['razao_social']; ?></td>
                        <td ><?php echo $dado['NotaFiscalServicoLog']['data_emissao']; ?></td>
                        <td ><?php echo $dado['NotaFiscalServicoLog']['data_vencimento']; ?></td>
                        <td ><?php echo $dado['NotaFiscalServicoLog']['data_pagamento']; ?></td>
                        <td ><?php echo $this->Buonny->moeda($dado['NotaFiscalServicoLog']['valor'], array('nozero' => true)); ?></td>
                        <td ><?php echo $dado['NotaFiscalServicoLog']['chave_rastreamento']; ?></td>
                        <td ><?php echo $dado['NotaFiscalServicoLog']['quantos_dias']; ?></td>
                        <td ><?php echo $dado['NotaFiscalServicoLog']['baixa_boleto_data']; ?></td>
                        <td ><?php echo $dado['NotaFiscalServicoLog']['baixa_boleto_descricao']; ?></td>
                        <td ><?php echo $dado['NotaFiscalServicoLog']['liberacao_data']; ?></td>
                        <td ><?php echo $dado['NotaFiscalServicoLog']['observacao']; ?></td>
                        <td ><?php echo $dado['NotaFiscalStatus']['descricao']; ?></td>
                        <td ><?php echo $dado['TipoRecebimento']['descricao']; ?></td>
                        <td ><?php echo $dado['FormaPagto']['descricao']; ?></td>
                        <td ><?php echo $dado['MotivoAcrescimo']['descricao']; ?></td>
                        <td ><?php echo $dado['UusuarioAud']['nome']; ?></td>
                        <td ><?php echo $dado['TipoServicoNfs']['descricao']; ?></td>
                        <td ><?php echo $dado['MotivoDesconto']['descricao']; ?></td>
                        <td ><?php echo $dado['UsuarioInc']['nome']; ?></td>
                        <td ><?php echo $dado['NotaFiscalServicoLog']['data_inclusao']; ?></td>
                        <td ><?php echo $dado['UsuarioAlt']['nome']; ?></td>
                        <td ><?php echo $dado['NotaFiscalServicoLog']['data_alteracao']; ?></td>
                        <td >
                            <?php 
                            $acao = "Inclusão";
                            if($dado['NotaFiscalServicoLog']['acao_sistema'] == "1") {
                                $acao = "Alteração";
                            }
                            else if($dado['NotaFiscalServicoLog']['acao_sistema'] == "2") {
                                $acao = "Exclusão";
                            }

                            echo $acao; 
                            ?>
                        </td>
                        
                    </tr>
                <?php endforeach ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
    <div class="alert">Nenhum registro encontrado</div>
<?php endif; ?>

<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){
        setup_mascaras();

        function mostrar_log(codigo_nfs,x,tabela) {

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
                    url: '/portal/nota_fiscal_servico/get_log_nfs/' + codigo_nfs + '/' + tabela,
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

                                        if(tabela == 'dadosNFS'){

                                            /*if(key == 0) {
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
                                        */
                                    });
                                });

                            }

                            $('#icone_carregar_'+x).html(detalhes); 

                        } else {
                            swal({type: 'error', title: 'Houve um erro.', text: 'Houve um erro ao tentar carregar os dados ds Nota Fiscal Serviço!'});
                        }
                    },
                    complete: function(dados) {

                    }
                });
            }//fim if
        }//fim mostrar_itens
    });", false);
?>