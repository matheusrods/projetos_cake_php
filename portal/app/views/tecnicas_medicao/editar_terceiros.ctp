<?php echo $this->BForm->create('TecnicaMedicaoPpra', array('url' => array('controller' => 'tecnicas_medicao', 'action' => 'editar_terceiros', $codigo, $codigo_cliente), 'type' => 'post')); ?>
<?php echo $this->element('tecnicas_medicao/fields_tecnicas_ppra', array('edit_mode' => true)); ?>
<?php echo $this->BForm->hidden('codigo_cliente', array('value' => $codigo_cliente));?>
<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'tecnicas_medicao', 'action' => 'index_terceiros'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>