<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th>Retorno</th>
            <th>Contato</th>
            <th>Tipo</th>
            <th>Representante</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($contatos as $contato): ?>
            <tr>
                <td>
                    <?= $contato['TipoRetorno']['descricao'] ?>
                </td>
                <td>
                    <?= $contato['FuncionarioContato']['descricao'] ?>
                </td>
                <td>
                    <?= $contato['TipoContato']['descricao'] ?>
                </td>
                <td>
                    <?= $contato['FuncionarioContato']['nome'] ?>
                </td>
            </tr>
        <?php endforeach; ?>        
    </tbody>
</table>