<?php echo $this->BForm->create('RemessaBancaria', array('url' => array('controller' => 'remessa_bancaria', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('remessa_bancaria/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>