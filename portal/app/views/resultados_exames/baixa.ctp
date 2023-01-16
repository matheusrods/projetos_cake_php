<?php echo $this->BForm->create('ItemPedidoExameBaixa', array('url' => array('controller' => 'itens_pedidos_exames_baixa', 'action' => 'baixa', $codigo_pedidos_exames))); ?>
<?php echo $this->element('itens_pedidos_exames_baixa/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>