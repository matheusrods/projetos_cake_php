<?php echo $this->BForm->create('MotivoRecusa', array('url' => array('controller' => 'motivos_recusa', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('motivos_recusa/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>