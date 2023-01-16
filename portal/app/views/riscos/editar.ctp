<?php echo $this->BForm->create('Risco', array('action' => 'editar', $this->passedArgs[0] )); ?>
<?php echo $this->element('riscos/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>