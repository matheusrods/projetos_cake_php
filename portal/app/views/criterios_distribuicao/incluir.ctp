<?php echo $this->BForm->create('TCdisCriterioDistribuicao', array('url' => array('controller' => 'criterios_distribuicao','action' => 'incluir')));?>
: <?php $this->addScript($this->Buonny->link_js('search')) ?>
<?php echo $this->element('criterios_distribuicao/fields'); ?>
<?php echo $this->BForm->end() ?>
