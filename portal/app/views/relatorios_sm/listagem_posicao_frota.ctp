
<?php 
if(!empty($dados) && count($dados) > 0): ?>
<div class="well">
    <div class="span3">
        <strong>Última atualização: </strong> <?php echo date('d/m/Y H:i:s') ?>
    </div>
</div>
    <div class="row-fluid">

        	<table class="table table-striped table-bordered veiculos">
        	    <thead>
                    <tr>
                        <th>STATUS</th>
                    <?php 
                        $colunas = array_keys($dados);
                        $linhas = array_keys($dados[$colunas[0]]);   
                        foreach($colunas as $coluna): 
                            echo '<th class="numeric">'.
                        $this->Html->link($coluna, 'javascript:void(0)').                
                        '</th>';
                        endforeach; ?>
                        <th class='numeric'><?php echo $this->Html->link('TOTAL', 'javascript:void(0)') ?></th>
                    </tr>                    
                </thead>
                <tbody>
                    <?php                         
                        foreach($linhas as $linha): 
                            echo '<tr><td>'.$linha.'</td>';
                            $total_linha = 0;
                            foreach($colunas as $key => $coluna): ?>

                                <td class="numeric">
<?php echo 
        $dados[$coluna][$linha] != 0 ? 
            $this->Html->link(
                $this->Buonny->moeda(
                    $dados[$coluna][$linha],
                    array('nozero'=>true, 'places'=>0)
                ), 
                array(
                    'controller'   => 'relatorios_sm',
                    'action'       => 'posicao_frota_analitico', 
                    'status'       => $linha, 
                    'situacao'     => $coluna,                     
                ), 
                array('onclick'=>'return open_popup(this);')
            ) 
        : ''; 
?>
                             
                                </td>
                            <?php
                                $total_linha += $dados[$coluna][$linha];
                                $total_coluna[$coluna] = empty($total_coluna[$coluna]) ? $dados[$coluna][$linha] : $total_coluna[$coluna]+$dados[$coluna][$linha];
                            endforeach;
echo '<td class="numeric">';
echo $total_linha != 0 ? 
         $this->Html->link(
            $this->Buonny->moeda(
                $total_linha,
                array('nozero'=>true, 'places'=>0)
            ), 
            array(
                'controller' => 'relatorios_sm',
                'action'     => 'posicao_frota_analitico',                 
                'status'     => $linha,                     
            ), 
            array('onclick'=>'return open_popup(this);')
        )
    : '';
echo '</td>' ;

                            echo '</tr>';
                        endforeach;  ?>
        	    </tbody>
                <tfoot>
                    <tr>
                        <td class="numeric"><b>TOTAL</b></td>
                    <?php
                        $total = 0;                        
                        foreach ($total_coluna as $key => $value) {
                            
echo '<td class="numeric">';
echo $value != 0 ? 
        $this->Html->link(
            $this->Buonny->moeda(
                $value,
                array('nozero'=>true, 'places'=>0)
            ), 
            array(
                'controller'   => 'relatorios_sm',
                'action'       => 'posicao_frota_analitico',                 
                'situacao'     => $key,                     
            ), 
            array('onclick'=>'return open_popup(this);')
        )
    : '';
echo '</td>' ;

                               
                            $total += $value;
                        }                        
                    ?>
                        <td class="numeric">
<?php
echo $total != 0 ? 
        $this->Html->link(
            $this->Buonny->moeda(
                $total,
                array('nozero'=>true, 'places'=>0)
            ), 
            array(
                'controller'   => 'relatorios_sm',
                'action'       => 'posicao_frota_analitico'
            ), 
            array('onclick'=>'return open_popup(this);')
        )
    : '';
?>
                        </td>
                    </tr>
                </tfoot>
        	</table>
        
    </div>
<?php else: ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php endif; 
echo $this->Javascript->codeBlock('
jQuery(document).ready(function(){ 
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
    jQuery("table.veiculos").tablesorter({
        headers: {
            1: {sorter: "qtd"},
            2: {sorter: "qtd"},
            3: {sorter: "qtd"},
            4: {sorter: "qtd"},
            5: {sorter: "qtd"}
        },
        widgets: ["zebra"]
    });
 });', false); 