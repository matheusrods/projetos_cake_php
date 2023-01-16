<?php echo $this->BForm->create('TipoAcidente', array('url' => array('controller' => 'tipos_acidentes','action' => 'incluir'))); ?>
<?php echo $this->element('tipos_acidentes/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>