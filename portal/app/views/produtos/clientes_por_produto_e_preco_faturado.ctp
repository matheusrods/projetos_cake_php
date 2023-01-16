<?php if (!isset($clientes)): ?>
    <div class='well'>
        <?php echo $this->BForm->create('Produto', array('autocomplete' => 'off', 'url' => array('controller' => 'produtos', 'action' => 'clientes_por_produto_e_preco_faturado'))) ?>
            <div class="row-fluid inline">
                <?php echo $this->Buonny->input_periodo($this) ?>
                <?php echo $this->BForm->input('codigo', array('class' => 'input-large', 'label' => false, 'options' => $produtos, 'empty' => 'Selecione um produto')) ?>
                <?php echo $this->BForm->input('codigo_servico', array('class' => 'input-large', 'label' => false, 'options' => $servicos, 'empty' => 'Selecione um serviço')) ?>
                <?php echo $this->BForm->input('valor_unitario', array('class' => 'numeric moeda input-small', 'label' => false, 'placeholder' => 'Valor Unitário')) ?>
            </div>
            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
        <?php echo $this->BForm->end() ?>
    </div>
    <?php $this->addScript($this->Javascript->codeBlock("
        setup_mascaras();
        jQuery('#ProdutoCodigo').change(function() {
            combo = jQuery(this);
            jQuery.ajax({
                url: baseUrl + 'produtos_servicos/servicos_por_produto/'+combo.val()+'/'+Math.random(), 
                success: function(data){
                    jQuery('#ProdutoCodigoServico').html(data);
                }
            });
        });
    ")) ?>
<?php else: ?>
    <div class='well'>
        <strong>Período de: </strong><?= $this->data['Produto']['data_inicial']; ?><strong> até: </strong><?= $this->data['Produto']['data_final']; ?>
        <strong>Produto:</strong><?= $produto['Produto']['descricao'] ?> <strong>Serviço: </strong><?= $servico['Servico']['descricao'] ?> <strong>Valor Unitário: </strong><?= $this->data['Produto']['valor_unitario'] ?>
    </div>  
    <table class='table table-striped tablesorter'>
        <thead>
            <th><?= $this->Html->link('Código Pagador', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Cliente Pagador', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Código', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Cliente', 'javascript:void(0)') ?></th>
            <th class='numeric'><?= $this->Html->link('Qtd', 'javascript:void(0)') ?></th>
        </thead>
        <?php $total = 0 ?>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
                <?php $total += $cliente['0']['quantidade'] ?>
                <tr>
                    <td><?= $cliente['ClientePagador']['codigo'] ?></td>
                    <td><?= $cliente['ClientePagador']['razao_social'] ?></td>
                    <td><?= $cliente['Cliente']['codigo'] ?></td>
                    <td><?= $cliente['Cliente']['razao_social'] ?></td>
                    <td class='numeric'><?= $cliente['0']['quantidade'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class='numeric'><?= $total ?></td>
        </tfoot>
    </table>
    <?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
    <?php $this->addScript($this->Javascript->codeBlock("jQuery('table.table').tablesorter({sortList: [[0,1]],})")); ?>
<?php endif ?>