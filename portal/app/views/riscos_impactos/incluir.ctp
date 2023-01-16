<?php echo $this->BForm->create('RiscosImpactos', array('url' => array('controller' => 'riscos_impactos','action' => 'incluir'))); ?>
<?php echo $this->element('riscos_impactos/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
