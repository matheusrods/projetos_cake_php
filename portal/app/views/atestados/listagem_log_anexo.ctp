<div id="listagem" style='padding-top:2%;'>
<?php if(!empty($dados)):?>
    <table class="table table-striped" style='width:100%;'>
        <thead>
            <tr>
                <th>Codigo Atestado</th>
                <th>Arquivo</th>
                <th>Usuário Inclusão</th>           
                <th>Usuário Alteração</th>
                <th>Ação</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>

                <?php $total = 0 ?>
                <?php foreach($dados as $key => $value) : ?>
                    <?php $total += 1 ?>
                    <tr>
                        <td><?= $value['AnexoAtestadoLog']['codigo_atestado']; ?></td>
                        <td><?= basename($value['AnexoAtestadoLog']['caminho_arquivo']); ?></td>
                        <td><?= $value['UsuarioInclusao']['nome']; ?></td>
                        <td><?= $value['UsuarioAlteracao']['nome']; ?></td>
                        <td><?=(isset($value['AnexoAtestadoLog']['acao_sistema'])) ? $acoes[$value['AnexoAtestadoLog']['acao_sistema']] : ''; ?></td>
                        <td><?= !empty($value['AnexoAtestadoLog']['data_alteracao']) ? AppModel::dbDateToDate($value['AnexoAtestadoLog']['data_alteracao']) : AppModel::dbDateToDate($value['AnexoAtestadoLog']['data_inclusao']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <tfoot>
                <tr>
                    <td><?= $total ?></td>
                    <td colspan="6"></td>
                </tr>
            </tfoot>
<?php else: ?>
    <div class="alert">Nenhum registro encontrado.</div>
<?php endif; ?> 
</tbody>
</table>

<?php if(!empty($dados_atestado)): echo "<br>";?>
    <div class="page-title"><h3>Registros do Atestado</h3></div>
    <table class="table table-striped" style='width:100%;'>
        <thead>
            <tr>
                <th>Codigo Atestado</th>
                <?php if($dados_atestado[0]['Atestado']['data_inclusao']) echo '<th>Usuário Inclusão</th>'; else echo "<th></th>"; ?>
                <?php if($dados_atestado[0]['Atestado']['data_inclusao']) echo '<th>Data Inclusão</th>'; else echo "<th></th>"; ?>         
                <?php if($dados_atestado[0]['Atestado']['data_alteracao']) echo '<th>Usuário Alteração</th>'; else echo "<th></th>"; ?>
                <?php if($dados_atestado[0]['Atestado']['data_alteracao']) echo '<th>Data Alteração</th>'; else echo "<th></th>"; ?> 
            </tr>
        </thead>
        <tbody>

                <?php $total = 0 ?>
                <?php foreach($dados_atestado as $key => $value) : ?>
                    <?php $total += 1 ;  //die(debug($value));?>

                    <tr>
                        <td><?= $value['Atestado']['codigo']; ?></td>
                        <td><?= !empty($value['Atestado']['data_inclusao']) ? $value['Atestado']['atestado_usuario_inclusao'] : ""; ?></td>
                        <td><?= !empty($value['Atestado']['data_inclusao']) ? AppModel::dbDateToDate($value['Atestado']['data_inclusao']) : ""; ?></td>
                        <td><?= !empty($value['Atestado']['data_alteracao']) ? $value['Atestado']['atestado_usuario_alteracao'] : ""; ?></td>
                        <td><?= !empty($value['Atestado']['data_alteracao']) ? AppModel::dbDateToDate($value['Atestado']['data_alteracao']) : ""; ?></td>
                    </tr>
                <?php endforeach; ?>
            
<?php else: ?>
    <div class="alert">Nenhum registro encontrado.</div>
<?php endif; ?> 
    </tbody>
</table>
</div>
