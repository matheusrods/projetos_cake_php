<?php echo $this->requestAction('/funcionarios/index/'.$codigo_cliente, array('return')); ?>


<div class='form-actions well'>
    <?php echo $html->link('Voltar para Estrutura', array('controller' => 'clientes_implantacao', 'action' => 'estrutura', $codigo_cliente), array('class' => 'btn')); ?>
</div>
