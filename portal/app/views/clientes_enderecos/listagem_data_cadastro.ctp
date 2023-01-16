<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th>CEP</th>
            <th>Endereço</th>
            <th>Complemento</th>
            <th>Número</th>
            <th>Tipo</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($enderecos as $endereco): ?>
            <tr>
                <td>
                    <?= $endereco['ClienteEndereco']['cep'] ?>
                </td>
                <td>
                    <?= $endereco['ClienteEndereco']['logradouro'] ?>
                </td>
                <td>
                    <?= $endereco['ClienteEndereco']['complemento'] ?>
                <td>
                    <?= $endereco['ClienteEndereco']['numero'] ?>
                </td>
                <td>
                    <?= $endereco['TipoContato']['descricao'] ?>
                </td>
            </tr>
        <?php endforeach; ?>        
    </tbody>
</table>