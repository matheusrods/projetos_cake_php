<?php echo $this->BForm->create('Exame', array('url' => array('controller' => 'Exames', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('exames/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>