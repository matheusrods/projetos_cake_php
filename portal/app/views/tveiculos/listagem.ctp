<?php 
if(empty($filtros)): ?>
    <div class="alert">
        Defina os critérios de filtros.
    </div>
<?php else: ?>
    <?php if (empty($listar)): ?>
        <div class="alert">
            Nenhum registro encontrado.
        </div>
    <?php else:
    $total          = 0;
    $total_com      = 0;
    $total_sem      = 0;
    $total_veiculos = 0;
     ?>

    <div class="row-fluid">
        <div class="span6" style="min-height: 200px">
            <h4>Agrupamento</h4>
            <div id="relatorio-agrupamento">
            </div>
        </div>
        <div class="span6" style="min-height: 200px">
            <h4>Total</h4>
            <div id="relatorio-total">
            </div>
        </div>
    </div>
    <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
    </br>
        <table class='table table-striped veiculos'>
            <thead>
                <tr>
                    <th><?= $this->Html->link($agrupamento_label, 'javascript:void(0)') ?></th>
                    <th class="numeric"><?= $this->Html->link('Veículos', 'javascript:void(0)') ?></th>
                    <th class='numeric'><?= $this->Html->link('Avarias', 'javascript:void(0)') ?></th>
                    <?php if(
                       $agrupamento_campo != 'avaria_local' && 
                       $agrupamento_campo != 'avaria_tipo'){ ?>
                    <th class='numeric'><?= $this->Html->link('Sem Avaria', 'javascript:void(0)') ?></th>
                    <th class='numeric'><?= $this->Html->link('Total', 'javascript:void(0)') ?></th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach($listar as $relatorio): 
                    $total          += $relatorio[0]['total'];
                    $total_sem      += $relatorio[0]['sem_avaria'] ;
                    $total_com      += $relatorio[0]['com_avaria'] ;
                    $total_veiculos += $relatorio[0]['veiculos'] ;
                ?>
                    <tr>
                        <td><?= (trim($relatorio[0]['agrupamento'])!='' ? $relatorio[0]['agrupamento'] : 'Não definido'); $agrup = trim($relatorio[0]['agrupamento']); ?></td>
                        <td class='numeric'><?= ($relatorio[0]['veiculos']>0) ? number_format($relatorio[0]['veiculos'],0,',','.') : '';  ?></td>
                        <td class='numeric'>
                            <?php                             
                            if($relatorio[0]['com_avaria'] > 0){
                                echo $this->Html->link(
                                number_format($relatorio[0]['com_avaria'],0,'','.'), 
                                array('action'=>'listagem_analitico', 
                                      'popup' , 
                                       $agrup ? $agrup : 'null' , 
                                       $agrupamento_campo,
                                       'null', 'null', 'com_avaria'
                                   ), 
                                array('onclick'=>'return open_popup(this);')); 
                            }
                            ?>
                        <?php if($agrupamento_campo != 'avaria_local' && $agrupamento_campo != 'avaria_tipo'){ ?>
                        
                            <?= $relatorio[0]['com_avaria'] > 0 ? '('.number_format($relatorio[0]['p_com_avaria'], 0,',','.').'%)' : ''; ?>
                        </td>
                        
                        <td class='numeric'>
                            <?php if($relatorio[0]['sem_avaria'] > 0){
                                echo $this->Html->link(
                                  number_format($relatorio[0]['sem_avaria'],0,'','.'), 
                                  array('action'=>'listagem_analitico', 
                                        'popup' , 
                                         $agrup ? $agrup : 'null' , 
                                         $agrupamento_campo,
                                         'null', 'null', 'sem_avaria'
                                     ), 
                                  array('onclick'=>'return open_popup(this);')); 
                              } ?>                            
                              <?= ($relatorio[0]['sem_avaria']>0) ? '('.number_format($relatorio[0]['p_sem_avaria'], 0,',','.').'%)' : ''; ?>
                        </td>
                        
                        <td class='numeric'>
                            <?php if($relatorio[0]['total'] > 0){                         
                                echo $this->Html->link(
                                number_format($relatorio[0]['total'],0,'','.'), 
                                array('action'=>'listagem_analitico', 
                                      'popup' , 
                                       $agrup ? $agrup : 'null' , 
                                       $agrupamento_campo,
                                       'null', 'null', 'null'
                                   ), 
                                array('onclick'=>'return open_popup(this);')); 
                            } ?>
                        </td>
                        <?php }else{ ?>
                            <?= '('.$relatorio[0]['percentual'] ?>%)</td>
                        <?php } ?>
                    </tr>
                <?php endforeach; ?>  
            </tbody>
            
            <tfoot>
                <tr>
                    <td><b>TOTAL</b></td>
                    <td class='numeric'><?=number_format($total_veiculos,0,',','.')?></td>
                    <td class='numeric'><b>
                        <?php    
                        if($total_com > 0){                         
                            echo $this->Html->link(
                            number_format($total_com,0,'','.'), 
                            array('action'=>'listagem_analitico', 'popup' , 'null' , 'null' ,'null','null','com_avaria'), 
                            array('onclick'=>'return open_popup(this);')); 
                        }
                        ?>
                    <?php if(
                         $agrupamento_campo != 'avaria_local' && 
                         $agrupamento_campo != 'avaria_tipo'){ ?>
                     <?= '('.number_format(round(100*$total_com/$total),0,',','.')?>%)</b></td>
                    <td class='numeric'><b>
                        <?php    
                        if($total_sem > 0){                         
                            echo $this->Html->link(
                            number_format($total_sem,0,'','.'), 
                            array('action'=>'listagem_analitico', 'popup' , 'null' , 'null' ,'null','null', 'sem_avaria'), 
                            array('onclick'=>'return open_popup(this);')); 
                        }
                        ?>
                   (<?= number_format(round(100*$total_sem/$total),0,',','.')?>%)</b></td>
                    <td class='numeric'><strong>
                        <?php 
                        if($total_com > 0){                         
                            echo $this->Html->link(
                                $total ? number_format($total,0,'','.') : '0', 
                                array('action'=>'listagem_analitico', 'popup', 'null', 'null', 'null', 'null', 'null', ), 
                                array('onclick'=>'return open_popup(this);')); 
                        }
                        ?></strong></td>

                    <?php }else{ ?>
                    (100%)</b></td>
                    <?php } ?>
                    
                </tr>
            </tfoot>
            
        </table>

        <?php 
        echo $this->Javascript->codeBlock('
            function carrgaListaTveiculosSinteticoAgrupamento() {
                var div = jQuery("#relatorio-agrupamento");
                bloquearDiv(div);
                div.load(baseUrl + "tveiculos/sintetico_tveiculos_agrupamento_grafico/" + Math.random());
            }
            function carrgaListaTveiculosSinteticoTotal() {
                var div = jQuery("#relatorio-total");
                bloquearDiv(div);
                div.load(baseUrl + "tveiculos/sintetico_tveiculos_total_grafico/" + Math.random());
            }
         
            jQuery(document).ready(function(){ 
                carrgaListaTveiculosSinteticoAgrupamento(); 
                carrgaListaTveiculosSinteticoTotal(); 
                $.tablesorter.addParser({
                    debug:true,
                    id: "qtd", 
                    is: function(s) { 
                        // return false so this parser is not auto detected 
                        // poderia ser detectado pelo simbolo do real R$
                        return false;
                    },
                    format: function(s) { 
                       return $.tablesorter.formatInt(s.replace(".", "").replace(new RegExp(/\(\d*\)/g),""));
                    }, 
                    type: "numeric"
                });
                $.tablesorter.addParser({
                    debug:true,
                    id: "valor", 
                    is: function(s) { 
                        // return false so this parser is not auto detected 
                        // poderia ser detectado pelo simbolo do real R$
                        return false;
                    },
                    format: function(s) { 
                       return $.tablesorter.formatInt(s.replace(".", ""));
                    }, 
                    type: "numeric"
                });
                
                jQuery("table.veiculos").tablesorter({
                    headers: {
                        1: {sorter: "valor"},
                        2: {sorter: "qtd"},
                        3: {sorter: "qtd"},
                        4: {sorter: "valor"}
                    },
                    widgets: ["zebra"]
                });
            });
        ', false); 
    endif; 
endif;