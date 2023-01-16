<?php echo $this->BForm->create('Promocao', array('url' => array('controller' => 'promocoes', 'action' => 'editar', $this->passedArgs[0])));?>
<?php echo $this->element('promocoes/fields'); ?>