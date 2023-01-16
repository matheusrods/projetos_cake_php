<?php echo $this->BForm->create('WsConfiguracao', array('action' => 'editar', $this->passedArgs[0] )); ?>
<?php echo $this->element('ws_configuracoes/fields', array('edit_mode' => true)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 