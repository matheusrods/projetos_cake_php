<br />

<?php if($cliente): ?>




<div id="cliente" class='well'>
    <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
    <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
</div>

<div class='row-fluid inline'>
    <table class='table table-striped'>
        <thead>
        <th class='input-small'>Arquivos</th>
        </thead>
        <tbody>
<?php foreach ($listagem as $arquivo ): ?>
            <tr>
                <td>
<?= $this->Html->link($arquivo['WebsmRetorno']['arquivo_nome'], array('Controller' => 'WebsmRetornos', 'action' => 'download', $arquivo['WebsmRetorno']['codigo'], rand())) ?>
                </td>
            </tr>

<?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan = "4">&nbsp;</td>
            </tr>
        </tfoot>
    </table>

</div>

<?php endif; ?>
</div>