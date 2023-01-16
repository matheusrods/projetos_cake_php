<?php echo $this->BForm->create('ConfiguracaoComissao', array( 'type' => 'post','url' => array('controller' => 'configuracao_comissoes','action' => 'incluir'))) ?>
	<?php echo $this->element('configuracao_comissoes/fields'); ?>
<?php echo $this->BForm->end(); ?>