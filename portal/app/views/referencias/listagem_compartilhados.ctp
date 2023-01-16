<br />
<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir_referencia_compartilhada'), array('class' => 'btn btn-success', 'escape' => false));?>
</div>
<div class='row-fluid inline'>
<?php if (isset($paginator)): ?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
        <table class='table table-striped'>
            <thead>
            <th class='input-small'><?php echo $this->Paginator->sort('Codigo', 'refe_codigo') ?></th>
            <th ><?php echo $this->Paginator->sort('Descrição', 'refe_descricao') ?></th>
            <th class='input-medium'><?php echo $this->Paginator->sort('Classe', 'cref') ?></th>
            <th class='input-medium'><?php echo $this->Paginator->sort('Cidade', 'cida_descricao') ?></th>
            <th class='input-small'><?php echo $this->Paginator->sort('Estado', 'esta_sigla') ?></th>
            <th style="width:13px"></th>
            <th style="width:13px"></th>
            <th style="width:13px"></th>
            </thead>
            <tbody>
                <?php foreach ($listagem as $referencia): ?>
                    <tr>
                        <td>
                            <?php echo $this->Buonny->posicao_geografica($referencia['TRefeReferencia']['refe_codigo'], $referencia['TRefeReferencia']['refe_latitude'], $referencia['TRefeReferencia']['refe_longitude'], '') ?>
                        </td>
                        <td title = "<?php echo $referencia['TRefeReferencia']['refe_descricao'] ?>" >
                            <?php echo mb_substr($referencia['TRefeReferencia']['refe_descricao'], 0, 30, 'utf-8') ?>
                        </td>
                        <td>
                            <?php echo $referencia['TCrefClasseReferencia']['cref_descricao'] ?>
                        </td>
                        <td>
                            <?php echo $referencia['TCidaCidade']['cida_descricao'] ?>
                        </td>
                        <td>
                            <?php echo $referencia['TEstaEstado']['esta_sigla'] ?>
                        </td>
                        <td>
                            <?php if ($referencia['TRefeReferencia']['refe_inativo'] == 'S'): ?>
                                INATIVO
                            <?php else: ?>
                                ATIVO
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo $html->link('', array('controller' => 'Referencias', 'action' => 'alterar_referencia_compartilhada', $referencia['TRefeReferencia']['refe_codigo']), array('class' => 'icon-edit', 'title' => 'Alterar Referencia')); ?>
                        </td>
                        <td>
                            <?php if ($referencia['TRefeReferencia']['refe_inativo'] == 'S'): ?>
                                <?php echo $html->link('', array('controller' => 'Referencias', 'action' => 'ativar', $referencia['TRefeReferencia']['refe_codigo'], rand()), array('onclick' => 'return open_dialog(this, "Ativar Referencia", 560)', 'title' => 'Ativar Referencia', 'class' => 'icon-thumbs-up')) ?>
                            <?php else: ?>
                                <?php echo $html->link('', array('controller' => 'Referencias', 'action' => 'inativar', $referencia['TRefeReferencia']['refe_codigo'], rand()), array('onclick' => 'return open_dialog(this, "Inativar Referencia", 560)', 'title' => 'Inativar Referencia', 'class' => 'icon-thumbs-down')) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan = "11"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['TRefeReferencia']['count']; ?></td>
                </tr>
            </tfoot>
        </table>
        <div class='row-fluid'>
            <div class='numbers span6'>
                <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
                <?php echo $this->Paginator->numbers(); ?>
                <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
            </div>
            <div class='counter span6'>
                <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            </div>
        </div>
    <?php echo $this->Js->writeBuffer(); ?>
<?php else: ?>
    <br />
<?php endif; ?>
</div>
<?php echo $this->Buonny->link_js('estatisticas') ?>