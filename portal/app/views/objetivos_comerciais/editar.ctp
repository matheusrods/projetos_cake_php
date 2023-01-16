<?php echo $this->BForm->create('ObjetivoComercial', array('type' => 'post' ,'url' => array('controller' => 'objetivos_comerciais','action' => 'editar',$this->data['ObjetivoComercial']['codigo'])));?>
<?php echo $this->element('objetivo_comercial/fields'); ?>
<?php echo $this->BForm->end(); ?>