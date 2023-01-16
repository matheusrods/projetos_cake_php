<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th>Cliente</th>
            <th>Retorno</th>
            <th>Contato</th>
            <th>Tipo</th>
            <th>Representante</th>
            <th>Ação</th>
            <th>Data Inclusão</th>
            <th>Usuário</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes_contato_log as $cliente_contato_log): ?>
            <tr>
                <td>
                    <?= $cliente_contato_log['ClienteContatoLog']['codigo_cliente'] ?>
                </td>
                <td>
                    <?= $cliente_contato_log['TipoRetorno']['descricao'] ?>
                </td>
                <td>
                    <?= $cliente_contato_log['ClienteContatoLog']['codigo_tipo_retorno'] == 1 ? '('.$cliente_contato_log['ClienteContatoLog']['ddd'].') '.$cliente_contato_log['ClienteContatoLog']['descricao'] : $cliente_contato_log['ClienteContatoLog']['descricao'];  ?>
                </td>
                <td>
                    <?= $cliente_contato_log['TipoContato']['descricao'] ?>
                </td>
                <td>
                    <?= $cliente_contato_log['ClienteContatoLog']['nome'] ?>
                </td>
                <td>
                    <?= $cliente_contato_log['ClienteContatoLog']['acao_sistema'] == 0 ? 'INSERCAO':($cliente_contato_log['ClienteContatoLog']['acao_sistema'] == 1 ? 'ALTERACAO':'EXCLUSAO') ?>
                </td>
                <td>
                    <?= $cliente_contato_log['ClienteContatoLog']['data_inclusao'] ?>
                </td>
                <td>
                    <?= $cliente_contato_log['Usuario']['apelido'] ?>
                </td>
            </tr>
        <?php endforeach; ?>        
    </tbody>
</table>


