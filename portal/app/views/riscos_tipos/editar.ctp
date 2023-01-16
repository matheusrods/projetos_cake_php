<?php echo $this->BForm->create('RiscosTipo', array('url' => array('controller' => 'riscos_tipos','action' => 'editar', $this->passedArgs[0]))); ?>
<?php echo $this->element('riscos_tipos/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end();
