<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th ><?php echo $this->Paginator->sort('Código', 'codigo') ?></th>
            <th ><?php echo $this->Paginator->sort('Número do Artigo', 'nome') ?></th>
            <th ><?php echo $this->Paginator->sort('Descrição', 'descricao') ?></th>
            <th ><?php echo $this->Paginator->sort('Vigente', 'vigente') ?></th>
            <th ><?php echo $this->Paginator->sort('Data da Vigência', 'data_vigencia') ?></th>
            <th></th>
            <th></th>
        </tr>
    </thead>

<?php foreach ($artigos as $artigo): ?>
    <tr>
        <td><?php echo $artigo['ArtigoCriminal']['codigo'] ?></td>
        <td><?php echo $artigo['ArtigoCriminal']['nome'] ?></td>
        <td><?php echo $artigo['ArtigoCriminal']['descricao'] ?></td>
        <td><?php 
                 if ($artigo['ArtigoCriminal']['vigente'] =='0') {

                        echo "Não";
                     }else{

                        echo "Sim";
                     }
                 

             ?></td>
        <td><?php echo date('d/m/Y', strtotime(str_replace('/', '-', $artigo['ArtigoCriminal']['data_vigencia']))); ?></td>
        <td>
            <?php echo $html->link('', array('controller' => 'artigos_criminais', 'action' => 'editar', $artigo['ArtigoCriminal']['codigo']), array('class' => 'icon-edit dialog', 'title' => 'editar')) ?>
        </td> 
        <td>   
            
            <?php echo $html->link('', array('controller' => 'artigos_criminais', 'action' => 'excluir', $artigo['ArtigoCriminal']['codigo']), array('class' => 'icon-trash', 'title' => 'Excluir Artigo Criminal'), 'Confirma exclusão?'); ?>
        </td>     
            <?php echo $this->Form->input('endereco_'.$artigo['ArtigoCriminal']['codigo'], array('type' => 'hidden', 'value' => $artigo['ArtigoCriminal']['codigo'])) ?> 
            <div class="clear"></div>
        </td>
    </tr>
<?php endforeach; ?>
    <tfoot>
        <?php if( isset($artigos) ): ?>
            <tr>
                <td><strong>Total</strong></td>
                <td colspan="6" class="input-xlarge"><strong>
                    <?php 
                        if($this->Paginator->counter('{:count}') > 1)
                            echo $this->Paginator->counter('{:count}')." Artigos Criminais";
                        else
                            echo $this->Paginator->counter('{:count}')." Artigo Criminal";
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
