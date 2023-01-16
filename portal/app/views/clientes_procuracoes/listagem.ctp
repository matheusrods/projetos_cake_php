<table class="table table-striped">
    <thead>
        <tr>
            <th>Data vigência início</th>
            <th>Data vigência fim</th>
            <th>Observação</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes_procuracoes as $procuracao): ?>
            <tr>
                <td><?php echo $procuracao['ClienteProcuracao']['data_vigencia_inicio'] ?></td>
                <td><?php echo $procuracao['ClienteProcuracao']['data_vigencia_inicio'] ?></td>
                <td><?php echo $procuracao['ClienteProcuracao']['observacao'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
