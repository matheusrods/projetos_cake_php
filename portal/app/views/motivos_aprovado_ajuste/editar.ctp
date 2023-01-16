<?php echo $this->BForm->create('MotivosAprovadoAjuste', array('url' => array('controller' => 'motivos_aprovado_ajuste', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('motivos_aprovado_ajuste/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>