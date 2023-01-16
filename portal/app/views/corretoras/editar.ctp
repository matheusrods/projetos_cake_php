<?php echo $this->BForm->create('Corretora', array('controller' => 'corretoras_contatos','action' => 'editar', $this->passedArgs[0] )); ?>
<?php echo $this->element('corretoras/fields', array('edit_mode' => true)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras(); });'); ?> 