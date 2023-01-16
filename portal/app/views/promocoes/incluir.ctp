<?php echo $this->BForm->create('Promocao', array('url' => array('controller' => 'promocoes', 'action' => 'incluir')));?>
<?php echo $this->element('promocoes/fields'); ?>