<?php 
echo $paginator->options(array('update' => 'div.lista')); 
$total_paginas = $this->Paginator->numbers();
?>
<table class="table table-condensed table-striped">
    <thead>
    <tr>
        <th ><?php echo $this->Paginator->sort('Código Cliente', 'codigo_cliente') ?></th>
        <th ><?php echo $this->Paginator->sort('Profissional', 'nome') ?></th>
        <th ><?php echo $this->Paginator->sort('Documento', 'codigo_documento') ?></th>
        <th ><?php echo $this->Paginator->sort('Negativação', 'descricao') ?></th>
        <th ><?php echo $this->Paginator->sort('Usuário Inclusão', 'apelido') ?></th>
        <th ><?php echo $this->Paginator->sort('Data inclusao', 'data_inclusao') ?></th>
        <th></th>
        <th></th>
    </tr>
    </thead>
    <?php foreach ($listagem as $profissional ): ?>
    <tr>
        <td><?php echo $profissional['Cliente']['codigo'] ?></td>
        <td><?php echo $profissional['Profissional']['nome'] ?></td>
        <td><?php echo $buonny->documento($profissional['Profissional']['codigo_documento']); ?></td>
        <td><?php echo $profissional['TipoNegativacao']['descricao'] ?></td>
        <td><?php echo $profissional['Usuario']['apelido'] ?></td>
        <td><?php echo $profissional['ProfNegativacaoCliente']['data_inclusao'] ?></td>
        <td>
            <?php echo $html->link('', array('controller' => 'profissionais_negativados_clientes', 'action' => 'editar', $profissional['ProfNegativacaoCliente']['codigo']), array('class' => 'icon-edit dialog', 'title' => 'editar')) ?>
        </td>         
        <td>
            <?php echo $html->link('', array('controller' => 'profissionais_negativados_clientes', 'action' => 'excluir', $profissional['ProfNegativacaoCliente']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir Profissional Negativado'), 'Confirma exclusão?'); ?>
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