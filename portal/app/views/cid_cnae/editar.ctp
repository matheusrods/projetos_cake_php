<?php echo $this->BForm->create('CidCnae', array('url' => array('controller' => 'cid_cnae', 'action' => 'editar'), 'type' => 'post')); ?>
<?php echo $this->element('cid_cnae/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>