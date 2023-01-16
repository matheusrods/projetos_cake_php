<?php echo $this->BForm->create('OrigemFerramenta', array('url' => array('controller' => 'origem_ferramenta','action' => "incluir",$codigo_cliente))); ?>
<?php echo $this->element('origem_ferramenta/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
