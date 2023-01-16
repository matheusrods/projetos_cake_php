<?php echo $this->BForm->create('ObjetoAcl', array('url' => array('controller' => 'objetos_acl', 'action' => 'editar', $this->passedArgs[0]))); ?>
<?php echo $this->element('objetos_acl/fields'); ?>