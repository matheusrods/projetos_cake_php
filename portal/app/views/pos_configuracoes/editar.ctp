<?php echo $this->BForm->create('PosConfiguracoes', array('url' => array('controller' => 'pos_configuracoes', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('pos_configuracoes/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>