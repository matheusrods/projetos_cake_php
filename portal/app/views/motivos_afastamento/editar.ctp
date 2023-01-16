<?php echo $this->BForm->create('MotivoAfastamento', array('url' => array('controller' => 'motivos_afastamento', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('motivos_afastamento/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>