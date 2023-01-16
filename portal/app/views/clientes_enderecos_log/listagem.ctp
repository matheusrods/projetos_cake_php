<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th>Código</th>
            <th>CEP</th>
            <th>Endereco</th>
            <th>Complemento</th>
            <th>Número</th>
            <th>Tipo</th>
            <th>Ação</th>
            <th>Data Inclusão</th>
            <th>Usuário</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes_endereco_log as $cliente_endereco_log): ?>
            <tr>
                <td>
                    <?= $cliente_endereco_log['ClienteEnderecoLog']['codigo_cliente'] ?>
                </td>
                <td>
                    <?= $cliente_endereco_log['ClienteEnderecoLog']['cep'] ?>
                </td>
                <td>
                    <?= $cliente_endereco_log['ClienteEnderecoLog']['logradouro'] ?>
                </td>
                <td>
                    <?= $cliente_endereco_log['ClienteEnderecoLog']['complemento'] ?>
                </td>
                <td>
                    <?= $cliente_endereco_log['ClienteEnderecoLog']['numero'] ?>
                </td>
                <td>
                    <?= $cliente_endereco_log['TipoContato']['descricao'] ?>
                </td>
                <td>
                    <?= $cliente_endereco_log['ClienteEnderecoLog']['acao_sistema'] == 0 ? 'INSERCAO':($cliente_endereco_log['ClienteEnderecoLog']['acao_sistema'] == 1 ? 'ALTERACAO':'EXCLUSAO') ?>
                </td>
                <td>
                    <?= $cliente_endereco_log['ClienteEnderecoLog']['data_inclusao'] ?>
                </td>
                <td>
                    <?= $cliente_endereco_log['Usuario']['apelido'] ?>
                </td>
            </tr>
        <?php endforeach; ?>        
    </tbody>
</table>


