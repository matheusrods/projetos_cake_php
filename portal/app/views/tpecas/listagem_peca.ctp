<?php 

if(empty($filtros)): ?>
    <div class="alert">
        Defina os critérios de filtros.
    </div>
<?php else: 
     unset($listar['total']);
     if (empty($listar)): ?>
        <div class="alert">
            Nenhum registro encontrado.
        </div>
    <?php else:
    $total = 0;
    $total_geral = 0;
    $totais = array();
     ?>

    <div class="row-fluid">        
        <h4>Agrupamento</h4>
        <div id="relatorio-peca-avaria"></div>        
    </div>
    <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
    </br>
        <table class='table table-striped pecas'>
            <thead>                        
                <tr>
                    <th><?php echo $this->Html->link('Peça', 'javascript:void(0)') ?></th>

                    <?php   

                        $colunas = array();                     
                        $linhas = array();   
                        unset($listar['total']);
                        foreach($listar as $peca => $avarias){
                            //unset($avarias['Sem Avaria']);
                            //unset($avarias[' ']);
                            $avarias = array_keys($avarias);
                            $colunas = array_merge($colunas, $avarias);

                        }        

                        $colunas = array_unique($colunas); 

                        foreach($colunas as $coluna){
                            $totais[$coluna] = 0;
                            if(!strpos($coluna, '%')){
                    ?>
                    <th class="numeric"><?php echo $this->Html->link($coluna, 'javascript:void(0)') ?></th>
                        <?php } ?>
                    <?php } ?>
                    <th class="numeric"><?php echo $this->Html->link('Total', 'javascript:void(0)') ?></th>                    
                </tr>
            </thead>
            <tbody>                
                <?php 
                    $pecas = array_keys($listar);
                    foreach($pecas as $peca){ ?>
                    <tr><td><?php echo $peca ?></td>
                    <?php 
                        $total = 0;
                        foreach($colunas as $key => $coluna){ 
                            $valor = !empty($listar[$peca][$coluna]) ? $listar[$peca][$coluna] : 0; 
                            if(strpos($coluna, '%')){
                                echo '('.$valor.'%) </td> ';
                            }else{
                            $totais[$coluna] +=  $valor;
                            if($key > 0)
                                echo '</td>';

                    ?>  
                        <td class="numeric">
                            <?php 
                            $total += $valor; 
                            if($valor > 0){
                                echo $this->Html->link(
                                    number_format($valor,0,'','.'), 
                                    array('action'=>'listagem_analitico', 
                                          'popup' , 
                                           $peca , 
                                           'tipo_peca' ,
                                           $tipo == 'total' ? 'null' : $coluna,
                                           $tipo == 'total' ? 'null' : 'tipo_peca_avaria',
                                           $tipo == 'total' ? $coluna == 'Sem Avaria' ? 'sem_avaria' : 'com_avaria' : 'null'
                                       ), 
                                    array('onclick'=>'return open_popup(this);')); 
                            }
                            ?>
                        
                            
                    <?php } } ?>
                       <td class='numeric'>
                           <?php 
                            echo $this->Html->link(
                                number_format($total,0,'','.'), 
                                array(
                                    'action'=>'listagem_analitico', 
                                    'popup' , 
                                    trim($peca) ? $peca : 'null' , 
                                    'tipo_peca',
                                    'null',
                                    'null',
                                    'null',
                                ), 
                                array('onclick'=>'return open_popup(this);')); ?>
                       </td>
                    </tr>                        
                
                <?php } ?>  
            </tbody>
            
            <tfoot>
                <tr>
                    <td><b>TOTAL</b></td>
                    <?php 
                        foreach($colunas as $coluna){ 
                            $total_geral += !empty($totais[$coluna]) ? $totais[$coluna] : 0;
                             if(!strpos($coluna, '%')){
                    ?>   
                        <td class="numeric">
                            <?php 
                                if($totais[$coluna] > 0){
                                    echo $this->Html->link(
                                        number_format($totais[$coluna],0,'','.'), 
                                        array(
                                          'action'=>'listagem_analitico', 
                                          'popup' , 
                                          'null' , 
                                          'null' ,
                                          $tipo == 'total' ? 'null' : $coluna,
                                          $tipo == 'total' ? 'null' : 'tipo_peca_avaria',
                                          $tipo == 'total' ? $coluna == 'Sem Avaria' ? 'sem_avaria' : 'com_avaria' : 'null'
                                       ), 
                                       array('onclick'=>'return open_popup(this);')); 
                                }

                            ?>
                            
                        </td>                     
                    <?php } } ?>
                    <td class='numeric'><strong>
                        <?php 
                            if($total_geral > 0){
                                echo $this->Html->link(
                                    $total_geral ? number_format($total_geral,0,'','.') : '0', 
                                    array(
                                        'action'=>'listagem_analitico', 
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

        <?php 
        if($tipo=='tipo'){ 
            $grafico = 'carrgaListaTpecasPecaAvaria()';
        }else{
            $grafico = 'carrgaListaTpecasTotalAvaria()';
        }
        $js = 'jQuery(document).ready(function(){
            '.$grafico.';
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
                        ';
            foreach($colunas as $key => $coluna){
                $js.= ($key+1).': {sorter: "valor"}, 
                ';
            }
            $js.= ($key+2).': {sorter: "valor"}
            ';
            echo $this->Javascript->codeBlock($js.'
        },
                    widgets: ["zebra"]
                });
        });

        ', false); 
    endif; 
endif;