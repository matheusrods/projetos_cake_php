<?php echo $this->BForm->create('WsConfiguracao', array('action' => 'incluir')); ?>
<?php echo $this->element('ws_configuracoes/fields', array('edit_mode' => false)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 