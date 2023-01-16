<?php echo $this->BForm->create('MotivoRecusaExame', array('url' => array('controller' => 'motivos_recusa','action' => 'exames_incluir'))); ?>
<?php echo $this->element('motivos_recusa/exames_fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>