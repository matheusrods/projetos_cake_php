<br />
<?php if($cliente): ?>
    <div id="cliente" class='well'>
        <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
        <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
    </div>
<?php endif; ?>

<div class='row-fluid inline'>
<?php if(isset($paginator) && $listagem): ?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>

        <table class='table table-striped'>
            <thead>
                <th class='input-small'><?php echo $this->Paginator->sort('SM', 'viag_codigo_sm') ?></th>
                <th class='input-small'><?php echo $this->Paginator->sort('Pedido Cliente', 'viag_pedido_cliente') ?></th>
                <th class='input-small'><?php echo $this->Paginator->sort('Veiculo', 'TVeicVeiculo.veic_placa') ?></th>
                <th ><?php echo $this->Paginator->sort('Transportador', 'TPjurPessoaJuridicaT.pjur_razao_social') ?></th>
                <th ><?php echo $this->Paginator->sort('Embarcador', 'TPjurPessoaJuridicaE.pjur_razao_social') ?></th>
                <th style="width:13px"></th>
                <th style="width:13px"></th>
                <th style="width:13px"></th>
                <th style="width:13px"></th>
            </thead>
            <tbody>
                <?php foreach ($listagem as $viagem): ?>
                    <tr>
                        <td>
                            <?php echo $this->Buonny->codigo_sm($viagem['TViagViagem']['viag_codigo_sm']) ?>
                        </td>
                        <td>
                            <?php echo $viagem['TViagViagem']['viag_pedido_cliente'] ?>
                        </td>
                        <td>
                            <?php echo $viagem['TVeicVeiculo']['veic_placa'] ?>
                        </td>
                        <td>
                            <?php echo $viagem['TPjurPessoaJuridicaT']['pjur_razao_social'] ?>
                        </td>
                        <td>
                            <?php echo $viagem['TPjurPessoaJuridicaE']['pjur_razao_social'] ?>
                        </td>
                        <td>
                            <?php if ($viagem['TPgpgPg']['pgpg_tipo_pgr']!='G'): ?>
                                <?php echo $html->link('', array('controller' => 'Viagens', 'action' => 'alterar_nfs_itinerario', $cliente['Cliente']['codigo'], $viagem['TViagViagem']['viag_codigo']), array('class' => 'icon-list-alt', 'title' => 'Alterar Notas Fiscais e Produtos')); ?>
                            <?php else: ?>
                                &nbsp;
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo $html->link('', array('controller' => 'Viagens', 'action' => 'alterar_pedido_cliente', $cliente['Cliente']['codigo'], $viagem['TViagViagem']['viag_codigo']), array('class' => 'icon-file', 'title' => 'Alterar Pedido Cliente')); ?>
                        </td>
                        <td>
                            <?php echo $html->link('', array('controller' => 'Viagens', 'action' => 'incluir_alvo', $cliente['Cliente']['codigo'], $viagem['TViagViagem']['viag_codigo']), array('class' => 'icon-globe', 'title' => 'Incluir Itinerarios')); ?>
                        </td>
                        <td>
                            <?php echo $html->link('', array('controller' => 'Viagens', 'action' => 'alterar_destino', $cliente['Cliente']['codigo'], $viagem['TViagViagem']['viag_codigo']), array('class' => 'icon-share-alt', 'title' => 'Alterar Destino')); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan = "9"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['TViagViagem']['count']; ?></td>
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
<?php echo $this->Buonny->link_js('estatisticas'); ?>