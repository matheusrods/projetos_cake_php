<?php echo $this->BForm->create('TecnicaMedicaoPpra', array('url' => array('controller' => 'tecnicas_medicao','action' => 'incluir_terceiros', $codigo_cliente))); ?>
<?php echo $this->element('tecnicas_medicao/fields_tecnicas_ppra', array('edit_mode' => false)); ?>
<?php echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente));?>
<?php echo $this->BForm->hidden('ativo', array('value' => 1));?>
<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'tecnicas_medicao', 'action' => 'index_terceiros'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>