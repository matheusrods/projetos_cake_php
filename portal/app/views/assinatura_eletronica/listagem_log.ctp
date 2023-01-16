<?php //debug($dados);exit; ?>

<div id="listagem" style='padding-top:2%;'>
<?php if(!empty($dados)):?>
    <table class="table table-striped" style='width:100%;'>
        <thead>
            <tr>
                <th>Codigo Medico</th>
                <th>Assinatura</th>
                <th>Login</th>
                <th>Nome</th>
                <th>Perfil</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>

                <?php $total = 0 ?>
                <?php foreach($dados as $key => $value) : ?>
                    <?php $total += 1 ?>
                    <tr>
                        <td><?= $value['AnexoAssinaturaEletronica']['codigo_medico']; ?></td>
                        <td><img width="50px" src="<?php echo $value['AnexoAssinaturaEletronica']['caminho_arquivo']; ?>"></td>
                        <td><?= $value['UsuarioInclusao']['apelido']; ?></td>
                        <td><?= $value['UsuarioInclusao']['nome']; ?></td>
                        <td><?= $value['UperfilInclusao']['descricao']; ?></td>
                        <td><?= AppModel::dbDateToDate($value['AnexoAssinaturaEletronica']['data_inclusao']); ?></td>
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
</div>
