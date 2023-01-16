<?php echo $this->BForm->create('RiscosEsocial', array('url' => array('controller' => 'riscos_esocial','action' => 'editar', $this->passedArgs[0]))); ?>
<?php echo $this->element('riscos_esocial/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end();
