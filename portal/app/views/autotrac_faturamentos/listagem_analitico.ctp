<?php if (empty($listar)): ?>
    <div class="alert">
        Nenhum registro encontrado.
    </div>
<?php else: 
    echo $this->Paginator->options(array('update' => 'div.lista')); 

?>
<div class="well">
    <?php if(!empty($cliente)): ?>
        <strong>Período De: </strong><?= $periodo ?>        
    <?php endif; ?>
    <strong>Última atualização:</strong> <?php echo date('d/m/Y H:i:s') ?> 
    <span class="pull-right">        
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export', $cliente), array('escape' => false, 'title' =>'Exportar para Excel'));?>
    </span>
</div>
<br>
<div class='row-fluid'>
    <table class="table table-striped" style='table-layout:fixed' id="lista_veiculos"> 
        <thead>
            <tr>                
                <th class="input-small">
                    <?php echo $this->Paginator->sort('Terminal', 'AutotracFaturamento.numero_terminal') ?>
                </th>
                <th class="input-small">
                    <?php echo $this->Paginator->sort('Placa', 'AutotracFaturamento.placa') ?>
                </th>
                <th class="input-xxlarge">
                    <?php echo $this->Paginator->sort('Transportadora', 'Transportadora.razao_social') ?>
                </th>
                <th class="input-medium">
                <?php echo $this->Paginator->sort('Data', 'AutotracFaturamento.data_ultima_viagem') ?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Ass. Básica Qtd', 'AutotracFaturamento.ass_basica_quantidade') ?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Ass. Básica Vl.', 'AutotracFaturamento.ass_basica_valor') ?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Mensagem Qtd.', 'AutotracFaturamento.mensagem_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Mensagem Vl.', 'AutotracFaturamento.mensagem_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Caracter Qtd.', 'AutotracFaturamento.caracter_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Caracter Vl.', 'AutotracFaturamento.caracter_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Comando/Alerta Qtd.', 'AutotracFaturamento.comando_alerta_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Comando/Alerta Vl.', 'AutotracFaturamento.comando_alerta_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Caracter OBC Qtd.', 'AutotracFaturamento.caracter_obc_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Caracter OBC Vl.', 'AutotracFaturamento.caracter_obc_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Msg. Prioritária Qtd.', 'AutotracFaturamento.mensagem_prioritaria_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Msg. Prioritária Vl.', 'AutotracFaturamento.mensagem_prioritaria_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Macro Qtd.', 'AutotracFaturamento.macro_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Macro Vl.', 'AutotracFaturamento.macro_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Def. Grupo Qtd.', 'AutotracFaturamento.def_grupo_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Def. Grupo Vl.', 'AutotracFaturamento.def_grupo_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Alarm Pânico Qtd.', 'AutotracFaturamento.alarme_panico_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Alarm Pânico Vl.', 'AutotracFaturamento.alarme_panico_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Msg. Grupo Qtd.', 'AutotracFaturamento.mensagem_grupo_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Msg. Grupo Vl.', 'AutotracFaturamento.mensagem_grupo_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Prior. Grupo Qtd.', 'AutotracFaturamento.prior_grupo_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Prior. Grupo Vl.', 'AutotracFaturamento.prior_grupo_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Transf MCT Qtd.', 'AutotracFaturamento.transf_mct_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Transf MCT Vl.', 'AutotracFaturamento.transf_mct_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Desativa/Reat Qtd.', 'AutotracFaturamento.desativ_reat_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Desativa/Reat Vl.', 'AutotracFaturamento.desativ_reat_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('QMass Qtd.', 'AutotracFaturamento.qmass_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('QMass Vl.', 'AutotracFaturamento.qmass_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Macro AC Qtd.', 'AutotracFaturamento.macro_ac_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Macro AC Vl.', 'AutotracFaturamento.macro_ac_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('QTWEB Qtd.', 'AutotracFaturamento.qtweb_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('QTWEB Vl.', 'AutotracFaturamento.qtweb_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Perm. A.C. Qtd.', 'AutotracFaturamento.perm_ac_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Perm. A.C. Vl.', 'AutotracFaturamento.perm_ac_valor')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Inc./Exc. A.C. Qtd.', 'AutotracFaturamento.inc_exc_ac_quantidade')?>
                </th>
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Inc./Exc. A.C. Vl.', 'AutotracFaturamento.inc_exc_ac_valor')?>
                </th>
                <?php if(is_null($cliente)){ ?>
                    <th class="input-medium numeric">
                        <?php echo $this->Paginator->sort('Posição Adicional Qtd.', 'AutotracFaturamento.posicao_adicional_quantidade')?>
                    </th>
                    <th class="input-medium numeric">
                        <?php echo $this->Paginator->sort('Posição Adicional Vl.', 'AutotracFaturamento.posicao_adicional_valor')?>
                    </th>
                <?php } ?>
                <?php if(is_null($cliente)){ ?>
                    <th class="input-medium numeric">
                        <?php echo $this->Paginator->sort('Posição Buonny Qtd.', 'AutotracFaturamento.inc_exc_buonny_quantidade')?>
                    </th>
                    <th class="input-medium numeric">
                        <?php echo $this->Paginator->sort('Posição Buonny Vl.', 'AutotracFaturamento.inc_exc_buonny_valor')?>
                    </th>
                <?php }else { ?>
                    <th class="input-medium numeric">
                        <?php echo $this->Paginator->sort('Posição Adicional Qtd.', 'AutotracFaturamento.inc_exc_buonny_quantidade')?>
                    </th>
                    <th class="input-medium numeric">
                        <?php echo $this->Paginator->sort('Posição Adicional Vl.', 'AutotracFaturamento.inc_exc_buonny_valor')?>
                    </th>               
                
                <?php } ?>
                    
                <?php if(is_null($cliente)){ ?>
                    <th class="input-medium numeric">
                        <?php echo $this->Paginator->sort('Total', 'AutotracFaturamento.total')?>
                    </th>
                    <th class="input-medium numeric">
                        <?php echo $this->Paginator->sort('Total Buonny', 'AutotracFaturamento.total_buonny')?>
                    </th>
                <?php }else{ ?>
                    <th class="input-medium numeric">
                        <?php echo $this->Paginator->sort('Total', 'AutotracFaturamento.total_buonny')?>
                    </th>
                <?php } ?>
                
                <th class="input-medium numeric">
                    <?php echo $this->Paginator->sort('Total c/ Tribulos', 'AutotracFaturamento.total_tributo')?>
                </th>
                     
            </tr>
        </thead>
        <tbody>
            <?php 
            $total = 0;
            $total_geral = 0;
            $totais = array_fill(0, 43, 0);

            foreach($listar as $linha): 
                $total = 0;
                $relatorio = $linha['AutotracFaturamento'];
                $transportadora = $linha['Transportadora'];
            ?>
                <tr>                
<td><?= $relatorio['numero_terminal'] ?></td>
<td><?= $relatorio['placa'] ?></td>
<td><?= !empty($transportadora['codigo']) ? $transportadora['codigo'].' - '.$transportadora['razao_social'] : '' ?></td>
<td><?= $relatorio['data_ultima_viagem'];  ?></td>
<td class="numeric"><?= number_format($relatorio['ass_basica_quantidade'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['ass_basica_valor'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['mensagem_quantidade'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['mensagem_valor'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['caracter_quantidade'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['caracter_valor'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['comando_alerta_quantidade'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['comando_alerta_valor'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['caracter_obc_quantidade'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['caracter_obc_valor'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['mensagem_prioritaria_quantidade'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['mensagem_prioritaria_valor'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['macro_quantidade'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['macro_valor'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['def_grupo_quantidade'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['def_grupo_valor'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['alarme_panico_quantidade'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['alarme_panico_valor'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['mensagem_grupo_quantidade'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['mensagem_grupo_valor'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['prior_grupo_quantidade'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['prior_grupo_valor'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['transf_mct_quantidade'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['transf_mct_valor'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['desativ_reat_quantidade'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['desativ_reat_valor'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['qmass_quantidade'],2,',','.');  ?></td>
<td class="numeric"><?= number_format($relatorio['qmass_valor'],2,',','.');  ?></td>
<td class="numeric"><?= number_format($relatorio['macro_ac_quantidade'],2,',','.');  ?></td>
<td class="numeric"><?= number_format($relatorio['macro_ac_valor'],2,',','.');  ?></td>
<td class="numeric"><?= number_format($relatorio['qtweb_quantidade'],2,',','.');  ?></td>
<td class="numeric"><?= number_format($relatorio['qtweb_valor'],2,',','.');  ?></td>
<td class="numeric"><?= number_format($relatorio['perm_ac_quantidade'],2,',','.');  ?></td>
<td class="numeric"><?= number_format($relatorio['perm_ac_valor'],2,',','.');  ?></td>
<td class="numeric"><?= number_format($relatorio['inc_exc_ac_quantidade'],2,',','.');  ?></td>
<td class="numeric"><?= number_format($relatorio['inc_exc_ac_valor'],2,',','.');  ?></td>
<?php if(is_null($cliente)){ ?>
<td class="numeric"><?= number_format($relatorio['posicao_adicional_quantidade'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['posicao_adicional_valor'],2,',','.') ?></td>
<?php } ?>
<td class="numeric"><?= number_format($relatorio['inc_exc_buonny_quantidade'],2,',','.');  ?></td>
<td class="numeric"><?= number_format($relatorio['inc_exc_buonny_valor'],2,',','.');  ?></td>
<?php if(is_null($cliente)){ ?>
<td class="numeric"><?= number_format($relatorio['total'],2,',','.'); ?></td>
<?php } ?>
<td class="numeric"><?= number_format($relatorio['total_buonny'],2,',','.') ?></td>
<td class="numeric"><?= number_format($relatorio['total_tributo'],2,',','.')?></td>                    
<?php

                $totais[0]  += $relatorio['ass_basica_quantidade'];
                $totais[1]  += $relatorio['ass_basica_valor'];
                $totais[2]  += $relatorio['mensagem_quantidade'];
                $totais[3]  += $relatorio['mensagem_valor'];
                $totais[4]  += $relatorio['caracter_quantidade'];
                $totais[5]  += $relatorio['caracter_valor'];
                $totais[6]  += $relatorio['comando_alerta_quantidade'];
                $totais[7]  += $relatorio['comando_alerta_valor'];
                $totais[8]  += $relatorio['caracter_obc_quantidade'];
                $totais[9]  += $relatorio['caracter_obc_valor'];
                $totais[10] += $relatorio['mensagem_prioritaria_quantidade'];
                $totais[11] += $relatorio['mensagem_prioritaria_valor'];                
                $totais[12] += $relatorio['macro_quantidade'];
                $totais[13] += $relatorio['macro_valor'];
                $totais[14] += $relatorio['def_grupo_quantidade'];
                $totais[15] += $relatorio['def_grupo_valor'];
                $totais[16] += $relatorio['alarme_panico_quantidade'];
                $totais[17] += $relatorio['alarme_panico_valor'];
                $totais[18] += $relatorio['mensagem_grupo_quantidade'];
                $totais[19] += $relatorio['mensagem_grupo_valor'];
                $totais[20] += $relatorio['prior_grupo_quantidade'];
                $totais[21] += $relatorio['prior_grupo_valor'];
                $totais[22] += $relatorio['transf_mct_quantidade'];
                $totais[23] += $relatorio['transf_mct_valor'];
                $totais[24] += $relatorio['desativ_reat_quantidade'];
                $totais[25] += $relatorio['desativ_reat_valor'];
                $totais[26] += $relatorio['qmass_quantidade'];
                $totais[27] += $relatorio['qmass_valor'];
                $totais[28] += $relatorio['macro_ac_quantidade'];
                $totais[29] += $relatorio['macro_ac_valor'];
                $totais[30] += $relatorio['qtweb_quantidade'];
                $totais[31] += $relatorio['qtweb_valor'];
                $totais[32] += $relatorio['perm_ac_quantidade'];
                $totais[33] += $relatorio['perm_ac_valor'];
                $totais[34] += $relatorio['inc_exc_ac_quantidade'];
                $totais[35] += $relatorio['inc_exc_ac_valor'];
                if(is_null($cliente)){
                    $totais[36] += $relatorio['posicao_adicional_quantidade'];
                    $totais[37] += $relatorio['posicao_adicional_valor'];
                    $totais[38] += $relatorio['inc_exc_buonny_quantidade'];
                    $totais[39] += $relatorio['inc_exc_buonny_valor'];
                    $totais[40] += $relatorio['total'];
                    $totais[41] += $relatorio['total_buonny'];
                    $totais[42] += $relatorio['total_tributo'];
                }else{
                    $totais[36] += $relatorio['inc_exc_buonny_quantidade'];
                    $totais[37] += $relatorio['inc_exc_buonny_valor'];
                    $totais[38] += $relatorio['total_buonny'];
                    $totais[39] += $relatorio['total_tributo'];
                    unset($totais[40]);
                    unset($totais[41]);
                    unset($totais[42]);
                }
?>
                </tr>
            <?php endforeach; ?>  
        </tbody>
        
        <tfoot>
            <tr>        
                <th colspan="4">Totais</th>
                <?php foreach($totais as $tot){ ?>    
                    <th class="numeric"><?=number_format($tot,2,',','.') ?></th>
                <?php } ?>
            </tr>
            <tr>            
                <th colspan="47">Total de Registros: <?php echo $this->Paginator->params['paging']['AutotracFaturamento']['count']; ?></th>
            </tr>
        </tfoot>
        
    </table>

   <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
          <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span6'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        </div>
    </div>
    <?php echo $this->Buonny->link_css('jquery.tablescroll'); ?>
    <?php echo $this->Buonny->link_js('jquery.tablescroll'); ?>
    <?php 
    $tipo_view = 'popup';
    echo $this->Javascript->codeBlock("
        jQuery(document).ready(function(){
            $('.horizontal-scroll').tableScroll({width:3000, height:(window.innerHeight-".($tipo_view != 'popup' ? "380" : "220").")});

            $('.numbers a[id^=\"link\"]').bind('click', function (event) { bloquearDiv($('.lista')); });
        });", false);
    ?>
    <?php if($this->layout != 'new_window'): ?>
        <?php echo $this->Js->writeBuffer(); ?>
    <?php endif; ?>
<?php endif; ?>