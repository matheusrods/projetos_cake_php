<?php if ($tipo_busca ==2 || $tipo_busca == 1 ) { ?>
<div class='row-fluid' style='overflow-x:auto'>
    <table class="table table-striped table-bordered" style='width:100%;max-width:none'>
        <thead>
            <tr>               
        	<?php if(!empty($dados)): ?>		
                <th class='input-small numeric'><?php echo "Usuário" ?></th>                
                <?php for($i=1; $i<=31;$i++):?>
                <th class='input-small numeric'><?php echo str_pad($i, 2,'0',STR_PAD_LEFT);?></th>
    		    <?php endfor;?>    
           <?php endif ?>
                <th class='input-small numeric'>Total</th>
            </tr>
        </thead>
        <tbody>
        <?php if($dados): ?>
        <?php $total_dia = array();?>
        <?php   foreach($dados as $key => $dado ): ?>
            <tr>
                <td>
            <?php if ($tipo_busca==1): ?>
                <?php echo $dado[0]['usuario'] ?>
            <?php else:?>
                <?php echo $dado[0]['usuario'] ?>
            <?php endif; ?>
                </td>
                <?php $total = 0;?>                
                <?php for($i = 1; $i<=31;$i++):?>
                <?php $qtde  = (isset($dado[0][str_pad($i, 2,'0',STR_PAD_LEFT)]) ? $dado[0][str_pad($i, 2,'0',STR_PAD_LEFT)] : 0);?>
                <?php $total = ($total+$qtde);?>
                <?php $total_dia[$i] = !empty($total_dia[$i]) ? ($total_dia[$i]+$qtde) : $qtde;?>
                <td class='numeric'>
                        <?php if ($qtde): ?>
                            <?php if( $tipo_busca == 1 ): ?>
                                <?php echo $this->Html->link($qtde, array(
                                'controller'=>'fichas_scorecard', 
                                'action'    =>'estatisticas_relatorio_gerencial', $dado[0]['codigo_usuario'], $i ),array('onclick'=>"return open_popup(this,700,350);",'title'=>'Estatistica Relatório Gerencial'))?>
                            <?php else:?>
                                <?=$qtde?>
                            <?php endif;?>
                    <?php endif;?>
                </td>
                <?php endfor;?>
                <td class='numeric'>
                <?php if ($total): ?>
                    <?php if( $tipo_busca == 1 ): ?>
                        <?php echo $this->Html->link($total, array(
                            'controller'=> 'fichas_scorecard', 
                            'action'    => 'estatisticas_relatorio_gerencial', $dado[0]['codigo_usuario'], NULL
                        ),
                        array(
                            'onclick'=>"return open_popup(this,700,350);",
                            'title'=>'Estatistica Relatório Gerencial'
                        ));?>
                        <?php else:?>
                            <?=$total?>
                        <?php endif;?>                        
                <?php endif;?>
                </td>
            </tr>
        <?php endforeach ?>
        <?php endif;?>
            <tr>
                <thead>
                    <th class='input-small numeric'><?php echo "Total"; ?></th>
                    <?php for( $i = 1; $i<=31; $i++ ):?>
                        <td class='numeric'>
                            <?php if ( $total_dia[$i] ): ?>
                                <?php if( $tipo_busca == 1 ): ?>
                                    <?php echo $this->Html->link($total_dia[$i], array(
                                        'controller'=> 'fichas_scorecard', 
                                        'action'    => 'estatisticas_relatorio_gerencial', 0, $i
                                    ),
                                    array(
                                        'onclick'=>"return open_popup(this,700,350);",
                                        'title'=>'Estatistica Relatório Gerencial'
                                    ));?>
                                <?php else:?>
                                    <?=$total_dia[$i]?>
                                <?php endif;?>
                            <?php endif;?>
                        </td>
                    <?php endfor;?>
                    <th class='input-small numeric'>
                        <?php if( $tipo_busca == 1 ): ?>
                            <?php echo $this->Html->link(array_sum($total_dia), array(
                                'controller'=> 'fichas_scorecard', 
                                'action'    => 'estatisticas_relatorio_gerencial', 0, 0
                            ),
                            array(
                                'onclick'=>"return open_popup(this,700,350);",
                                'title'=>'Estatistica Relatório Gerencial'
                            ));?>
                            <?php else:?>
                                <?=array_sum($total_dia)?>
                            <?php endif;?>
                    </th>
                </thead>
            </tr>
        </tbody>
    </table>
</div>

<?php } ?>
<?php if ($tipo_busca ==3) { ?>
<div class='row-fluid' style='overflow-x:auto'>
    <table class="table table-striped table-bordered" style='width:100%;max-width:none'>
        <thead>
            <tr>
                <?php if(!empty($dados)): ?>
                    
                        <th class='input-small numeric'><?= $this->Html->link('Hora', 'javascript:void(0)') ?></th>
                        <th class='input-small numeric'><?php echo "Cadastro Carreteiro" ?></th>
                        <th class='input-small numeric'><?php echo "Cadastro Outros" ?></th>
                        <th class='input-small numeric'><?php echo "Atualização Carreteiro" ?></th>
                        <th class='input-small numeric'><?php echo "Atualização Outros" ?></th>
                        <th class='input-small numeric'><?php echo "Renovação Automática" ?></th>
                       
                <?php endif ?>
                <th class='input-small numeric'><?= $this->Html->link('Total', 'javascript:void(0)') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php 
                //debug($dados);
                    $qtd_total = 0 ;
                    $qtd_01 = 0 ;
                    $qtd_02 = 0 ;
                    $qtd_03 = 0 ;
                    $qtd_04 = 0 ;
                    $qtd_05 = 0 ;
                   
                if ($dados): ?>
                <?php foreach($dados as $dado): ?>
                    <?php 
                    //debug($dado);
                    $qtd_01 = $qtd_01 + $dado[0]['01'] ;
                    $qtd_02 = $qtd_02 + $dado[0]['02'] ;
                    $qtd_03 = $qtd_03 + $dado[0]['03'] ;
                    $qtd_04 = $qtd_04 + $dado[0]['04'] ;
                    $qtd_05 = $qtd_05 + $dado[0]['05'] ;
                   
                    $qtd_total_h = $dado[0]['01'] + $dado[0]['02'] + $dado[0]['03'] + $dado[0]['04'] + $dado[0]['05'] ; 

                    ?>

                    <tr>
                        <td class='numeric'><?=$dado[0]['Hora'] ;
                                 ?></td>
                        <td class='numeric'><?php if ($dado[0]['01']=='') { echo ' - '; }else{ echo $dado[0]['01'];  } ?></td>
                        <td class='numeric'><?php if ($dado[0]['02']=='') { echo ' - '; }else{ echo $dado[0]['02'];  } ?></td>
                        <td class='numeric'><?php if ($dado[0]['03']=='') { echo ' - '; }else{ echo $dado[0]['03'];  } ?></td>
                        <td class='numeric'><?php if ($dado[0]['04']=='') { echo ' - '; }else{ echo $dado[0]['04'];  } ?></td>
                        <td class='numeric'><?php if ($dado[0]['05']=='') { echo ' - '; }else{ echo $dado[0]['05'];  } ?></td>
                        
                         <td class='numeric'><?= $qtd_total_h ?></td>
                         
                    </tr>
                <?php endforeach ?>
                 <tr>
                    <thead>
                        <th class='input-small numeric'><?php echo "Total"; ?></th>
                        <th class='input-small numeric'><?php echo $qtd_01; ?></th>
                        <th class='input-small numeric'><?php echo $qtd_02; ?></th>
                        <th class='input-small numeric'><?php echo $qtd_03; ?></th>
                        <th class='input-small numeric'><?php echo $qtd_04; ?></th>
                        <th class='input-small numeric'><?php echo $qtd_05; ?></th>
                        
                        <?php $qtd_total = $qtd_01 +  $qtd_02 +  $qtd_03 +  $qtd_04 +  $qtd_05 ; ?>
                       <th class='input-small numeric'><?php echo $qtd_total; ?></th>
                    </thead>
                 </tr>   

            <?php endif ?>
        </tbody>
    </table>
</div>

<?php } ?>

<?php echo $this->Buonny->link_css('tablesorter') ?>
<?php echo $this->Buonny->link_js('jquery.tablesorter.min') ?>
<?= $this->addScript($this->Javascript->codeBlock("
    jQuery(document).ready(function() {
        jQuery('table.table').tablesorter({
            sortList: [[1,0],[0,1]],
        });
    })"
)) ?>