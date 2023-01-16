<?php if (empty($listar)): ?>
    <div class="alert">
        Nenhum registro encontrado.
    </div>
<?php else:
$total = 0;
$total_com = 0;
$total_sem = 0;
 ?>

<?php 
// if($grafico) {
//     $style = "style='width:100%;height:800px;float:left'" ;
// }
// else {
//     $style = "style='width:100%;height:450px;float:left'" ;
// }
?>


<!-- <div id="grafico" <?php echo $style ?> ></div> -->

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
    <table class='table table-striped pecas'>
        <thead>
            <tr>
                <th><?= $this->Html->link($agrupamento_label, 'javascript:void(0)') ?></th>
                <?php if($agrupamento_campo != 'tipo_peca_avaria'){ ?>
                <th class="numeric"><?= $this->Html->link('Sem Avaria', 'javascript:void(0)') ?></th>
                <?php } ?>
                <th class="numeric"><?= $this->Html->link('Com Avaria', 'javascript:void(0)') ?></th>
                <th class='numeric'><?= $this->Html->link('Total', 'javascript:void(0)') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($listar as $relatorio): 
                $total += $relatorio[0]['total'];
                $total_sem += $relatorio[0]['sem_avaria'] ;                
                $total_com += $relatorio[0]['com_avaria'] ;    
            ?>
                <tr>
                    <td><?php echo (trim($relatorio[0]['agrupamento'])!='' ? $relatorio[0]['agrupamento'] : 'Não definido'); ?></td>
                    <?php 
                    $agrup = trim($relatorio[0]['agrupamento']);
                    if($agrupamento_campo != 'tipo_peca_avaria'){ ?>
                    <td class="numeric">
                        <?php                         
                        if($relatorio[0]['sem_avaria'] > 0){
                            echo $this->Html->link(
                            number_format($relatorio[0]['sem_avaria'],0,'','.'), 
                            array('action'=>'listagem_analitico', 
                                  'popup' , 
                                   $agrup ? $agrup : 'null' , 
                                   $agrupamento_campo,
                                   'null',
                                   'null',
                                   'sem_avaria'
                               ), 
                            array('onclick'=>'return open_popup(this);')); 
                        }
                        ?>
                         <?= $relatorio[0]['sem_avaria'] > 0 ? '('.number_format($relatorio[0]['p_sem_avaria'], 0,',','.').'%)' : ''; ?>
                    </td>
                    <?php } ?>
                    <td class="numeric">
                        <?php                             
                        if($relatorio[0]['com_avaria'] > 0){
                            echo $this->Html->link(
                            number_format($relatorio[0]['com_avaria'],0,'','.'), 
                            array('action'=>'listagem_analitico', 
                                  'popup' , 
                                   $agrup ? $agrup : 'null' , 
                                   $agrupamento_campo,
                                   'null',
                                   'null',
                                   'com_avaria'
                               ), 
                            array('onclick'=>'return open_popup(this);')); 
                        }
                        ?>
                         <?= $relatorio[0]['com_avaria'] > 0 ? '('.number_format($relatorio[0]['p_com_avaria'], 0,',','.').'%)' : ''; ?>
                    </td>
                    <td class='numeric'>
                        <?php    
                        if($relatorio[0]['total'] > 0){                         
                            echo $this->Html->link(
                            number_format($relatorio[0]['total'],0,'','.'), 
                            array('action'=>'listagem_analitico', 
                                  'popup' , 
                                   $agrup ? $agrup : 'null' , 
                                   $agrupamento_campo,
                                   'null',
                                   'null',
                                   'null'
                               ), 
                            array('onclick'=>'return open_popup(this);')); 
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>  
        </tbody>
        
        <tfoot>
            <tr>
                <td><b>TOTAL</b></td>
                <?php if($agrupamento_campo != 'tipo_peca_avaria'){ ?>
                <td class="numeric"><b>
                     <?php    
                    if($total_sem > 0){                         
                        echo $this->Html->link(
                        number_format($total_sem,0,'','.'), 
                        array('action'=>'listagem_analitico', 
                              'popup' , 
                               'null' , 
                               'null' ,
                               'null',
                               'null',
                               'sem_avaria'
                           ), 
                        array('onclick'=>'return open_popup(this);')); 
                    }
                    ?>
                </b></td>
                <?php } ?>
                <td class="numeric"><b>
                    <?php    
                    if($total_com > 0){                         
                        echo $this->Html->link(
                        number_format($total_com,0,'','.'), 
                        array('action'=>'listagem_analitico', 
                              'popup' , 
                               'null' , 
                               'null' ,
                               'null',
                               'null',
                               'com_avaria'
                           ), 
                        array('onclick'=>'return open_popup(this);')); 
                    }
                    ?>
                </b></td>
                <td class='numeric'><strong>                
                    <?php 
                    if($total_com > 0){                         
                        echo $this->Html->link(
                            $total ? number_format($total,0,'','.') : '0', 
                            array('action'=>'listagem_analitico', 
                                'popup', 
                                'null', 
                                'null', 
                                'null', 
                                'null', 
                                'null', 
                            ), 
                            array('onclick'=>'return open_popup(this);')); 
                    }
                    ?>
                </strong></td>
            </tr>
        </tfoot>
        
    </table>
      <?php echo $this->Javascript->codeBlock('
        jQuery(document).ready(function(){ 
            carrgaListaTpecasSinteticoAgrupamento(); 
            carrgaListaTpecasSinteticoTotal(); 
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
            
            jQuery("table.pecas").tablesorter({
                headers: {
                    1: {sorter: "valor"},
                    2: {sorter: "qtd"},
                    3: {sorter: "qtd"},
                    4: {sorter: "valor"}
                },
                widgets: ["zebra"]
            });
        });
    ', false); ?>
<?php endif; ?>