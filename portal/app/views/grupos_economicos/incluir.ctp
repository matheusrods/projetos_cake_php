<?php echo $this->BForm->create('GrupoEconomico', array('url'=>array('controller' => 'grupos_economicos', 'action' => 'incluir'))); ?>
<?php echo $this->element('grupos_economicos/fields'); ?>