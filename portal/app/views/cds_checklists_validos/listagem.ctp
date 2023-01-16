<table class='table table-striped'>
    <thead>
        <th ><?php echo $this->Paginator->sort('Descrição', 'refe_descricao') ?></th>
        <th class='input-small'><?php echo $this->Paginator->sort('Bandeira', 'band_descricao') ?></th>
        <th class='input-small'><?php echo $this->Paginator->sort('Região', 'regi_descricao') ?></th>
        <th class='input-small'><?php echo $this->Paginator->sort('Cidade', 'cida_descricao') ?></th>
        <th class='input-small'><?php echo $this->Paginator->sort('Estado', 'esta_sigla') ?></th>
        <th style="width:13px"></th>
        <th style="width:13px"></th>
    </thead>
    <tbody>
        <?php foreach ($listagem as $key => $referencia): ?>
            <tr>
                <td title = "<?php echo $referencia['TRefeReferencia']['refe_descricao'] ?>" >
                    <?php echo mb_substr($referencia['TRefeReferencia']['refe_descricao'], 0, 30, 'utf-8') ?>
                </td>
                <td><?php echo $referencia['TBandBandeira']['band_descricao'] ?></td>
                <td><?php echo $referencia['TRegiRegiao']['regi_descricao'] ?></td>
                <td><?php echo $referencia['TCidaCidade']['cida_descricao'] ?></td>
                <td><?php echo $referencia['TEstaEstado']['esta_sigla'] ?></td>
                <td><?php if ($referencia['TRefeReferencia']['refe_inativo'] == 'S'): ?>
                        INATIVO
                    <?php else: ?>
                        ATIVO
                    <?php endif; ?>
                </td>
                <td><?= $this->Html->link('', array('action' => 'excluir', $referencia['TCcvaCdChecklistValido']['ccva_codigo'], rand()), array('title' => 'Excluir', 'class' => 'icon-trash')) ?></td>

            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan = "11"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['TRefeReferencia']['count']; ?></td>
        </tr>
    </tfoot>
</table>