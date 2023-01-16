<?php 
echo $paginator->options(array('update' => 'div.lista')); 
$total_paginas = $this->Paginator->numbers();
?>
<table class="table">
    <thead>        
        <tr>
            <th>Usuário Inclusão</th>
            <th>Data Inclusão</th>
            <th>Endereço IP</th>
            <th>Código Cliente</th>
            <th>Cliente</th>
            <th></th>
        </tr>
    </thead>
    <?php foreach ($enderecos_ips as $ip): ?>
    <tr>
        <td><?php echo $ip['Usuario']['nome']; ?></td>
        <td><?php echo $ip['ClienteIp']['data_inclusao']; ?></td>
        <td><?php echo $ip['ClienteIp']['descricao']; ?></td>
        <td><?php echo $ip['ClienteIp']['codigo_cliente']; ?></td>
        <td><?php echo $ip['Cliente']['razao_social']; ?></td>        
        <td>
            <?php echo $this->Html->link('', array('action' => 'excluir', $ip['ClienteIp']['codigo'], rand()), array('title' => 'Remover IP', 'class' => 'icon-trash'));?>
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