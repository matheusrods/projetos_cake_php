<?php echo $this->BForm->create('TipoAcidente', array('url' => array('controller' => 'tipos_acidentes', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('tipos_acidentes/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>