<?php echo $this->BForm->create('FichaClinica', array('url' => array('controller' => 'fichas_clinicas','action' => 'incluir', $dados['PedidoExame']['codigo']))); ?>
<?php echo $this->BForm->input('codigo_pedido', array('type' => 'hidden', 'value' => $codigoPedidoExame)); ?>
<?php echo $this->BForm->input('codigo_item_exame_aso', array('type' => 'hidden')); ?>
<?php echo $this->element('fichas_clinicas/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>

