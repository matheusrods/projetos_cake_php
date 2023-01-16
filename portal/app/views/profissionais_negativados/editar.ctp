<?php echo $this->BForm->create('ProfissionalNegativacao', array('type' => 'post' ,'url' => array('controller' => 'profissionais_negativados','action' => 'editar',$this->passedArgs[0])));?>
<?php echo $this->element('profissionais_negativados/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>
