<?php echo $this->BForm->create('PlanoDeSaude', array('url' => array('controller' => 'planos_de_saude', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('planos_de_saude/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>