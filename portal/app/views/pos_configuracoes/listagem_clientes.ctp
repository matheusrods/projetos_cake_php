<?php if(!empty($clientes)):?>
    <?= $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
               <th class="input-medium">Código</th>
            <th class="input-xxlarge">Razão Social</th>
            <th class="input-xxlarge">Nome fantasia</th>
            <th class="acoes" style="width:75px">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $dados): ?>
            <tr>
                <td class="input-mini"><?= $dados['Cliente']['codigo'] ?></td>
                <td><?= $dados['Cliente']['razao_social'] ?></td>
                <td><?= $dados['Cliente']['nome_fantasia'] ?></td>
                <td>
                    <?= $html->link('', array('controller' => 'pos_configuracoes', 'action' => 'gerenciar', $dados['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar Configurações POS')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?= $this->Paginator->params['paging']['Cliente']['count']; ?></td>
            </tr>
        </tfoot>    
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?= $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?= $this->Paginator->numbers(); ?>
            <?= $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span7'>
            <?= $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
    <?= $this->Js->writeBuffer(); ?>
   <?php //echo $javascript->link('comum.js'); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    