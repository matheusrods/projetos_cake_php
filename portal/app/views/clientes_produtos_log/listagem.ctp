<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Produto</th>
            <th>Data Faturamento</th>
            <th>Motivo Bloqueio</th>
            <th>Possui Contrato</th>
            <th>Ação</th>
            <th>Data Inclusão</th>
            <th>Usuário</th>
            <th>Data Alteração</th>
            <th>Usuário Alteração</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes_produto_log as $cliente_produto_log): ?>
            <tr>
                <td>
                    <?= $cliente_produto_log['ClienteProdutoLog']['codigo_cliente'] ?>
                </td>
                <td>
                    <?= $cliente_produto_log['Produto']['descricao'] ?>
                </td>
                <td>
                    <?= $cliente_produto_log['ClienteProdutoLog']['data_faturamento'] ?>
                </td>
                <td>
                    <?= $cliente_produto_log['MotivoBloqueio']['descricao'] ?>
                </td>
                <td>
                    <?= $cliente_produto_log['ClienteProdutoLog']['possui_contrato'] == 1 ? 'sim' : 'não'; ?>
                </td>
                <td>
                    <?= $cliente_produto_log['ClienteProdutoLog']['acao_sistema'] == 0 ? 'INSERCAO':($cliente_produto_log['ClienteProdutoLog']['acao_sistema'] == 1 ? 'ALTERACAO':'EXCLUSAO') ?>
                </td>
                <td>
                    <?= $cliente_produto_log['ClienteProdutoLog']['data_inclusao'] ?>
                </td>
                <td>
                    <?= $cliente_produto_log['Usuario']['apelido'] ?>
                </td>
                <td>
                    <?= $cliente_produto_log['ClienteProdutoLog']['data_alteracao'] ?>
                </td>
                <td>
                    <?= $cliente_produto_log['UsuarioAlteracao']['apelido'] ?>
                </td>
            </tr>
        <?php endforeach; ?>        
    </tbody>
</table>
