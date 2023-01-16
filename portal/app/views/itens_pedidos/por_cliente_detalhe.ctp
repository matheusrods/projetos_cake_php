<div class='well' style='display:none' >
    <?php echo $this->BForm->create('ItemPedido', array('autocomplete' => 'off', 'url' => array('controller' => 'itens_pedidos', 'action' => 'por_cliente_detalhe'))) ?>
    <div class="row-fluid inline">
        <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente_pagador', 'Pagador', false, 'ItemPedido') ?>
        <?php echo $this->BForm->input('mes_referencia', array('label' => false, 'placeholder' => 'Mês', 'class' => 'input-small', 'options' => $meses)) ?>
        <?php echo $this->BForm->input('ano_referencia', array('label' => false, 'placeholder' => 'Ano','class' => 'input-small', 'options' => $anos)) ?>
        <?php echo $this->BForm->input('codigo_produto', array('label' => false, 'placeholder' => 'Código produto','class' => 'input-small', 'type' => 'text')); ?>
    </div>
    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
    <?php echo $this->BForm->end();?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_datepicker();
    });', false);
?>

<?php if(isset($servico)): ?>
<div class="well">
        <strong>Servico:</strong> <?php echo $servico; ?>
    </div>
<?php endif; ?>

<?php if (isset($itens_pedidos)): ?>
    <table class='table table-striped tablesorter'>
        <thead>
            <th>                <?php echo $this->Html->link('Operação',          'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Taxa Bancária',     'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Prêmio Mínimo',     'javascript:void(0)') ?></th>
            <th>                <?php echo $this->Html->link('Quantidade',        'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Valor Utilizado',   'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Valor Faturamento', 'javascript:void(0)') ?></th>
        </thead>
        <tbody>
            <?php $total_utilizado   = 0 ?>
            <?php $total_faturamento = 0 ?>
            <?php $total_quantidade  = 0 ?>

            <?php foreach($itens_pedidos as $item_pedido): ?>
                <?php $operacao = ''; ?>
                <?php if (in_array($item_pedido['Produto']['codigo'], array(1,2))): ?>
                    <?php $operacao = $item_pedido['TipoOperacao']['descricao'] ?>
                <?php elseif ($item_pedido['Produto']['codigo'] == '30'): ?>
                    <?php $operacao = $item_pedido['TipoDocumento']['descricao'] ?>
                <?php endif ?>
                <tr>
                    <td><?= $operacao ?></td>
                    <td class='numeric'><?= $item_pedido['ItemPedido']['valor_taxa_bancaria'] ?></td>
                    <td class='numeric'><?= $this->Buonny->moeda($item_pedido['ItemPedido']['valor_premio_minimo']) ?></td>
                    <td class='numeric'><?= $item_pedido['ItemPedido']['quantidade'] ?></td>
                    <td class='numeric'><?= $this->Buonny->moeda($item_pedido['ItemPedido']['valor_utilizado']) ?></td>
                    <td class='numeric'><?= $this->Buonny->moeda($item_pedido['ItemPedido']['valor_faturamento']) ?></td>

                    <?php $total_utilizado   += $item_pedido['ItemPedido']['valor_utilizado'] ?>
                    <?php $total_faturamento += $item_pedido['ItemPedido']['valor_faturamento'] ?>
                    <?php $total_quantidade  += $item_pedido['ItemPedido']['quantidade'] ?>
                </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td class='numeric'><?= $total_quantidade ?></td>
                <td class='numeric'><?= $this->Buonny->moeda($total_utilizado) ?></td>
                <td class='numeric'><?= $this->Buonny->moeda($total_faturamento) ?></td>
            </tr>
        </tfoot>
<?php endif ?>

<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
          
        $.tablesorter.addParser({
            id: "brazil", 
            is: function(s) { 
                return false;
            },
            format: function(s) { 
               s = s.replace(/\./g,"");
               s = s.replace(/\,/g,".");
               return $.tablesorter.formatFloat(s.replace(new RegExp(/[^0-9.-]/g),""));
            }, 
            type: "numeric"
        });

        jQuery("table.table").tablesorter({
            headers: {
                2: {sorter: "brazil"}
            },
            widgets: ["zebra"]
        });
    });', false);
?>