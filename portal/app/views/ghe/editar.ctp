<?php echo $this->BForm->create('Ghe', array('url' => array('controller' => 'ghe', 'action' => 'editar', $this->passedArgs[0]))); ?>
<?php echo $this->element('ghe/fields-edit', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end();
