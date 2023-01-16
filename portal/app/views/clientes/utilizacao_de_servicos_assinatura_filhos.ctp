<style type="text/css">
.thead-prorata{
    background-color: #dbeaf9!important;
}    

.alinhar-icon-question{
    margin: 3px 0 0 5px;
    cursor: help;
}

.alinhar-icon-question-tfoot{
    float:left!important;
    cursor: help;
}
</style>

<div class='well'>
    <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?> - 
    <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?><br>
    <strong>Produto: </strong><?= isset($utilizacoes_assinatura[0][0]['produto']) ? $utilizacoes_assinatura[0][0]['produto'] : 'PACOTE PER CAPITA' ?>
</div>

<div class='row-fluid' style='overflow-x:auto'>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th class='input-small' colspan="3"><?= $this->Html->link('Serviço', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Quantidade', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Valor Assinatura', 'javascript:void(0)') ?></th>
                <th class='input-small numeric'><?= $this->Html->link('Valor', 'javascript:void(0)') ?></th>
            </tr>
        </thead>
        <?php if(isset($per_capita) && $per_capita['per_capita_parcial'] == 0): ?>
            <?php if ($utilizacoes_assinatura): ?>
                <tbody>
                    <?php if ($utilizacoes_assinatura): ?>
                        <?php foreach($utilizacoes_assinatura as $utilizacao): 
                            $codigo_produto         = $utilizacao[0]['codigo_produto'];
                            $codigo_cliente_pagador = $utilizacao[0]['codigo'];
                            $nome_fantasia          = $utilizacao[0]['nome_fantasia'];
                            $servico                = $utilizacao[0]['servico'];
                            $quantidade             = number_format($utilizacao[0]['quantidade'], 0);
                            $valor_assinatura       = $utilizacao[0]['valor'];
                            $valor                  = number_format($valor_assinatura * $quantidade, 2, ',', '.');
                            $valor_assinatura       = number_format($valor_assinatura , 2, ',', '.');

                            $servico = ($codigo_produto == 117) ? $servico . " - {$codigo_cliente_pagador} - {$nome_fantasia}" : $servico;
                        ?>
                            <tr>
                                <td colspan="3"><?= $servico ?></td>
                                <td class="numeric"><?= $quantidade ?></td>
                                <td class="numeric"><?= $valor_assinatura ?></td>
                                <td class="numeric"><?= $valor ?></td>
                            </tr>
                        <?php endforeach; //FINAL FOREACH $utilizacoes_assinatura ?>
                    <?php endif; //FINAL IF utilizacoes_assinatura ?>
                </tbody>
            <?php endif; //FINAL IF utilizacoes_assinatura ?>
        <?php endif; //FINAL IF per_capita &&  count($per_capita['per_capita_parcial']) MAIOR QUE ZERO ?>
        
        <?php if(isset($per_capita) && count($per_capita) > 0 && $per_capita['per_capita_parcial'] != 0): ?>
            <tbody>
                <?php 
                if(isset($per_capita['per_capita_parcial'])):
                        
                    $total_quantidade_parcial       = 0;
                    $total_valor_parcial            = 0;

                    foreach($per_capita['per_capita_parcial'] as $detalhe): ?>
                        <?php

                        $servico            = $detalhe['descricao'];
                        $codigo             = $detalhe['codigo'];
                        $nome_fantasia      = $detalhe['nome_fantasia'];
                        $quantidade         = $detalhe['qtd_vidas'];
                        $valor_assinatura   = $detalhe['valor_assinatura'];
                        $valor              = $detalhe['valor'];

                        $total_quantidade_parcial += $quantidade;
                        $total_valor_parcial += $valor;

                        ?>  
                        <tr>
                            <td colspan="3"><?= $servico ?> - <?= $codigo . ' - '. $nome_fantasia ?></td>
                            <td class="numeric"><?= $quantidade ?></td>
                            <td class="numeric"><?= number_format($valor_assinatura, 2,',','.'); ?></td>
                            <td class="numeric"><?= number_format($valor, 2,',','.'); ?></td>
                        <tr>
                    <?php endforeach; ?>
                    <?php if($total_quantidade_parcial > 0): ?>
                        <tr style="background-color: #c4c4c4;">
                            <td colspan="3">PACOTE PER CAPITA - TOTAL</td>
                            <td class="numeric calcula-total-vidas-destaque"><?= $total_quantidade_parcial ?></td>
                            <td class="numeric"> - </td>
                            <td class="numeric calcula-total-valor-assinatura-destaque calcula-total-valor-destaque">
                                <?= number_format($total_valor_parcial, 2,',','.'); ?>
                            </td>
                        <tr>  
                    <?php endif;?>
                <?php endif; ?> 

                <?php if((isset($per_capita['pro_rata'])) && (count($per_capita['pro_rata'])) > 0): ?>
                    <tr style="color: #08c;font-weight: bold;">
                        <td class="thead-prorata">
                            PACOTE PER CAPITA - PRO RATA
                        </td>
                        <td class="thead-prorata numeric">Dias Cobrados</td>
                        <td class="thead-prorata numeric">Valor Dia 
                                <i class="adjust-icon icon-question-sign alinhar-icon-question" data-toggle="tooltip" 
                                title="Para calcular o valor do dia é necessário fazer a seguinte conta: 
                                Quantidade de dias do mês referente dividido pelo Valor Assinatura                              Exemplo: 30 / 10,00 = 3,00
                                "></i>
                        </td>
                        <td class="thead-prorata numeric">Quantidade</td>
                        <td class="thead-prorata numeric">Valor Assinatura</td>
                        <td class="thead-prorata numeric">Valor
                            <i class="adjust-icon icon-question-sign alinhar-icon-question calcula-valor-pro-rata" data-toggle="tooltip" 
                                title="Para calcular o Valor é necessário fazer a seguinte conta: 
                                Dias Cobrados vezes Valor Dia. 
                                Exemplo: 4 * 3,00 = 12,00
                                "></i>
                        </td>
                    </tr>
                    <?php foreach($per_capita['pro_rata'] as $pro_rata): 

                            $codigo                 = $pro_rata['Cliente']['codigo'];
                            $nome_fantasia          = $pro_rata['Cliente']['nome_fantasia'];
                            $dias_cobrados          = $pro_rata['ItemPedidoAlocacao']['dias_cobrado'];
                            $valor_assinatura       = number_format($pro_rata['ItemPedidoAlocacao']['valor_assinatura'], 2, ',', '.');
                            $valor_dia_assinatura   = number_format($pro_rata['ItemPedidoAlocacao']['valor_dia_assinatura'], 2, ',', '.');
                            $valor_pro_rata         = number_format($pro_rata['ItemPedidoAlocacao']['valor_pro_rata'], 2, ',', '.');
                    ?>
                        <tr>
                            <td><?=$codigo?> - <?=$nome_fantasia?></td>
                            <td class="numeric calcula-valor-pro-rata-destaque"><?=$dias_cobrados?></td>
                            <td class="numeric calcula-valor-pro-rata-destaque"><?=$valor_dia_assinatura?></td>
                            <td class="numeric"> 1 </td>
                            <td class="numeric"><?= $valor_assinatura ?></td>
                            <td class="numeric calcula-valor-pro-rata-destaque"><?= $valor_pro_rata ?></td>
                        <tr>
                    <?php endforeach; //FINAL FOREACH $per_capita['pro_rata']?>
                <?php endif; //FINAL SE isset($per_capita['pro_rata']) && count($per_capita['pro_rata'])) > 0)?>

                <?php   if((isset($per_capita['pro_rata_total'])) && $per_capita['pro_rata_total']['qtd_vidas'] != 0): 
                            $qtd_vidas      = number_format($per_capita['pro_rata_total']['qtd_vidas'], 0);
                            $total_pro_rata = number_format($per_capita['pro_rata_total']['total_pro_rata'], 2, ',', '.');
                ?>
                    <tr style="background-color: #eee">
                        <td colspan="3">PACOTE PER CAPITA - PRO RATA - TOTAL</td>
                        <td class="numeric calcula-total-vidas-destaque"><?= $qtd_vidas ?></td>
                        <td class="numeric"> - </td>
                        <td class="numeric calcula-total-valor-destaque"><?= $total_pro_rata?></td>
                    <tr>
                <?php endif; //FINAL IF isset($per_capita['pro_rata_total'])?>
            </tbody>
            <?php   if((isset($per_capita['item_pedido'])) && $per_capita['item_pedido']['qtd_vidas'] != 0): ?> 
                <tfoot>
                    <tr style="color: #08c;font-weight: bold;">

                        <?php
                            $qtd_vidas          = $per_capita['item_pedido']['qtd_vidas'];
                            $valor_assinatura   = number_format($per_capita['item_pedido']['valor_assinatura'], 2, ',', '.');
                            $valor              = number_format($per_capita['item_pedido']['valor'], 2, ',', '.');
                        ?>

                        <td class='input-small' colspan="3">TOTAL</td>
                        <td class='input-small numeric'>
                            <i class="adjust-icon icon-question-sign alinhar-icon-question-tfoot calcula-total-vidas" data-toggle="tooltip" 
                                title="Soma Quantidade PACOTE PER CAPITA - TOTAL com Quantidade PACOTE PER CAPITA - PRO RATA - TOTAL">
                            </i>
                            <?= $qtd_vidas ?>
                        </td>
                        <td class='input-small numeric'>
                            <!-- <i class="adjust-icon icon-question-sign alinhar-icon-question-tfoot calcula-total-valor-assinatura" data-toggle="tooltip" 
                                title="Soma Valor PACOTE PER CAPITA - TOTAL com Valor Assinatura PACOTE PER CAPITA - PRO RATA - TOTAL">
                            </i> -->
                            <?= $valor_assinatura ?>
                        </td>
                        <td class='input-small numeric'>
                            <i class="adjust-icon icon-question-sign alinhar-icon-question-tfoot calcula-total-valor" data-toggle="tooltip" 
                                title="Soma Valor PACOTE PER CAPITA - TOTAL com Valor PACOTE PER CAPITA - PRO RATA - TOTAL">
                            </i>
                            <?= $valor ?>
                        </td>
                    </tr>

                </tfoot>
            <?php  endif;?> 
        </table>
        <?php endif; //FINAL SE isset($per_capita) && count($per_capita) > 0)?>
