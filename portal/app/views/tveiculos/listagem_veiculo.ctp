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
    $total         = 0;
    $total_geral   = 0;
    $total_veiculo = 0;
    $totais        = array();
    switch ($tipo) {
        case 'tipo':
            $listagem_tipo = 'avaria_tipo';
            break;
        case 'local':
            $listagem_tipo = 'avaria_local';
            break;       
    }
    
     ?>
    <div class="row-fluid">        
        <h4>Agrupamento</h4>
        <div id="relatorio-veiculo-avaria"></div>        
    </div>
    <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
    </br>
    <?php
        $colunas = array();                     
        $linhas = array();   
        unset($listar['total']);
        foreach($listar as $veiculo => $avarias){
            if($tipo=='local'){
                unset($avarias['Sem Avaria']);
                unset($avarias[' ']);
            }
            unset($avarias['veiculos']);
            $avarias = array_keys($avarias);
            $colunas = array_merge($colunas, $avarias);
        }        

        $colunas = array_unique($colunas); 
    ?>
        <table class='table table-striped veiculos' <?php if(count($colunas) > 11){ ?> style="width: 1800px;" <?php } ?>>
            <thead>                        
                <tr>
                    <th><?= $this->Html->link('Veículos', 'javascript:void(0)') ?></th>
                    <th class="numeric"><?= $this->Html->link('Quantidade', 'javascript:void(0)') ?></th>
                    
                    <?php   
                        foreach($colunas as $coluna){
                            $totais[$coluna] = 0;
                            if(!strpos($coluna, '%')){
                    ?>
                    <th class="numeric">
                    <?= $this->Html->link($coluna, 'javascript:void(0)') ?>
                    </th>
                    <?php 
                    } } ?>
                    <th class="numeric"><?= $this->Html->link('Total', 'javascript:void(0)') ?></th>                    
                </tr>
            </thead>
            <tbody>                
                <?php 
                    $veiculos = array_keys($listar);
                    foreach($veiculos as $veiculo){ ?>
                    <tr><td><?php echo $veiculo ?></td>
                    <td class="numeric"><?php 
                        if($listar[$veiculo]['veiculos'] > 0)
                            echo number_format($listar[$veiculo]['veiculos'],0,',','.');
                        $total_veiculo += $listar[$veiculo]['veiculos'];
                    ?></td>
                        
                    <?php 
                        $total = 0;
                        foreach($colunas as $key=> $coluna){ 
                            $valor = !empty($listar[$veiculo][$coluna]) ? $listar[$veiculo][$coluna]: 0; 
                            $totais[$coluna] +=  $valor; 
                    ?>  
                            <?php   

                                if($valor > 0){
                                    if(strpos($coluna, '%') ){
                                        echo '('.$valor.'%) </td> ';
                                    }else{
                                        $total += $valor;
                                        if($key > 0)
                                            echo '</td>';

                                        echo '<td class="numeric">'.$this->Html->link(
                                            $valor, 
                                            array('action'=>'listagem_analitico', 
                                                  'popup' , 
                                                   $veiculo , 
                                                   'veiculo_tipo' ,
                                                   $tipo == 'total' ? 'null' : $coluna,
                                                   $tipo == 'total' ? 'null' : $listagem_tipo,
                                                   $tipo == 'total' ? $coluna == 'Sem Avaria' ? 'sem_avaria' : 'com_avaria' : 'null'
                                               ), 
                                            array('onclick'=>'return open_popup(this);')); 
                                    }
                                }else{
                                    if(strpos($coluna, '%')){
                                        echo ' </td> ';
                                    }else{
                                        if($key > 0)
                                            echo '</td>';
                                        echo '<td class="numeric">';
                                    }
                                }
                            ?>
                                                   
                    <?php } ?>  
                        </td>                 
                        <td class='numeric'>
                            <?php 
                            echo $this->Html->link(
                                number_format($total,0,'','.'), 
                                array(
                                    'action'=>'listagem_analitico', 
                                    'popup' , 
                                    trim($veiculo) ? $veiculo : 'null' , 
                                    'veiculo_tipo', 'null','null','null',
                                ), 
                                array('onclick'=>'return open_popup(this);')); ?>
                        </td>
                    </tr>                      
                <?php } ?>  
            </tbody>
            <tfoot>
                <tr>
                    <td><b>TOTAL</b></td>
                    <td class="numeric"><b><?php echo $total_veiculo ?></b></td>
                    <?php 
                        if($tipo == 'total'){
                            ?>                           
                                
                            <td class="numeric">
                            <?php if($totais['Avaria'] > 0){
                                    echo $this->Html->link(
                                        number_format($totais['Avaria'],0,'','.'), 
                                        array(
                                              'action'=>'listagem_analitico', 
                                              'popup', 'null', 'null','null', 'null',
                                              'com_avaria'
                                       ), 
                                       array('onclick'=>'return open_popup(this);')); 
                                }
                            ?>
                            (<?php
                             $valor = (100*$totais['Avaria'])/($totais['Sem Avaria'] + $totais['Avaria']);
                             echo number_format($valor,0,'','.').'%'; ?>)
                              </td><td class="numeric">
                            <?php if($totais['Sem Avaria'] > 0){
                                    echo $this->Html->link(
                                        number_format($totais['Sem Avaria'],0,'','.'), 
                                        array(
                                              'action'=>'listagem_analitico', 
                                              'popup','null','null','null','null',
                                              'sem_avaria'
                                       ), 
                                       array('onclick'=>'return open_popup(this);')); 
                                } ?>
                            (<?php
                                $valor = (100*$totais['Sem Avaria'])/($totais['Sem Avaria'] + $totais['Avaria']);
                                echo number_format($valor,0,'','.').'%'; ?>)</td>
                        <?php 
                            
                            $total_geral += $totais['Avaria']+$totais['Sem Avaria'];
                            
                        }else{
                        foreach($colunas as $coluna){ 
                            $total_geral += !empty($totais[$coluna]) ? $totais[$coluna] : 0;
                    ?>   
                            <td class="numeric">

                            <?php 
                                if($totais[$coluna] > 0){
                                    echo $this->Html->link(
                                        number_format($totais[$coluna],0,'','.'), 
                                        array(
                                              'action'=>'listagem_analitico', 
                                              'popup', 'null', 'null' ,
                                              $tipo == 'total' ? 'null' : $coluna,
                                              $tipo == 'total' ? 'null' : $listagem_tipo,
                                              $tipo == 'total' ? $coluna == 'Sem Avaria' ? 'sem_avaria' : 'com_avaria' : 'null'
                                       ), 
                                       array('onclick'=>'return open_popup(this);')); 
                                }

                            ?>
                            </td> 
                    <?php } 
                    }?>
                    <td class='numeric'><strong>
                        <?php 
                            if($total_geral > 0){
                                echo $this->Html->link(
                                    $total_geral ? number_format($total_geral,0,'','.') : '0', 
                                    array(
                                        'action'=>'listagem_analitico', 
                                        'popup', 'null', 'null', 'null', 'null', 'null',
                                    ), 
                                    array('onclick'=>'return open_popup(this);')); 
                            }
                        ?></strong></td>
                </tr>
            </tfoot>
            
        </table>

        <?php 
        $grafico = 'carrgaListaTveiculosVeicAvaria()';

        $js = '
            function carrgaListaTveiculosVeicAvaria() {
                var div = jQuery("#relatorio-veiculo-avaria");
                bloquearDiv(div);
                div.load(baseUrl + "tveiculos/tveiculos_avaria_grafico/" + Math.random());
            }
            jQuery(document).ready(function(){
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
                
                jQuery("table.veiculos").tablesorter({
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
            });', false); 
    endif; 
endif;