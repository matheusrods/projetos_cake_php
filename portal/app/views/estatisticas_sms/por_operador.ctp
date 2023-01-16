<div class='well'>
    <?php echo $this->BForm->create('EstatisticaSm', array('autocomplete' => 'off', 'url' => array('controller' => 'estatisticas_sms', 'action' => 'por_operador'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo_tipo_operacao', array('class' => 'input-medium', 'label' => false, 'options' => $operacoes, 'empty' => 'Todos')) ?>
            <?php echo $this->BForm->input('tipo', array('class' => 'input-medium', 'label' => false, 'options' => $tipos)) ?>
            <?php echo $this->BForm->input('data', array('class' => 'data input-small', 'placeholder' => 'Data', 'label' => false, 'type' => 'text')) ?>
            <?php echo $this->BForm->input('status', array('class' => 'input-medium', 'label' => false, 'options' => array('1' => 'Em aberto', '2' => 'Monitoradas'))) ?>
        </div>
        <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $this->BForm->end() ?>  
</div>

<?php
$qtd_operadores = 0;
$qtd_operacoes = 0;
$qtd_sm_aberta = 0;
$qtd_sm_em_andamento = 0;
$qtd_ocorrencias = 0;
?>
        
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php if (!empty($lista)): ?>
    <table class="table table-striped table-bordered tablesorter">
        <thead>
            <tr>
                <th><?= $this->Html->link('Logado', 'javascript:void(0)') ?></th>
                <th><?= $this->Html->link('Operador', 'javascript:void(0)') ?></th>
                <th><?= $this->Html->link('Função', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Operadores"><?= $this->Html->link('Operações', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de SMs abertas"><?= $this->Html->link('SMs Abertas', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de SMs Monitoradas"><?= $this->Html->link('SMs Monitoradas', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Ocorrências"><?= $this->Html->link('Ocorrências', 'javascript:void(0)') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($lista): ?>
                <?php foreach ($lista as $operacao): ?>        
            
                    <tr>
                        <td class="numeric"><?= $operacao['logado'] ?></td>
                        <td><?= iconv('ISO-8859-1', 'UTF-8', $operacao['operador']) ?></td>
                        <td><?= iconv('ISO-8859-1', 'UTF-8', $operacao['funcao']) ?></td>
                        
                        <td class="numeric">
                        <?= 
                                $this->Html->link($operacao['operacoes'], 'javascript:void(0)', 
                                array('onclick' => "sm_consulta_geral_por_operador_historico('{$this->data['EstatisticaSm']['codigo_tipo_operacao']}','{$operacao['codigo_operador']}', '*','{$this->data['EstatisticaSm']['data']}', '{$this->data['EstatisticaSm']['tipo']}')")) 
                        ?> 
                        </td>
                        <td class="numeric">
                        <?= $this->Html->link(
                                $operacao['em_aberto'], 'javascript:void(0)', 
                                array('onclick' => "sm_consulta_geral_por_operador_historico('{$this->data['EstatisticaSm']['codigo_tipo_operacao']}','{$operacao['codigo_operador']}', 0,'{$this->data['EstatisticaSm']['data']}', '{$this->data['EstatisticaSm']['tipo']}')")) 
                         ?>
                        </td>
                        <td class="numeric">                            
                            <?= $this->Html->link(
                                $operacao['em_andamento'], 'javascript:void(0)', 
                                array('onclick' => "sm_consulta_geral_por_operador_historico('{$this->data['EstatisticaSm']['codigo_tipo_operacao']}','{$operacao['codigo_operador']}', 1,'{$this->data['EstatisticaSm']['data']}', '{$this->data['EstatisticaSm']['tipo']}')")) 
                            ?>
                        </td>
                        <td class="numeric"><?= $operacao['ocorrencias'] ?></td>
                    </tr>
                    <?php $qtd_operadores ++; ?>
                    <?php $qtd_operacoes += $operacao['operacoes']; ?>
                    <?php $qtd_sm_aberta += $operacao['em_aberto']; ?>
                    <?php $qtd_sm_em_andamento += $operacao['em_andamento']; ?>
                    <?php $qtd_ocorrencias += $operacao['ocorrencias']; ?>
                <?php endforeach; ?>
            <?php else: ?>
                Sem dados para o dia selecionado
            <?php endif; ?>
        </tbody>
        <tfoot>
            <td></td>
            <td class="numeric"><?= $qtd_operadores ?></td>
            <td class="numeric"><?php //$qtd_operacoes ?></td>
            <td class="numeric"><?= $qtd_sm_aberta ?></td>
            <td class="numeric"><?= $qtd_sm_em_andamento ?></td>
            <td class="numeric"><?= $qtd_ocorrencias ?></td>
        </tfoot>
    </table>
    <?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
    <?php $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter()")) ?>
<?php endif; ?>