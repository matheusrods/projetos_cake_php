<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
    $total_paginas = $this->Paginator->numbers();    
?>
<table class="table table-condensed table-striped">
    <thead>
        <tr>
            <th ><?php echo $this->Paginator->sort('Código', 'codigo_cliente') ?></th>
            <th ><?php echo $this->Paginator->sort('Razão Social', 'razao_social') ?></th>
            <th ><?php echo $this->Paginator->sort('CPF', 'codigo_documento') ?></th>
            <th ><?php echo $this->Paginator->sort('Nome', 'nome') ?></th>
            <th ><?php echo $this->Paginator->sort('Categoria', 'descricao') ?></th>
            <th ><?php echo $this->Paginator->sort('Contato', 'contato') ?></th>
            <th ><?php echo $this->Paginator->sort('Representante', 'representante') ?></th>
            <th ><?php echo $this->Paginator->sort('Renovar', 'renovar') ?></th>
            <th ><?php echo $this->Paginator->sort('Processado', 'processado') ?></th>
            <th ><?php echo $this->Paginator->sort('Usuario', 'apelido') ?></th>
        </tr>
    </thead>

<?php foreach ($dadosLogRenovacao as $dados): ?>
    <tr>
        <td><?=$dados['0']['codigo_cliente'] ?></td>
        <td><?=$dados['0']['razao_social'] ?></td>
        <td><?=$dados['0']['codigo_documento'] ?></td>
        <td><?=$dados['0']['nome'] ?></td>
        <td><?=$dados['0']['tipo_profissional']?></td>
        <td><?=$dados['0']['contato']?></td>
        <td><?=$dados['0']['representante']?></td>
        <td><?php 
                 if ($dados['0']['renovar'] =='0') {

                        echo "Não";
                     }else{

                        echo "Sim";
                     }
                 

             ?></td>
         <td><?php 
                 if ($dados['0']['processado'] =='0') {

                        echo "Não";
                     }else{

                        echo "Sim";
                     }
                 

             ?></td>
        <td><?=$dados['0']['apelido']?></td>          
      
            <div class="clear"></div>
        </td>
    </tr>
<?php endforeach; ?>
    <tfoot>
        <?php if( isset($dadosLogRenovacao) ): ?>
            <tr>
                <td colspan="10" class="input-xlarge">
                    <strong>Total: <?php echo $this->Paginator->counter('{:count}')?></strong>
                </td>
            </tr>
        <?php  endif;?>
    </tfoot>
</table>

<div class='row-fluid'>
    <div class='numbers span8'>
        <?php if($this->Paginator->counter('{:pages}') > 1): ?>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        <?php endif; ?>
    </div>
    <div class='counter span8'>
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<?php echo $this->Js->writeBuffer(); ?>
