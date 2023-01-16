<?php echo $this->BForm->create('RiscoExame', array('url' => array('controller' => 'riscos_exames', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('riscos_exames/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>