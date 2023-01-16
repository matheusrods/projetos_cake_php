<?php 
    echo $this->Paginator->options(array('update' => '.lista')); 
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th class="input-xlarge">Código</th>
            <th class="input-xxlarge">Descrição</th>
            <th style="width:13px"></th>
            <th style="width:13px"></th>
            <th style="width:13px"></th>
           
        </tr>
    </thead>
    <tbody>
        <?php $class = null?>

        <?php foreach($produtos as $produto): ?>
        <tr>
            <td class="input-xlarge"><?php echo $produto['TProdProduto']['prod_codigo'] ?></td>
            <td class="input-xxlarge"><?php echo $produto['TProdProduto']['prod_descricao'] ?></td>
            <td>
                <?php echo $this->Html->link('', array('action' => 'editar', $produto['TProdProduto']['prod_codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
            </td>   
            <td>
                <?php if($produto['TProdProduto']['prod_status']== true):?>
                    <?php $class = 'icon-thumbs-down'?>
                   <?php $titulo = 'Inativar Mercadoria'?>
                         
                <?php elseif($produto['TProdProduto']['prod_status']== false): ?>
                    <?php $class =  'icon-thumbs-up'?>
                    <?php $titulo =  'Ativar Mercadoria'?>
                    
                <?php endif; ?>  
                
                <?php echo $this->Html->link('', 'javascript:void(0)',array('class' => $class, 'escape' => false, 'title'=>$titulo,'onclick' => "atualizaStatusMercadorias('{$produto['TProdProduto']['prod_codigo']}','{$produto['TProdProduto']['prod_status']}')"));?>
            </td>
            <td>    
                <?php if($produto['TProdProduto']['prod_status']== true):?>
                    <span class="badge-empty badge badge-success" title="Ativo"></span>
                    <?php $class = 'icon-thumbs-up'?>
                <?php elseif($produto['TProdProduto']['prod_status']== false): ?>
                    <span class="badge-empty badge badge-important" title="Inativo"></span>
                   <?php $class =  'icon-thumbs-down'?>
                <?php endif; ?>  
            </td>
            
        </tr>
        <?php endforeach; ?>        
    </tbody>
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

