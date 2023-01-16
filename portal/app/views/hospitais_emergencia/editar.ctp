<?php echo $this->BForm->create('HospitaisEmergencia', array('url' => array('controller' => 'hospitais_emergencia','action' => 'editar', $codigo, $codigo_cliente, $codigo_unidade), 'type' => 'post')); ?>
	<?php echo $this->Form->hidden('codigo'); ?>
<?php echo $this->element('hospitais_emergencia/fields', array('edit_mode' => true)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 
<?php echo $this->BForm->end(); ?>