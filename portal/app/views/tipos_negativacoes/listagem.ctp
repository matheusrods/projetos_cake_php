<?php 
echo $paginator->options(array('update' => 'div.lista')); 
$total_paginas = $this->Paginator->numbers();
?>
<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th ><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
            <th ><?php echo $this->Paginator->sort('Descrição', 'descricao') ?></th>
            <th></th>
            <th></th>
        </tr>
    </thead>

    <?php foreach ($dados as $d): 
      
    ?>
    <tr>
        <td><?php echo $d['TipoNegativacao']['codigo']; ?></td>
        <td><?php echo $d['TipoNegativacao']['descricao']; ?></td>
        <td>
            <?php echo $html->link('', array('controller' => 'TiposNegativacoes', 'action' => 'editar', $d['TipoNegativacao']['codigo']), array('class' => 'icon-edit dialog', 'title' => 'editar')) ?>
        </td> 
        <td>   

            <?php echo $html->link('', array('controller' => 'TiposNegativacoes', 'action' => 'excluir', $d['TipoNegativacao']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir Tipo de Negativação.'), 'Confirma exclusão?'); ?>
        </td>     
        <div class="clear"></div>
    </td>
</tr>
<?php endforeach; ?>
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
