<?php echo $this->BForm->create('ArtigoCriminal', array('type' => 'post' ,'url' => array('controller' => 'artigos_criminais','action' => 'editar')));?>
<?php echo $this->element('artigocriminal/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>

