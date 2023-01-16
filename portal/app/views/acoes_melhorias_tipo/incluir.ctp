<?php echo $this->BForm->create('AcoesMelhoriasTipo', array('url' => array('controller' => 'acoes_melhorias_tipo','action' => 'incluir',$codigo_cliente))); ?>
<?php echo $this->element('acoes_melhorias_tipo/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
