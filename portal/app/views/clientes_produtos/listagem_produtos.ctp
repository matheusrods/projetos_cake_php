<table class="table table-striped">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Produto</th>
            <th>N° Contrato</th>
            <th class="acoes">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes_produtos as $produto): ?>
            <tr>
                <td><?php echo $produto['ClienteProduto']['codigo_cliente'] ?></td>
                <td><?php echo $produto['Produto']['descricao'] ?></td>
                <td><?php echo $produto['ClienteProdutoContrato']['numero'] ?></td>
                <td><?php echo $this->Html->link('', array('controller' => 'clientes_produtos_contratos', 'action' => 'atualizar', $produto['ClienteProduto']['codigo']), array('escape' => false, 'class' => 'icon-edit', 'title' => 'Editar Contratos', 'onclick' => "return open_dialog(this, '', 960)")); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>