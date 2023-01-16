<?php if(!$isAjax){ ?>
<div class='well'>
    <div class="row-fluid inline">
        <?php echo $this->BForm->create('LogFaturamentoTeleconsult', array('autocomplete' => 'off', 'url' => array('controller' => 'fichas', 'action' => 'consolidar_relatorio_teleconsult'))) ?>
        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_pagador', 'Pagador') ?>
        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_utilizador', 'Utilizador') ?>
        <?php echo $this->Buonny->input_periodo($this) ?>
        <?php echo $this->BForm->input('codigo_servico', array('class' => 'input-large', 'options' => $codigo_servico, 'label' => false, 'empty' => $label_serv)); ?>
    </div>
    <?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $this->BForm->end() ?>
</div>
<?php } ?>

<?php if (!empty($dados)): ?>
    <div class="well">
        <strong>Data Inicial: </strong><?php echo $this->data['LogFaturamentoTeleconsult']['data_inicial']; ?>
        <strong>Data Final: </strong><?php echo $this->data['LogFaturamentoTeleconsult']['data_final']; ?>
        
        <?php if(!empty($this->data['LogFaturamentoTeleconsult']['codigo_cliente_pagador'])){ ?>
        <strong>Cliente Pagador: </strong><?php echo $dados[0]['ClientePagador']['razao_social']; ?> <strong>Código: </strong><?php echo $dados[0]['LogFaturamentoTeleconsult']['codigo_cliente_pagador']; ?> 
        <?php } ?>
        
        <?php if(!empty($this->data['LogFaturamentoTeleconsult']['codigo_cliente_utilizador'])){ ?>
        <strong>Cliente Utilizador: </strong><?php echo $dados[0]['Cliente']['razao_social']; ?> <strong>Código: </strong><?php echo $dados[0]['LogFaturamentoTeleconsult']['codigo_cliente']; ?>
        <?php } ?>
        
        <?php if(!empty($desc_servico)){ ?>
        <strong>Serviço: </strong><?php echo $desc_servico['Servico']['descricao']; ?>
        <?php } ?>
        
    </div>
    
    <table class='table table-striped tablesorter'>
        <thead class="head_table">
            <th><?= $this->Html->link('Serviço', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Tipo Operação', 'javascript:void(0)') ?></th>
            <th class="numeric"><?= $this->Html->link('Cód.', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Cliente Pagador', 'javascript:void(0)') ?></th>
            <th class="numeric"><?= $this->Html->link('Cód.', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Cliente Utilizador', 'javascript:void(0)') ?></th>
            <th class="numeric"><?= $this->Html->link('Quantidade', 'javascript:void(0)') ?></th>
        </thead>
        
        <tbody>
            <?php $total_quantidades = 0; ?>
            <?php foreach ($dados as $dado): ?>
                <tr>
                    <td><?= $dado['Servico']['descricao'] ?></td>
                    <td><?= $dado['TipoOperacao']['descricao'] ?></td>
                    <td class="numeric"><?= $dado['LogFaturamentoTeleconsult']['codigo_cliente_pagador'] ?></td>
                    <td><?= $dado['ClientePagador']['razao_social'] ?></td>
                    <td class="numeric"><?= $dado['LogFaturamentoTeleconsult']['codigo_cliente'] ?></td>
                    <td><?= $dado['Cliente']['razao_social'] ?></td>
                    <td class="numeric"><?= $dado[0]['quantidade'] ?></td>
                </tr>
                <?php $total_quantidades = $total_quantidades + $dado[0]['quantidade']; ?>
            <?php endforeach; ?>
        </tbody>
        
        <tfoot>
            <tr>
                <td colspan="7" class="numeric"><strong>Total:</strong> <?php echo $total_quantidades; ?></td>
            </tr>
        </tfoot>
    </table>
    
    <?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
    <?php $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter()")) ?>
    <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
    <?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
<?php endif ?>