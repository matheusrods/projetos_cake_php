<?php if(!empty($vendedores)):?>
    <?php echo $paginator->options(array('update' => 'div.lista')); ?>
    <table class="table table-striped">
        <thead>
            <tr>
             <th class="input-medium">Código</th>
             <th class="input-xxlarge">Nome</th>
             <th class="acoes" style="width:75px">Ações</th>
         </tr>
     </thead>
     <tbody>
        <?php foreach ($vendedores as $dados): ?>
            <tr>
                <td class="input-mini"><?php echo $dados['Vendedor']['codigo'] ?></td>
                <td class="input-xxlarge"><?php echo $dados['Vendedor']['nome'] ?></td>
                <td>
                    <?php echo $this->Html->link('', array('action' => 'editar', $dados['Vendedor']['codigo']), array('class' => 'icon-edit ', 'data-toggle' => 'tooltip', 'title' => 'Editar')); ?>&nbsp;&nbsp;
                    <?php echo $this->Html->link('', array('action' => 'excluir', $dados['Vendedor']['codigo']), array('class' => 'icon-trash ', 'data-toggle' => 'tooltip', 'title' => 'Excluir')); ?>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan = "10"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['Vendedor']['count']; ?></td>
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
    $(document).ready(function() {
        $('[data-toggle=\"tooltip\"]').tooltip();
    });
    function atualizaLista() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'vendedores/listagem/' + Math.random());
    }
    ");
    ?>
    <?php //echo $javascript->link('comum.js'); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>    