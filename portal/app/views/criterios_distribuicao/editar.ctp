<?php echo $this->BForm->create('TCdisCriterioDistribuicao', array('url' => array('controller' => 'criterios_distribuicao','action' => 'editar')));?>
<?php echo $this->element('criterios_distribuicao/fields'); ?>
<?php echo $this->BForm->end() ?>