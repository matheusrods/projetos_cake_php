<?php echo $this->BForm->create('ListaDePreco', array('url' => array('controller' => 'listas_de_preco', 'action' => 'editar', $this->passedArgs[0] ))); ?>
<?php echo $this->element('listas_de_preco/fields'); ?>