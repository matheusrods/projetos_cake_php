<?php 
echo $paginator->options(array('update' => 'div.lista')); 
$total_paginas = $this->Paginator->numbers();
?>
<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th ><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
            <th ><?php echo $this->Paginator->sort('Documento', 'codigo_documento') ?></th>
            <th ><?php echo $this->Paginator->sort('Nome', 'descricao') ?></th>
            <th ><?php echo $this->Paginator->sort('Negativacao', 'vigente') ?></th>
            <th></th>
            <th></th>
        </tr>
    </thead>

    <?php foreach ($profissionalnegativado as $profissional): 
      
    ?>
    <tr>
        <td><?php echo $profissional['ProfissionalNegativacao']['codigo'] ?></td>
        <td><?php echo $buonny->documento($profissional['Profissional']['codigo_documento']); ?></td>
        <td><?php echo $profissional['Profissional']['nome'] ?></td>
        <td><?php echo $profissional['Negativacao']['descricao'] ?></td>
        <td>
            <?php echo $html->link('', array('controller' => 'profissionais_negativados', 'action' => 'editar', $profissional['ProfissionalNegativacao']['codigo']), array('class' => 'icon-edit dialog', 'title' => 'editar')) ?>
        </td> 
        <td>   

            <?php echo $html->link('', array('controller' => 'profissionais_negativados', 'action' => 'excluir', $profissional['ProfissionalNegativacao']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir Profissional Negativado'), 'Confirma exclusão?'); ?>
        </td>     
        <?php echo $this->Form->input('endereco_'.$profissional['ProfissionalNegativacao']['codigo'], array('type' => 'hidden', 'value' => $profissional['ProfissionalNegativacao']['codigo'])) ?> 
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