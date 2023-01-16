<?php if($conditions == 'WHERE 1 = 0'): ?>
    <div class="alert">
        Favor informar o Loadplan ou o Início e Fim.
    </div>
<?php elseif($filtros['codigo_cliente'] != TLoadLoadplan::LG): ?>
<?php else: ?>
    <div class='row-fluid inline'>
        <h4>Loadplans</h4>
	    <div class='row-fluid' style='overflow-x:auto'>
        <table class='table table-striped horizontal-scroll' style = 'width:500' > 
            <thead >
                <tr>
                    <th class='numeric' class='input-medium'  title="Utilizados">Total</th>
                    <th class='numeric' class='input-medium'  title="Utilizados">Utilizados</th>
                    <th class='numeric' class='input-medium' title="Parcialmente Utilizados">Parcialmente Utilizados</th>
                    <th class='numeric' class='input-medium' title="Não Utilizados">Não Utilizados</th>
                </tr>

            </thead>
            <tbody >
                <tr>
                    <td class='numeric'><?= $dados[0][0]['load_total_utilizado'] + $dados[0][0]['load_total_parcialmente'] + $dados[0][0]['load_total_nao_utilizado'] ?></td>
                    <td class='numeric'><?= ((isset($dados[0][0]['load_total_utilizado']) && $dados[0][0]['load_total_utilizado'] > 0)?$this->Html->link($dados[0][0]['load_total_utilizado'],array('controller' => 'loadplans','action' => 'analitico',$loadplan_utilizado,rand()), array('target' => '_blank')):0) ?></td>
                    <td class='numeric'><?= ((isset($dados[0][0]['load_total_parcialmente']) && $dados[0][0]['load_total_parcialmente'] > 0)?$this->Html->link($dados[0][0]['load_total_parcialmente'],array('controller' => 'loadplans','action' => 'analitico',$loadplan_parcialmente_utilizado,rand()), array('target' => '_blank')):0)?></td>
                    <td class='numeric'><?= ((isset($dados[0][0]['load_total_nao_utilizado']) &&  $dados[0][0]['load_total_nao_utilizado'] > 0)?$this->Html->link($dados[0][0]['load_total_nao_utilizado'],array('controller' => 'loadplans','action' => 'analitico',$loadplan_nao_utilizado,rand()), array('target' => '_blank')):0)?></td>
                </tr>
            </tbody>
        </table>
    </div>
    </div>	
    <?php echo $this->Js->writeBuffer(); ?>
    <?php echo $this->Buonny->link_js('estatisticas') ?>
<?php endif; ?>
	