<?php echo $this->BForm->create('Atestado', array('url' => array('controller' => 'atestados','action' => 'incluir', $this->passedArgs[0], $this->passedArgs[1]))); ?>
<?php echo $this->element('atestados/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>