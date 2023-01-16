<table class="table table-striped">
    <thead>
        <tr>
            <th style="width:120px">Data</th>
            <th>Tipo</th>
            <th>Observação</th>
            <th>Usuário</th>
        </tr>
    </thead>

<?php foreach ($historicos as $historico): ?>
    <tr>
        <td><?php echo $historico['ClienteHistorico']['data_inclusao'] ?></td>
        <td><?php echo $historico['TipoHistorico']['descricao'] ?></td>
        <td><?php echo $historico['ClienteHistorico']['observacao'] ?></td>
        <td><?php echo $historico['Usuario']['apelido'] ?></td>
    </tr>
<?php endforeach; ?>
</table>