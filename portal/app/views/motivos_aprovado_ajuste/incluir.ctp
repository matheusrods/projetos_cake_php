<?php echo $this->BForm->create('MotivosAprovadoAjuste', array('url' => array('controller' => 'motivos_aprovado_ajuste','action' => 'incluir'))); ?>
<?php echo $this->element('motivos_aprovado_ajuste/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>