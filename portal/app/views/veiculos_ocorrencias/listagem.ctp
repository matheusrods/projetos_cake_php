<?php if( isset($codigo_veiculo) && $codigo_veiculo > 0 ) : ?>
<div class='actionbar-right'>
    <?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir', $codigo_veiculo), array('escape' => false, 'class' => 'btn btn-success', 'title' => 'Cadastrar Novas Ocorrências')); ?>
</div>
<?php endif; ?>

<?php if( count($ocorrencias) > 0 ) : ?>
    <?php
    echo $paginator->options(array('update' => 'div.lista'));
    $total_paginas = $this->Paginator->numbers(); ?>
    <table class="table table-condensed table-striped">
        <thead>
            <tr>
                <th><?php echo $this->Paginator->sort('Data ocorrência', 'data_ocorrencia') ?></th>
                <th><?php echo $this->Paginator->sort('Ocorrência', 'descricao') ?></th>
                <th><?php echo $this->Paginator->sort('Observação', 'observacao') ?></th>
                <th><?php echo $this->Paginator->sort('Data Inclusão', 'data_inclusao') ?></th>
                <th><?php echo $this->Paginator->sort('Usuário', 'apelido') ?></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <?php foreach ($ocorrencias as $ocorrencia): ?>  
        <tr>        
            <td><?php echo $ocorrencia['VeiculoOcorrencia']['data_ocorrencia'] ?></td>
            <td><?php echo $ocorrencia['TipoOcorrencia']['descricao'] ?></td>
            <td><?php echo $ocorrencia['VeiculoOcorrencia']['observacao'] ?></td>
            <td><?php echo $ocorrencia['VeiculoOcorrencia']['data_inclusao'] ?></td>
            <td><?php echo $ocorrencia['Usuario']['apelido'] ?></td>
            <td>
                <?php echo $html->link('', array('controller' => 'veiculos_ocorrencias', 'action' => 'editar', $ocorrencia['VeiculoOcorrencia']['codigo']), array('class' => 'icon-edit dialog', 'title' => 'editar')) ?>
            </td> 
            <td>            
                <?php echo $html->link('', array('controller' => 'veiculos_ocorrencias', 'action' => 'excluir', $ocorrencia['VeiculoOcorrencia']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir ocorrencia Criminal'), 'Confirma exclusão?'); ?>
            </td>     
            <?php echo $this->Form->input('endereco_'.$ocorrencia['VeiculoOcorrencia']['codigo'], array('type' => 'hidden', 'value' => $ocorrencia['VeiculoOcorrencia']['codigo'])) ?> 
        <div class="clear"></div>
    </td>
    </tr>
    <?php endforeach; ?>
    <tfoot>
        <?php if( isset($ocorrencias) ): ?>
        <tr>
            <td><strong>Total</strong></td>
            <td colspan="6" class="input-xlarge"><strong>
                    <?php
                        if($this->Paginator->counter('{:count}') > 1)
                            echo $this->Paginator->counter('{:count}')." Ocorrencias de Veículos";
                        else
                            echo $this->Paginator->counter('{:count}')." Ocorrencias de Veículos";
                        ?></strong>
            </td>
        </tr>
        <?php endif; ?>
    </tfoot>


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
<?php else: ?>
    <?php if ($filtrado === true ): ?>
        <div class="alert">Não constam ocorrências para esta Placa.</div>
    <?php endif; ?>
<?php endif; ?>