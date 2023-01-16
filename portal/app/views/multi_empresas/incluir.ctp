<?php echo $this->BForm->create('MultiEmpresa', array('url' => array('controller' => 'multi_empresas','action' => 'incluir'))); ?>
	<?php echo $this->element('multi_empresas/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>