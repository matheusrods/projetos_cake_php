<?php 
echo $paginator->options(array('update' => 'div.lista')); 
$total_paginas = $this->Paginator->numbers();
?>
<table class="table table-condensed table-striped">
    <thead>

        <tr>
            <th ><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
            <th ><?php echo $this->Paginator->sort('CPF/CNPJ', 'codigo_documento') ?></th>
            <th ><?php echo $this->Paginator->sort('Nome', 'nome_razao_social') ?></th>
            <th ><?php echo $this->Paginator->sort('Data Inclusão', 'data_inclusao') ?></th>
            
            <th></th>
            <th></th>
        </tr>
    </thead>

    <?php foreach ($proprietarios as $proprietario): ?>
    <tr>
        <td><?php echo $proprietario['Proprietario']['codigo'] ?></td>
        <td><?php echo $buonny->documento($proprietario['Proprietario']['codigo_documento']); ?></td>
        <td><?php echo $proprietario['Proprietario']['nome_razao_social'] ?></td>
        <td><?php echo $proprietario['Proprietario']['data_inclusao'] ?></td>     
        <td>
            <?php echo $html->link('', array('controller' => 'proprietarios', 'action' => 'editar', $proprietario['Proprietario']['codigo']), array('class' => 'icon-edit dialog', 'title' => 'editar')) ?>
        </td> 
        <td>   
            <?php echo $html->link('', array('controller' => 'proprietarios', 'action' => 'excluir', $proprietario['Proprietario']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir Proprietario'), 'Confirma exclusão?'); ?>
        </td>     
        <?php echo $this->Form->input('endereco_'.$proprietario['Proprietario']['codigo'], array('type' => 'hidden', 'value' => $proprietario['Proprietario']['codigo'])) ?> 
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