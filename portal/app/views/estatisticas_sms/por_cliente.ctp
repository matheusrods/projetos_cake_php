<div class='well'>
	<?php echo $this->BForm->create('EstatisticaSm', array('autocomplete' => 'off', 'url' => array('controller' => 'estatisticas_sms', 'action' => 'por_cliente'))) ?>
    	<div class="row-fluid inline">
            <?php echo $this->BForm->input('tipo', array('class' => 'input-medium', 'label' => false, 'options' => $tipos)) ?>
            <?php echo $this->BForm->input('data', array('class' => 'data input-small', 'placeholder' => 'Data', 'label' => false, 'type' => 'text')) ?>
    	</div>
    	<?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $this->BForm->end() ?>  
</div>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php if (!empty($lista)): ?>
    <table class="table table-striped table-bordered tablesorter">
        <thead>
            <tr>
                <th><?= $this->Html->link('Código', 'javascript:void(0)') ?></th>
                <th title="Razão Soial"><?= $this->Html->link('Razão Social', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Operadores"><?= $this->Html->link('Operadores', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de SMs abertas"><?= $this->Html->link('SMs Abertas', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de SMs em Viagem"><?= $this->Html->link('SMs em Viagem', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Média de SMs em Viagem por Operador"><?= $this->Html->link('Média', 'javascript:void(0)') ?></th>
                <th class="numeric" title="Quantidade de Ocorrências"><?= $this->Html->link('Ocorrências', 'javascript:void(0)') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php $qtd_clientes = 0; ?>
            <?php $qtd_sm_aberta = 0; ?>
            <?php $qtd_sm_em_andamento = 0; ?>
            <?php $qtd_ocorrencias = 0; ?>
            <?php if ($lista): ?>
                <?php foreach ($lista as $operacao): ?>
                    <tr>
                        <td class="numeric"><?= $operacao['codigo_cliente'] ?></td>
                        <td><?= preg_replace('/^.*-\s+/', '', iconv('ISO-8859-1', 'UTF-8', $operacao['raz_social'])); ?></td>
                        <td class="numeric"><?= $operacao['operadores'] ?></td>
                        <td class="numeric"><?= $operacao['em_aberto'] ?></td>
                        <td class="numeric"><?= $operacao['em_andamento'] ?></td>
                        <td class="numeric"><?= $this->Buonny->moeda(round($operacao['em_andamento_por_operador'],2), array('edit' => true)) ?></td>
                        <td class="numeric"><?= $operacao['ocorrencias'] ?></td>
                    </tr>
                    <?php $qtd_clientes ++; ?>
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
            <td class="numeric"><?= $qtd_clientes ?></td>
            <td class="numeric"></td>
            <td class="numeric"><?= $qtd_sm_aberta ?></td>
            <td class="numeric"><?= $qtd_sm_em_andamento ?></td>
            <td class="numeric"></td>
            <td class="numeric"><?= $qtd_ocorrencias ?></td>
        </tfoot>
    </table>
    <?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
    <?php $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter()")) ?>
<?php endif; ?>