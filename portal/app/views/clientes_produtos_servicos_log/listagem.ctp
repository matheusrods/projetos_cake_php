<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Produto</th>
            <th>Servico</th>
            <th>Categoria</th>
            <th>Valor</th>
            <th>Pagador</th>
            <th>Consistência Motorista</th>
            <th>Consistência Veículo</th>
            <th>Consulta Embarcador</th>
            <th>Tempo de Pesquisa</th>
            <th>Validade</th>
            <th>Ação</th>
            <th>Data Inclusão</th>
            <th>Usuário</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes_produtos_servicos_log as $cliente_produto_servico_log): ?>
            <tr>
                <td>
                    <?= $cliente_produto_servico_log['ClienteProduto']['codigo_cliente'] ?>
                </td>
                <td>
                    <?= $cliente_produto_servico_log['ClienteProduto']['Produto']['descricao'] ?>
                </td>
                <td>
                    <?= $cliente_produto_servico_log['Servico']['descricao'] ?>
                </td>
                <td>
                    <?= $cliente_produto_servico_log['ProfissionalTipo']['descricao'] ?>
                </td>
                <td>
                    <?= $cliente_produto_servico_log['ClienteProdutoServicoLog']['valor'] ?>
                </td>
                <td>
                    <?= $cliente_produto_servico_log['ClienteProdutoServicoLog']['codigo_cliente_pagador'] ?>
                </td>
                <td>
                    <?= $cliente_produto_servico_log['ClienteProdutoServicoLog']['consistencia_motorista'] == 1 ? 'sim': 'não'; ?>
                </td>
                <td>
                    <?= $cliente_produto_servico_log['ClienteProdutoServicoLog']['consistencia_veiculo'] == 1 ? 'sim': 'não'; ?>
                </td>
                <td>
                    <?= $cliente_produto_servico_log['ClienteProdutoServicoLog']['consulta_embarcador'] == 1 ? 'sim': 'não'; ?>
                </td>
                <td>
                    <?= $cliente_produto_servico_log['ClienteProdutoServicoLog']['tempo_pesquisa'] ?>
                </td>
                <td>
                    <?= $cliente_produto_servico_log['ClienteProdutoServicoLog']['validade'] ?>
                </td>
                <td>
                    <?= $cliente_produto_servico_log['ClienteProdutoServicoLog']['acao_sistema'] == 0 ? 'INSERCAO':($cliente_produto_servico_log['ClienteProdutoServicoLog']['acao_sistema'] == 1 ? 'ALTERACAO':'EXCLUSAO') ?>
                </td>
                <td>
                    <?= $cliente_produto_servico_log['ClienteProdutoServicoLog']['data_inclusao'] ?>
                </td>
                <td>
                    <?= $cliente_produto_servico_log['Usuario']['apelido'] ?>
                </td>
            </tr>
        <?php endforeach; ?>        
    </tbody>
</table>
