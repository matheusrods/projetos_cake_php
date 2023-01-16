<?php echo $this->BForm->create('FonteGeradoraExterno', array('url' => array('controller' => 'fontes_geradoras', 'action' => 'editar_externo/'.$codigo_cliente.'/'.$this->data['FonteGeradora']['codigo']), 'type' => 'post')); ?>
<?php echo $this->element('fontes_geradoras/fields_externo', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>