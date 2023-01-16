<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th>Produto</th>
            <th>Motivo Bloqueio</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produtos_motivo_bloqueios as $produto_motivo_bloqueio): ?>
            <tr>
                <td>
                    <?= $produto_motivo_bloqueio['Produto']['descricao'] ?>
                </td>
                <td>
                    <?= $produto_motivo_bloqueio['MotivoBloqueio']['descricao'] ?>
                </td>
            </tr>
        <?php endforeach; ?>        
    </tbody>
</table>