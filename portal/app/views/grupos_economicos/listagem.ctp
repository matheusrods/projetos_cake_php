<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th >Código</th>
            <th><?php echo $this->Paginator->sort('Descrição', 'descricao') ?></th>
            <th >Código Cliente Matriz</th>
            <th ></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($grupos_economicos as $grupo_economico): ?>
        <tr>
            <td><?php echo $grupo_economico['GrupoEconomico']['codigo'] ?></td>
            <td><?php echo $grupo_economico['GrupoEconomico']['descricao'] ?></td>
            <td><?php echo $grupo_economico['GrupoEconomico']['codigo_cliente'] ?></td>
            <td class="pagination-centered">
                <?= $html->link('', array('action' => 'editar', $grupo_economico['GrupoEconomico']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
            
                <?= $html->link('', array('action' => 'excluir', $grupo_economico['GrupoEconomico']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir'), 'Confirma exclusão?') ?>
            
                <?= $html->link('', array('controller' => 'grupos_economicos_clientes', 'action' => 'index', $grupo_economico['GrupoEconomico']['codigo']), array('class' => 'icon-wrench', 'title' => 'Clientes')) ?>
            </td>
        </tr>
        <?php endforeach; ?>        
    </tbody>
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