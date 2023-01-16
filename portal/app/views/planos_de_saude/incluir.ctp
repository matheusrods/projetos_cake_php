<?php echo $this->BForm->create('PlanoDeSaude', array('url' => array('controller' => 'planos_de_saude','action' => 'incluir'))); ?>
<?php echo $this->element('planos_de_saude/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>