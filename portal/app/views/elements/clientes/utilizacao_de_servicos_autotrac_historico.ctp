<h5>Autotrac</h5>
<div class='row-fluid'>
    <table class="table table-striped table-bordered" style="width: 8900px;">
        <thead>
            <tr> 
                <th class="input-mini">Cód</th>
                <th class="input-xlarge">Razão Social</th>
                <th class="input-small numeric">Valor a Pagar</th>
                <th class="input-mini numeric">Tributo</th>
               <?php     
               
                foreach($servicos_autotrac as $servico){
                    $totais[$servico['Servico']['descricao']] = array('quantidade' => 0, 'valor' => 0);
                    echo '<th class="input-medium numeric">'.$servico['Servico']['descricao'].' Qtd.</th>';
                    echo '<th class="input-medium numeric">'.$servico['Servico']['descricao'].' Vl.</th>';
                }
               ?>         
                
            </tr>
        </thead>
        <tbody>
            <?php             
            $total_geral = 0;
            $total_tributo = 0;
            foreach($utilizacoes_autotrac as $key => $utiliza_auto){ 

                ?>
            <tr>
              <td><?=$key ?></td>
              <td><?=$utiliza_auto['nome'] ?></td>
              <td class="numeric"><?=
                 $this->Html->link(
                    number_format($utiliza_auto['valor_total'],2,',','.'),
                    array('controller' => 'autotrac_faturamentos', 
                        'action'=>'listagem_analitico', 'popup' , $key, $utiliza_auto['mes_referencia'], $utiliza_auto['ano_referencia']), 
                    array('onclick'=>'return open_popup(this);'));
                     ?></td>
              
              <?php 
                $total = 0;
                $html = '';
                foreach($servicos_autotrac as $servico){
                    $totais[$servico['Servico']['descricao']]['valor'] += $utiliza_auto[$servico['Servico']['codigo']]['valor'];
                    $totais[$servico['Servico']['descricao']]['quantidade'] += $utiliza_auto[$servico['Servico']['codigo']]['quantidade'];
                    $total += $utiliza_auto[$servico['Servico']['codigo']]['valor'];
               
                    $html .= '<td class="numeric">'.number_format($utiliza_auto[$servico['Servico']['codigo']]['quantidade'],2,',','.').'</td>';
                    $html .= '<td class="numeric">'.number_format($utiliza_auto[$servico['Servico']['codigo']]['valor'], 2, ',', '.').'</td>';
                } 

                $total_geral += $utiliza_auto['valor_total'];
                $total_tributo += $utiliza_auto['valor_total']-$total;

                ?>
              <td class="numeric"><?=number_format($utiliza_auto['valor_total']-$total,2,',','.'); ?></td>
              <?=$html ?>
            </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td class="numeric"><?=count($utilizacoes_autotrac)?></td>
                <td class="numeric"></td>
                <td class="numeric"><?=number_format($total_geral, 2,',', '.')?></td>
                <td class="numeric"><?=number_format($total_tributo,2,',','.')?></td>
                <?php foreach($totais as $tot){ ?>
                    <td class="numeric"><?=number_format($tot['quantidade'], 2, ',','.') ?></td>
                    <td class="numeric"><?=number_format($tot['valor'], 2, ',','.') ?></td>
                <?php } ?>
            </tr>
        </tfoot>
    </table>
</div>