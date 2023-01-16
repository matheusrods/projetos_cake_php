<?php echo $this->BForm->create('ObjetivoComercial', array('type' => 'post' ,'url' => array('controller' => 'objetivos_comerciais','action' => 'incluir')));?>
<?php echo $this->element('objetivo_comercial/fields'); ?>
<?php echo $this->BForm->end(); ?>