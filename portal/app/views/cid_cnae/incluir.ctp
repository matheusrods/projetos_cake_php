<?php echo $this->BForm->create('CidCnae', array('url' => array('controller' => 'cid_cnae','action' => 'incluir'))); ?>
<?php echo $this->element('cid_cnae/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>