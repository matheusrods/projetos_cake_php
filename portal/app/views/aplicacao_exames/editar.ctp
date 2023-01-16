<?php echo $this->BForm->create('AplicacaoExame', array('url' => array('controller' => 'aplicacao_exames', 'action' => 'editar', $codigo_cliente_alocacao, $codigo_setor, $codigo_cargo,'null',$referencia), 'type' => 'POST')); ?>	
	<?php echo $this->element('aplicacao_exames/fields', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>