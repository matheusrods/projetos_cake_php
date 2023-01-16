<?php echo $this->BForm->create('MotivosDesconto', array('url' => array('controller' => 'motivos_desconto','action' => 'incluir'))); ?>
<?php echo $this->element('motivos_desconto/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>