<table class="table table-striped" style='width:98%;max-width:none;'>
    <thead>
        <tr>
            <th class="input-medium">Caminho do arquivo</th>
            <th class="input-medium">Data Inclusão</th>
            <th class="input-medium">Usuário Inclusão</th>
            <th class="input-small">Status Anexo</th>
            <th class="input-large">Motivo Recusa</th>
            <th class="input-small" >Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php $total = 0 ?>
        <?php foreach($dados as $key => $value) : ?>
            <?php $total += 1 ?>
            <tr>
                <td><?=$value['AnexoFichaClinicaLog']['caminho_arquivo']; ?></td>
                <td><?=AppModel::dbDateToDate($value['AnexoFichaClinicaLog']['data_inclusao']); ?></td>
                <td><?=$value['Usuario']['nome']; ?></td>
                <td><?=isset($tipos_status[$value['AnexoFichaClinicaLog']['status']]) ? $tipos_status[$value['AnexoFichaClinicaLog']['status']]  : ''; ?></td>                
                <td><?=$value['AnexoFichaClinicaLog']['motivo_recusa']; ?></td>
                <td><?= isset($acoes[$value['AnexoFichaClinicaLog']['acao_sistema']]) ? $acoes[$value['AnexoFichaClinicaLog']['acao_sistema']]  : ''; ?></td>
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