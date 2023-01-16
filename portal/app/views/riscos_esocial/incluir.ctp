<?php echo $this->BForm->create('RiscosEsocial', array('url' => array('controller' => 'riscos_esocial','action' => 'incluir'))); ?>
<?php echo $this->element('riscos_esocial/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
