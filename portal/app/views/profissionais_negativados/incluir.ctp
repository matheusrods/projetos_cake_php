<?php echo $this->BForm->create('ProfissionalNegativacao', array('type' => 'post' ,'url' => array('controller' => 'profissionais_negativados','action' => 'incluir')));?>
<?php echo $this->element('profissionais_negativados/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>