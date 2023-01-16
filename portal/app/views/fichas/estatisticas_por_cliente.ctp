<div class='well'>
    <div class="row-fluid inline">
        <?php echo $this->BForm->create('Ficha', array('autocomplete' => 'off', 'url' => array('controller' => 'fichas', 'action' => 'estatisticas_por_cliente'))) ?>
            <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false, 'Ficha') ?>
            <?php echo $this->BForm->input('tipo_cobranca', array('class' => 'input-medium', 'options' => array(1 => 'Somente Cobrados', 2 => 'Sem Cobrança'), 'label' => false, 'empty' => 'Todos')) ?>
            <?php echo $this->BForm->input('tipo_smonline', array('class' => 'input-medium', 'options' => array(1 => 'Somente SM Online', 2 => 'Sem SM Online'), 'label' => false, 'empty' => 'Todos')) ?>        
            <?php echo $this->Buonny->input_periodo($this) ?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $this->BForm->end() ?>
</div>
<?php if (!empty($dados)): ?>
    <div class="well">
        <strong>Data Inicial: </strong><?php echo $this->data['Ficha']['data_inicial']; ?>
        <strong>Data Final: </strong><?php echo $this->data['Ficha']['data_final']; ?>
        <strong>Cliente: </strong><?php echo $dados[0]['Cliente']['razao_social']; ?>
        <strong>Código: </strong><?php echo $dados[0]['LogFaturamentoTeleconsult']['codigo_cliente']; ?>
    </div>
    <div id="grafico" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
    <table class='table table-striped table-bordered tablesorter'>
        <thead class="head_table">
            <th><?= $this->Html->link('Produto', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Serviço', 'javascript:void(0)') ?></th>
            <th class="input-mini numeric"><?= $this->Html->link('Quantidade', 'javascript:void(0)') ?></th>
            <th class='action-icon'></th>
        </thead>
        <?php $total_quantidades = 0; ?>
        <tbody>
        <?php foreach ($dados as $dado): ?>
            <tr>
                <td><?= $dado['Produto']['descricao'] ?></td>
                <td><?= $dado['Servico']['descricao'] ?></td>
                <td class="input-mini numeric"><?= $dado[0]['quantidade'] ?></td>
                <td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-list-alt', 'title' => 'Relatório Consolidado', 'onclick' => "consolidado_teleconsult('{$this->data['Ficha']['codigo_cliente']}','{$this->data['Ficha']['data_inicial']}','{$this->data['Ficha']['data_final']}','{$dado['LogFaturamentoTeleconsult']['codigo_produto']}','{$dado['Servico']['codigo']}')")) ?></td>
            </tr>
            <?php $total_quantidades = $total_quantidades + $dado[0]['quantidade']; ?>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="numeric"><strong>Total:</strong> <?php echo $total_quantidades; ?></td>
            </tr>
        </tfoot>
    </table>
    <?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
    <?php $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter()")) ?>
    <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
    <?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
    <?php echo $this->Javascript->codeBlock($this->Highcharts->render($eixo_x, $series, array(
        'renderTo' => 'grafico',
        'chart' => array('type' => 'pie'),
    ))); ?>
<?php endif ?>
<?php $this->addScript($this->Buonny->link_js('fichas')) ?>