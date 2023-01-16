<?php echo $this->BForm->create('TPtvePerifericoTipoVeiculo', array('type' => 'post' ,'url' => array('controller' => 'vinculo_veiculo_periferico','action' => 'incluir')));?>
<?php echo $this->element('vinculo_veiculo_periferico/fields'); ?>
<?php echo $this->BForm->end(); ?>
