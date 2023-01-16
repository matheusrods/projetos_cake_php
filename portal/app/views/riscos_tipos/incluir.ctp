<?php echo $this->BForm->create('RiscosTipo', array('url' => array('controller' => 'riscos_tipos','action' => 'incluir'))); ?>
<?php echo $this->element('riscos_tipos/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
