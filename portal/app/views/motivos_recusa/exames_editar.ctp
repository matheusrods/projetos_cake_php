<?php echo $this->BForm->create('MotivoRecusaExame', array('url' => array('controller' => 'motivos_recusa', 'action' => 'exames_editar', $mrexame['MotivoRecusaExame']['codigo']), 'type' => 'post')); ?>
<?php echo $this->BForm->hidden('MotivoRecusaExame.codigo', array('value' => $mrexame['MotivoRecusaExame']['codigo'])); ?>
<?php echo $this->element('motivos_recusa/exames_fields'); ?>
<?php echo $this->BForm->end(); ?>