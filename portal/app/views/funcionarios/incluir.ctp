<?php echo $this->BForm->create('Funcionario', array('url' => array('controller' => 'funcionarios', 'action' => 'incluir', $codigo_cliente, $referencia, $terceiros_implantacao), 'type' => 'post')); ?>
<?php echo $this->BForm->input('edit_mode', array('type' => 'hidden', 'value' => 0)); ?>
<?php echo $this->element('funcionarios/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>