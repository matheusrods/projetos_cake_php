<?php echo $this->BForm->create('Proprietario', array('controller'=>'proprietarios' ,'action' => 'editar',$this->data['Proprietario']['codigo'])); ?>
<?php echo $this->element('proprietarios/fields', array('edit_mode' => true)); ?>
<?php echo $javascript->codeblock('jQuery(document).ready(function() { setup_datepicker(); });'); ?> 
