<?php echo $this->BForm->create('Prestador', array('url' => array('controller' => 'prestadores','action' => 'editar', $this->passedArgs[0]))); ?>
<?php echo $this->element('prestadores/fields', array('edit_mode' => true)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 