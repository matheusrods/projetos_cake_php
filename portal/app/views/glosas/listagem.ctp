<style>
    .td-detalhe{
        width:100%;
        overflow: scroll;
        max-height: 400px;
        font-size: 15px;
        background-color: #f5f5f5;
    }
    .input-date{
        width: 70px;
    }
    .input-date-label{
        font-size: 10px;
    }
</style>
<?php if(isset($glosas) && count($glosas)) : ?>

    <?= $paginator->options(array('update' => 'div.lista')); ?>

    <div class='well'>
        <?= $this->Html->link('<i class="cus-page-white-excel"></i>', array('controller' => $this->name, 'action' => $this->action, 'destino','export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-mini">Detalhe</th>
               <th class="input-medium">Número Nota Fiscal</th>
               <th class="input-medium">Código Prestador</th>
               <th class="input-medium">Razão Social Prestador</th>
               <th class="input-medium">CNPJ Prestador</th>
               <th class="input-medium">Nome Fantasia Prestador</th>
               <th class="input-medium">Quantidade de Glosas</th>
               <th class="input-medium">Valor Bruto NF</th>
               <th class="input-medium">Valor Glosado</th>
               
            </tr>
        </thead>
        <tbody>
            <?php foreach ($glosas as $key => $dados): ?>
            <tr>
                <td>
                    <a href="javascript:void(0);" id="expandir_<?= $key ?>" onclick="mostrar_itens(<?= $dados['NotaFiscalServico']['codigo'] ?>,<?= $key ?>, 'dadosGlosas');" title="Exibir Glosas"><i id="icone_<?= $key ?>" class="icon-plus"></i></a>
                </td>
                <td><?= $dados['NotaFiscalServico']['numero_nota_fiscal'] ?></td>
                <td><?= $dados['Fornecedor']['codigo'] ?></td>
                <td><?= $dados['Fornecedor']['razao_social'] ?></td>
                <td><?= Comum::formatarDocumento($dados['Fornecedor']['codigo_documento']) ?></td>
                <td><?= $dados['Fornecedor']['nome'] ?></td>
                <td><?= $dados[0]['qtd_glosas'] ?></td>
                <td><?= 'R$ '.$this->Ithealth->moeda($dados['NotaFiscalServico']['valor']) ?></td>
                <td><?= empty($dados[0]['total_glosado']) ? '<span> R$ 0,00</span>' : 'R$ '.$this->Ithealth->moeda($dados[0]['total_glosado']) ?></td>
            </tr>
            <tr id="detalhe_<?= $key ?>" class="hidden">
                <td colspan="9" class="td-detalhe">

                    <div class='row-fluid'>
                        <div class='span12'>
                            <h4>Número Nota Fiscal [<?= $dados['NotaFiscalServico']['numero_nota_fiscal']  ?>] - Detalhes</h4>
                            <div id="icone_carregar_<?= $key ?>" class="inline carregarGlosas" style=""></div>
                        </div>        
                    </div>

                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan="9"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['NotaFiscalServico']['count']; ?></td>
            </tr>
        </tfoot>    
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?= $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?= $this->Paginator->numbers(); ?>
            <?= $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span7'>
            <?= $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
    <?= $this->Js->writeBuffer(); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){

        $('[data-toggle=\"tooltip\"]').tooltip();

        mostrar_itens = function(codigo_nota,x,tabela) {

            //troca o icone
            if($('#icone_'+x).hasClass('icon-plus')) {

                $('#icone_'+x).removeClass('icon-plus');
                $('#icone_'+x).addClass('icon-minus');

                $('#detalhe_'+x).removeClass('hidden');

                $('#icone_carregar_'+x).show();

                carregamento = $('#carregado_'+x).val();
                
                if(carregamento != 1) {

                    $('#icone_carregar_'+x).show();

                    $.ajax({
                        type: 'GET',
                        url: '/portal/glosas/exibir_glosas/' + codigo_nota + '/' + tabela,
                        dataType: 'json',
                        beforeSend: function() {
                            $('#icone_carregar_'+x).html('<img src=\"/portal/img/Fountain.gif\">');
                        },
                        success: function(dados) {
                            if(dados) {

                                $('icone_carregar_'+x).html('');
                                $('#carregado_'+x).val('1');

                                var detalhes = '';

                                if(dados == 'erro'){
                                    detalhes += '<div class=\"alert\">Nenhum dado foi encontrado.</div>';
                                } else {

                                    $.each(dados, function(key, val){
                                        $.each(val, function(){

                                            if(tabela == 'dadosGlosas'){

                                                detalhes += '<div class=\"row-fluid \">';
                                                detalhes += '<div class=\"span12 \">';

                                                if(key == 0) {
                                                    detalhes += '<div class=\"control-group input text required \"><label >Glosa</label>';
                                                } else {
                                                    detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                                }
                                                detalhes += '<input name=\"\" value=\"'+this.codigo+'\" class=\"input-mini\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                                
                                                if(key == 0) {
                                                    detalhes += '<div class=\"control-group input text required \"><label class=\"input-date-label\" >Pedido Exame</label>';
                                                } else {
                                                    detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                                }

                                                detalhes += '<input name=\"\" value=\"'+this.codigo_pedidos_exames+'\" class=\"input-small input-date\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                                if(key == 0) {
                                                    detalhes += '<div class=\"control-group input text required \"><label >Exame</label>';
                                                } else {
                                                    detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                                }

                                                detalhes += '<input name=\"\" value=\"'+this.exame+'\" class=\"input-xlarge\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                                if(key == 0) {
                                                    detalhes += '<div class=\"control-group input text required \"><label >Valor</label>';
                                                } else {
                                                    detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                                }

                                                detalhes += '<input name=\"\" value=\"'+this.valor+'\" class=\"input-mini\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                                
                                                if(key == 0) {
                                                    detalhes += '<div class=\"control-group input text required \"><label class=\"input-date-label\">Data Glosa</label>';
                                                } else {
                                                    detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                                }

                                                detalhes += '<input name=\"\" value=\"'+this.data_glosa+'\" class=\"input-small input-date\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';
                                                
                                                if(key == 0) {
                                                    detalhes += '<div class=\"control-group input text required \"><label  class=\"input-date-label\">Data Vencimento</label>';
                                                } else {
                                                    detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                                }

                                                detalhes += '<input name=\"\" value=\"'+this.data_vencimento+'\" class=\"input-small input-date\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                                if(key == 0) {
                                                    detalhes += '<div class=\"control-group input text required \"><label class=\"input-date-label\">Data Pagamento</label>';
                                                } else {
                                                    detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                                }

                                                detalhes += '<input name=\"\" value=\"'+this.data_pagamento+'\" class=\"input-small input-date\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                                if(key == 0) {
                                                    detalhes += '<div class=\"control-group input text required \"><label >Status</label>';
                                                } else {
                                                    detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                                }

                                                detalhes += '<input name=\"\" value=\"'+this.status+'\" class=\"input-small\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';

                                                if(key == 0) {
                                                    detalhes += '<div class=\"control-group input text required \"><label >Observações</label>';
                                                } else {
                                                    detalhes += '<div class=\"control-group input text required\"><label >&nbsp;</label>';
                                                }

                                                detalhes += '<input name=\"\" value=\"'+this.motivo_glosa+'\" class=\"input-xlarge\" readonly=\"readonly\" id=\"\" type=\"text\"></div>';                                  

                                                detalhes += '</div>';
                                                detalhes += '</div>';
                                            }
                        
                                            detalhes += '<div class=\"clear\"></div>';
                                        });
                                    });

                                }

                                $('#icone_carregar_'+x).html(detalhes); 

                            } else {
                                swal({type: 'error', title: 'Houve um erro.', text: 'Houve um erro ao tentar carregar os dados da nota fiscal'});
                            }
                        },
                        complete: function(dados) {

                        }
                    });
                }//fim if
            } else {

                $('#icone_'+x).removeClass('icon-minus');
                $('#icone_'+x).addClass('icon-plus');

                $('#icone_carregar_'+x).hide();

                $('#detalhe_'+x).addClass('hidden');
            }
        }//fim mostrar_itens
    });", false);
?>