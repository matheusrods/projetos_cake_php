<?php if(!empty($clientes)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
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
                <td class="input-mini"><?php echo $dados['Cliente']['codigo'] ?></td>
                <td><?php echo $dados['Cliente']['razao_social'] ?></td>
                <td><?php echo $dados['Cliente']['nome_fantasia'] ?></td>
                <td>
                    <?php echo $html->link('', array('controller' => 'atribuicoes', 'action' => 'gerenciar', $dados['Cliente']['codigo']), array('class' => 'icon-wrench', 'title' => 'Gerenciar atribuições')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
        <tfoot>
            <tr>
                <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Cliente']['count']; ?></td>
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
   <?php //echo $javascript->link('comum.js'); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    