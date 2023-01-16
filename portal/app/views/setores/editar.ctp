<?php echo $this->BForm->create('Setor', array('url' => array('controller' => 'setores', 'action' => 'editar', $codigo_cliente, $this->data['Setor']['codigo'], $referencia, $terceiros_implantacao), 'type' => 'post')); ?>
<?php echo $this->element('setores/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>