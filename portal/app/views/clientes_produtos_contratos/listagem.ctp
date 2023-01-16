<?php if (!empty($documentos_cliente)): ?>
<?php endif; ?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Produto</th>
            <th>Status</th>
            <th>Data Vencimento</th>
            <th>N° Contrato</th>
            <th class="acoes">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes_produtos as $produto): ?>
            <tr>
                <td><?php echo $produto['Produto']['descricao'] ?></td>
                <td><?php echo $produto['MotivoBloqueio']['descricao']?></td>
                <td><?php echo $produto['ClienteProdutoContrato']['data_vigencia'] ? preg_replace('/\s+.*$/', '', $produto['ClienteProdutoContrato']['data_vigencia']) : '&nbsp;'; ?></td>
                <td><?php echo $produto['ClienteProdutoContrato']['numero'] ? $produto['ClienteProdutoContrato']['numero'] : '&nbsp;' ?></td>
                <td width="10"><?php echo $this->Html->link('', array('controller' => 'clientes_produtos_contratos', 'action' => 'atualizar', $produto['ClienteProduto']['codigo']), array('escape' => false, 'class' => 'icon-edit', 'title' => 'Editar contratos')); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
