<?php echo $this->BForm->create('ArtigoCriminal', array('type' => 'post' ,'url' => array('controller' => 'artigos_criminais','action' => 'incluir')));?>
<?php echo $this->element('artigocriminal/fields'); ?>
<?php echo $this->BForm->end(); ?>

