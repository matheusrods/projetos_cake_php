<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th ><?php echo $this->Paginator->sort('N° Ficha', 'codigo') ?></th>
            <th ><?php echo $this->Paginator->sort('Cliente', 'nome') ?></th>
            <th ><?php echo $this->Paginator->sort('Profissional', 'descricao') ?></th>
            <th ><?php echo $this->Paginator->sort('Validade', 'data_vigencia') ?></th>
            <th ><?php echo $this->Paginator->sort('Observação', 'data_vigencia') ?></th>
            <th ><?php echo $this->Paginator->sort('Número CT', 'data_vigencia') ?></th>
            <th></th>
        </tr>
    </thead>

<?php foreach ($listagem_ct as $ct): ?>
    <tr>
        <td><?=$ct[0]['codigo'] ?></td>
        <td><?=$ct[0]['razao_social'] ?></td>
        <td><?=$ct[0]['Profissional.nome'] ?></td>
        <td><?=date('d/m/Y', strtotime(str_replace('/', '-', $ct[0]['validade_ate']))); ?></td>
        <td><?=$ct[0]['FichaStatus.descricao'] ?></td>
        <td><?=$ct[0]['FichaCt.numero_liberacao'] ?></td>
        <td>
            <?php echo $html->link('', array('controller' => 'relatorio_fichas_scorecard', 'action' => 'gera_ct_ficha', $ct[0]['codigo']), array('class' => 'icon-print dialog', 'title' => 'Imprimir CT')) ?>
        </td> 
    </tr>
<?php endforeach; ?>
    <tfoot>
        <?php if( isset($cts) ): ?>
            <tr>
                <td><strong>Total</strong></td>
                <td colspan="6" class="input-xlarge"><strong>
                    <?php 
                        if($this->Paginator->counter('{:count}') > 1)
                            echo $this->Paginator->counter('{:count}')." Demostrativos CT";
                        else
                            echo $this->Paginator->counter('{:count}')." Demostrativos CT";
                    ?></strong>
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
