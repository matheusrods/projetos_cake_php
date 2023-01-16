<?php echo $this->BForm->create('HospitaisEmergencia', array('url' => array('controller' => 'HospitaisEmergencia','action' => 'incluir', $codigo_cliente, $codigo_unidade))); ?>
<?php echo $this->BForm->hidden('codigo_cliente_matriz', array('value' => $codigo_cliente));?>
	<?php echo $this->BForm->hidden('codigo_cliente_unidade', array('value' => $codigo_unidade));?>
<?php echo $this->element('hospitais_emergencia/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 
<?php echo $this->BForm->end(); ?>