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

<?php if(isset($produto)): ?>
<div class="well">
    <strong>Cliente:</strong> <?php echo $cliente['Cliente']['codigo'] . ' - ' . $cliente['Cliente']['razao_social']; ?>
        <strong>Produto:</strong> <?php echo $produto; ?>
    </div>
<?php endif; ?>

<?php if (isset($itens_pedidos)): ?>
    <table class='table table-striped tablesorter'>
        <thead>
            <th>                <?php echo $this->Html->link('Serviço',           'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Taxa Bancária',     'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Prêmio Mínimo',     'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Quantidade',        'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Valor Utilizado',   'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Valor Faturamento', 'javascript:void(0)') ?></th>
        </thead>
        <tbody>
            <?php $total_utilizado   = 0 ?>
            <?php $total_faturamento = 0 ?>
            <?php $total_quantidade  = 0 ?>

            <?php foreach($itens_pedidos as $item_pedido): ?>
                <tr>
                    <?php if(($this->data['ItemPedido']['codigo_produto'] == 1) or ($this->data['ItemPedido']['codigo_produto'] == 2)): ?>
                          <td><?php echo $this->Html->link(iconv('ISO-8859-1', 'UTF-8', $item_pedido[0]['servico']),
                                     'javascript:void(0)',
                                      array( 'onclick' => "por_cliente_detalhe('ItemPedido',
                                                                               '{$this->data['ItemPedido']['codigo_cliente_pagador']}',
                                                                               '{$this->data['ItemPedido']['mes_referencia']}',
                                                                               '{$this->data['ItemPedido']['ano_referencia']}',
                                                                               '{$item_pedido[0]['codigo_produto']}',
                                                                               '{$item_pedido[0]['codigo_servico']}'
                                                                               )" )) ?></td>
                    <?php else: ?>
                          <td><?= iconv('ISO-8859-1', 'UTF-8', $item_pedido[0]['servico']) ?></td>
                    <?php endif; ?>

                    <td class='numeric'><?= $item_pedido[0]['valor_taxa_bancaria'] ?></td>
                    <td class='numeric'><?= $this->Buonny->moeda($item_pedido[0]['valor_premio_minimo']) ?></td>
                    <td class='numeric'><?= $item_pedido[0]['quantidade'] ?></td>

                    <td class='numeric'><?= $this->Buonny->moeda($item_pedido[0]['valor_utilizado']) ?></td>
                    <td class='numeric'><?= $this->Buonny->moeda($item_pedido[0]['valor_faturamento']) ?></td>


                    <?php $total_utilizado   += $item_pedido[0]['valor_utilizado'] ?>
                    <?php $total_faturamento += $item_pedido[0]['valor_faturamento'] ?>
                    <?php $total_quantidade  += $item_pedido[0]['quantidade'] ?>
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

<?php $this->addScript($this->Buonny->link_js('pedidos')) ?>
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