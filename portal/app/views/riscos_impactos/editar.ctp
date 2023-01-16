<?php echo $this->BForm->create('RiscosImpactos', array('url' => array('controller' => 'riscos_impactos','action' => 'editar', $this->passedArgs[0]))); ?>
<?php echo $this->element('riscos_impactos/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end();
