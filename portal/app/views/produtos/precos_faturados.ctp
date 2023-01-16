<div class='well'>
    <?php echo $this->BForm->create('Produto', array('autocomplete' => 'off', 'url' => array('controller' => 'produtos', 'action' => 'precos_faturados'))) ?>
        <div class="row-fluid inline">
            <?php echo $this->Buonny->input_periodo($this) ?>
            <?php echo $this->BForm->input('codigo', array('class' => 'input-large', 'label' => false, 'options' => $produtos)) ?>
        </div>
        <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
    <?php echo $this->BForm->end() ?>
</div>
<?php if (isset($faturamentos)): ?>
    <table class='table table-striped'>
        <thead>
            <th>Produto</th>
            <th>Servico</th>
            <th class='numeric'>Valor</th>
            <th class='numeric'>Qtd Clientes</th>
            <th class='numeric'>Qtd Utilizado</th>
            <th class='action-icon'></th>
        </thead>
        <?php foreach ($faturamentos as $fatura): ?>
            <tr>
                <td><?= $fatura['Produto']['descricao'] ?></td>
                <td><?= $fatura['Servico']['descricao'] ?></td>
                <td class='numeric'><?= $this->Buonny->moeda($fatura['LogFaturamento']['valor']) ?></td>
                <td class='numeric'><?= $fatura['0']['qtd_clientes'] ?></td>
                <td class='numeric'><?= $fatura['0']['qtd_utilizado'] ?></td>
                <td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "clientes_por_produto_e_preco('{$this->data['Produto']['data_inicial']}', '{$this->data['Produto']['data_final']}', '{$fatura['Produto']['codigo']}', '{$fatura['Servico']['codigo']}', '{$this->Buonny->moeda($fatura['LogFaturamento']['valor'])}')", 'class' => 'icon-list-alt', 'title' => 'Clientes')) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif ?>
<?= $this->Javascript->codeBlock("
    function clientes_por_produto_e_preco(data_inicial, data_final, codigo_produto, codigo_servico, valor_unitario){
        var field = null;
        var form = document.createElement(\"form\");
        form.setAttribute(\"method\", \"post\");
        form.setAttribute(\"action\", \"/portal/produtos/clientes_por_produto_e_preco_faturado/\");
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Produto][data_inicial]\");
        field.setAttribute(\"value\", data_inicial);
        form.appendChild(field);
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Produto][data_final]\");
        field.setAttribute(\"value\", data_final);
        form.appendChild(field);
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Produto][codigo]\");
        field.setAttribute(\"value\", codigo_produto);
        form.appendChild(field);
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Produto][codigo_servico]\");
        field.setAttribute(\"value\", codigo_servico);
        form.appendChild(field);
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Produto][valor_unitario]\");
        field.setAttribute(\"value\", valor_unitario);
        form.appendChild(field);
        document.body.appendChild(form);
        form.submit();
        $(form).remove();
    }
    ");
?>