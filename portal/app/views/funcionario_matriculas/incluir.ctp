<?php echo $this->BForm->create('Funcionario', array('url'=>array('controller' => 'funcionarios', 'action' => 'incluir', $codigo_cliente, $referencia), 'type' => 'post')); ?>
<?php echo $this->element('funcionarios/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>