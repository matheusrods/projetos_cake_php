<?php if (!empty($riscos)) : ?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <div class='well'>
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array('controller' => 'riscos', 'action' => 'export_excel'), array('target' => '_blank', 'escape' => false, 'title' => 'Exportar para Excel', 'style' => 'float:right')); ?>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="input-medium">Códigoooooo</th>
                <th class="input-xxlarge">Risco</th>
                <th class="input-xlarge">Grupo</th>
                <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($riscos as $dados) : ?>
                <tr>
                    <td class="input-mini"><?php echo $dados['Risco']['codigo'] ?></td>
                    <td class="input-xxlarge"><?php echo $dados['Risco']['nome_agente'] ?></td>
                    <td class="input-xlarge"><?php echo $dados['GrupoRisco']['descricao']; ?></td>
                    <td>
                        <?php echo $html->link('', array('controller' => 'Riscos', 'action' => 'trocar_status', $dados['Risco']['codigo']), array('class' => 'icon-random', 'title' => 'Trocar Status do Risco')) ?>
                        <?php if ($dados['Risco']['ativo']) : ?>
                            <span class="badge badge-empty badge-success" title="Ativo"></span>
                        <?php else : ?>
                            <span class="badge badge-empty badge-important" title="Inativo"></span>
                        <?php endif; ?>
                        <?php echo $this->Html->link('', array('action' => 'editar', $dados['Risco']['codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Risco']['count']; ?></td>
            </tr>
        </tfoot>
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span7'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>

        </div>
    </div>
    <?php echo $this->Js->writeBuffer(); ?>
    <?php
    echo $this->Javascript->codeBlock("
    function atualizaListaRiscos() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'riscos/listagem/' + Math.random());
    }
");
    ?>
    <?php //echo $javascript->link('comum.js'); 
    ?>
<?php else : ?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif; ?>