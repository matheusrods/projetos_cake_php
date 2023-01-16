<br />
<?php if( !empty($codigo_cliente) && $tela_inclusao == FALSE ): ?>
    <div class='actionbar-right'>
        <?php        
        echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir_configuracao_origem_destino?codigo_cliente='.urlencode(Comum::encriptarLink("$codigo_cliente"))), array('class' => 'btn btn-success', 'escape' => false));
        ?>
    </div>
    <div id="cliente" class='well'>
        <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
        <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
    </div>
<?php endif; ?>
<div class='row-fluid inline'>
<?php if (isset($paginator) && !empty($listagem)): ?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class='table table-striped'>
        <thead>
        <th class='input-large'>Origem</th>
        <th class='input-large'>Destino</th>            
        <th style="width:13px"></th>
        </thead>
        <tbody>
            <?php foreach ($listagem as $referencia): ?>
                <tr>
                    <td title = "<?php echo $referencia['TRefeReferenciaOrigem']['refe_descricao'] ?>" >
                        <?php echo $this->Buonny->posicao_geografica( mb_substr($referencia['TRefeReferenciaOrigem']['refe_descricao'], 0, 30, 'utf-8'), $referencia['TRefeReferenciaOrigem']['refe_latitude'], $referencia['TRefeReferenciaOrigem']['refe_longitude'], '') ?>
                    </td>
                    <td title = "<?php echo $referencia['TRefeReferenciaDestino']['refe_descricao'] ?>" >
                        <?php echo $this->Buonny->posicao_geografica( mb_substr($referencia['TRefeReferenciaDestino']['refe_descricao'], 0, 30, 'utf-8'), $referencia['TRefeReferenciaDestino']['refe_latitude'], $referencia['TRefeReferenciaDestino']['refe_longitude'], '') ?>
                    </td>
                    <td>
                        <?php if( $tela_inclusao == FALSE ):?>
                        <?php echo $html->link('', array('controller' => 'Referencias', 'action' => 'excluir_configuracao_origem_destino', $referencia['TCodeConfOrigemDestino']['code_codigo']), array('class' => 'icon-trash', 'title' => 'Remover Destino'), 'Confirma exclusão?'); ?>
                        <?php endif;?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan = "11"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['TCodeConfOrigemDestino']['count']; ?></td>
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