<?php echo $this->BForm->create('Cat', array('url' => array('controller' => 'cat', 'action' => 'incluir', $codigo_funcionario, $codigo_cliente,$codigo_funcionario_setor_cargo))); ?>
<?php echo $this->element('cat/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 