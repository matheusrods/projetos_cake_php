<?php echo $this->BForm->create('Cid', array('url' => array('controller' => 'cid','action' => 'incluir'))); ?>
<?php echo $this->element('cid/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>