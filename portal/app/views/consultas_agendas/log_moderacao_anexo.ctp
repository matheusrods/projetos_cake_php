<table class="table table-striped" style='width:98%;max-width:none;'>
    <thead>
        <tr>
            <th>Caminho do arquivo</th>
            <th>Data Inclusão</th>
            <th>Usuário Inclusão</th>
            <th>Status Anexo</th>
            <th>Motivo Recusa</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0 ?>
        <?php foreach($dados as $key => $value) : ?>
            <?php $total += 1 ?>
            <tr>
                <td><?=$value['AnexoExameLog']['caminho_arquivo']; ?></td>
                <td><?=AppModel::dbDateToDate($value['AnexoExameLog']['data_inclusao']); ?></td>
                <td><?=$value['Usuario']['nome']; ?></td>
                <td><?=isset($tipos_status[$value['AnexoExameLog']['status']]) ? $tipos_status[$value['AnexoExameLog']['status']]  : ''; ?></td>                
                <td><?=$value['AnexoExameLog']['motivo_recusa']; ?></td>
                <td><?=isset($acoes[$value['AnexoExameLog']['acao_sistema']]) ? $acoes[$value['AnexoExameLog']['acao_sistema']]  : ''; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td><?= $total ?></td>
            <td colspan="5"></td>
        </tr>
    </tfoot>
</table>