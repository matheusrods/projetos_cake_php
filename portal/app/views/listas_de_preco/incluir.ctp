<?php echo $this->BForm->create('ListaDePreco', array('url' => array('controller' => 'listas_de_preco', 'action' => 'incluir'))); ?>
<?php echo $this->element('listas_de_preco/fields'); ?>