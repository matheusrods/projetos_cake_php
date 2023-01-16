<table class='table table-striped'>
    <thead>
        <th>Produto</th>
        <th>Serviço</th>
        <th class='numeric'>Valor</th>
    </thead>
    <tbody>
        <?php foreach ($produtos_servicos as $produto_servico): ?>
            <tr>
                <td><?= $produto_servico['Produto']['descricao'] ?></td>
                <td><?= $produto_servico['Servico']['descricao'] ?></td>
                <td class='numeric'><?= $this->Buonny->moeda($produto_servico['ClienteProdutoServico2']['valor']) ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>