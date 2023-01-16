<?php echo $this->BForm->create('FonteGeradora', array('url' => array('controller' => 'fontes_geradoras','action' => 'incluir', 'id' => 'form_fonte'))); ?>
<?php echo $this->element('fontes_geradoras/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>