<?php echo $this->BForm->create('AcoesMelhoriasTipo', array('url' => array('controller' => 'acoes_melhorias_tipo','action' => 'editar', $this->passedArgs[0]))); ?>
<?php echo $this->element('acoes_melhorias_tipo/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end();
