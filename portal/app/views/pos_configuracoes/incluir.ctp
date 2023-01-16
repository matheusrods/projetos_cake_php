<?php echo $this->BForm->create('PosConfiguracoes', array('url' => array('controller' => 'pos_configuracoes','action' => 'incluir'))); ?>
<?php echo $this->element('pos_configuracoes/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>