<?php echo $this->BForm->create('TCmatChecklistMotivoAtraso', array('url'=>array('controller' => 'motivos_atrasos_checklist', 'action' => 'incluir'))); ?>
<?php echo $this->element('motivos_atrasos_checklist/fields'); ?>
<?php echo $this->BForm->end(); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() {setup_mascaras();setup_datepicker(); });'); ?>