</div>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>

<script type="text/javascript">
    
    $(document).ready(function(){
        $('.calcula-valor-pro-rata').on('mouseover',function(){
            $('.calcula-valor-pro-rata-destaque').css({'background-color': '1px solid #f14c4c'});
        });


        $('.calcula-valor-pro-rata').on('mouseleave',function(){
            $('.calcula-valor-pro-rata-destaque').removeAttr('style');
        });


        $('.calcula-total-vidas').on('mouseover',function(){
            $('.calcula-total-vidas-destaque').css({'background-color': '1px solid #f14c4c'});
        });

        $('.calcula-total-vidas').on('mouseleave',function(){
            $('.calcula-total-vidas-destaque').removeAttr('style');
        }); 

        $('.calcula-total-valor-assinatura').on('mouseover',function(){
            $('.calcula-total-valor-assinatura-destaque').css({'background-color': '1px solid #f14c4c'});
        });

        $('.calcula-total-valor-assinatura').on('mouseleave',function(){
            $('.calcula-total-valor-assinatura-destaque').removeAttr('style');
        }); 

        $('.calcula-total-valor').on('mouseover',function(){
            $('.calcula-total-valor-destaque').css({'background-color': '1px solid #f14c4c'});
        });

        $('.calcula-total-valor').on('mouseleave',function(){
            $('.calcula-total-valor-destaque').removeAttr('style');
        });
    });

</script>