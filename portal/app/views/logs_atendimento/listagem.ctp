<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th class='input-medium'><?php echo $this->Paginator->sort('Usuário', 'apelido') ?></th>
            <th class='input-medium'><?php echo $this->Paginator->sort('Operação', 'TipoOperacao.descricao') ?></th>
            <th class='input-medium'><?php echo $this->Paginator->sort('Categoria', 'ProfissionalTipo.descricao') ?></th>
            <th class='input-small'><?php echo $this->Paginator->sort('Data Início', 'data_inicio') ?></th>
            <th class='input-small'><?php echo $this->Paginator->sort('Data Término', 'data_inclusao') ?></th>
            <th class='input-small'><?php echo $this->Paginator->sort('Código documento', 'Profissional.codigo_documento') ?></th>
            <th class='input-small'><?php echo $this->Paginator->sort('Placa', 'placa') ?></th>
        </tr>
    </thead>
<?php foreach ($logatendimentos as $logatendimento): ?>
    <tr>
        <td><?php echo $logatendimento['Usuario']['apelido'] ?></td>
        <td><?php echo $logatendimento['TipoOperacao']['descricao'] ?></td>
        <td><?php echo $logatendimento['ProfissionalTipo']['descricao'] ?></td>
        <td><?php echo $logatendimento['LogAtendimento']['data_inicio']; ?></td> 
        <td><?php echo $logatendimento['LogAtendimento']['data_inclusao']; ?></td> 
        <td><?php echo $buonny->documento($logatendimento['Profissional']['codigo_documento']) ?></td>
        <td><?php echo comum::formatarPlaca($logatendimento['Veiculo']['placa'])?></td>       
    </tr>
<?php endforeach; ?>
    <tfoot>
        <?php if( isset($artigos) ): ?>
            <tr>
                <td><strong>Total</strong></td>
                <td colspan="7" class="input-xlarge"><strong>
                    <?php 
                        if($this->Paginator->counter('{:count}') > 1)
                            echo $this->Paginator->counter('{:count}')." Vínculos";
                        else
                            echo $this->Paginator->counter('{:count}')." Artigo Criminal";
                    ?></strong>
                </td>
            </tr>
        <?php endif;?>
    </tfoot>
    <tfoot>
        <tr>
            <td colspan="7"><strong>Total</strong> <?php echo $this->Paginator->params['paging']['LogAtendimento']['count']; ?></td>
        </tr>
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