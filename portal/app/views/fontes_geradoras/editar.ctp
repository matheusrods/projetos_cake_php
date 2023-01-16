<?php echo $this->BForm->create('FonteGeradora', array('url' => array('controller' => 'fontes_geradoras', 'action' => 'editar', $codigo, 'id' => 'form_fonte'), 'type' => 'post')); ?>
<?php echo $this->element('fontes_geradoras/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>