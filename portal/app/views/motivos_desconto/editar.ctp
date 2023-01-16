<?php echo $this->BForm->create('MotivosDesconto', array('url' => array('controller' => 'motivos_desconto', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('motivos_desconto/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>