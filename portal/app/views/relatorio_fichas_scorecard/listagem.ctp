<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th ><?php echo $this->Paginator->sort('CPF', 'ConsultaBCB.codigo_documento') ?></th>
            <th ><?php echo $this->Paginator->sort('Nova Consulta', 'nome') ?></th>
            <th ><?php echo $this->Paginator->sort('Data', 'descricao') ?></th>
            <th ><?php echo $this->Paginator->sort('Usuario', 'vigente') ?></th>          
        </tr>
    </thead>
    <?php foreach ($consultabcb as $bcb): ?>
    <tr>
        <td><?php echo $bcb['0']['codigo_documento'] ?></td>
        <td><?php echo $bcb['0']['nova_consulta'] ?></td>
        <td><?php echo $bcb['LogConsultaBCB']['data']; ?></td>
        <td><?php echo $bcb['Usuario']['apelido'] ?></td>
    </tr>
<?php endforeach; ?>
    <tfoot>
        <?php if( isset($consultabcb) ): ?> 
            <tr>
                <td colspan="6">                
                    <strong>Total: <?php echo $this->Paginator->counter('{:count}');?></strong>
                </td>
            </tr>
        <?php  endif;?>
    </tfoot>
</table>

<div class='row-fluid'>
    <div class='numbers span6'>
        <?php if($this->Paginator->counter('{:pages}') > 1): ?>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        <?php endif; ?>
    </div>
    <div class='counter span6'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>

