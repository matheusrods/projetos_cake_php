<?php echo $this->BForm->create('OrigemFerramenta', array('url' => array('controller' => 'origem_ferramenta','action' => 'editar', $this->passedArgs[0]))); ?>
<?php echo $this->element('origem_ferramenta/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end();
