<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-mini"><?php echo $this->Paginator->sort('Código', 'aatu_codigo') ?></th>
            <th><?php echo $this->Paginator->sort('Área de Atuação', 'aatu_descricao') ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($areas_atuacoes as $area_atuacao): ?>
        <tr>
            <td class="input-mini">
                <?php echo $area_atuacao['TAatuAreaAtuacao']['aatu_codigo'] ?>
            </td>
            <td>
                <?php echo $area_atuacao['TAatuAreaAtuacao']['aatu_descricao'] ?>
            </td>
            <td class="input-mini">
                <?php echo $html->link('', array('action' => 'editar', $area_atuacao['TAatuAreaAtuacao']['aatu_codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
				<?php echo $html->link('', array('controller' => 'areas_atuacoes', 'action' => 'excluir', $area_atuacao['TAatuAreaAtuacao']['aatu_codigo']), array('class' => 'icon-trash', 'title' => 'Excluir área de atuação'), 'Confirma exclusão?'); ?>
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