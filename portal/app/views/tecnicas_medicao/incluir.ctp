<?php echo $this->BForm->create('TecnicaMedicao', array('url' => array('controller' => 'tecnicas_medicao','action' => 'incluir'))); ?>
<?php echo $this->element('tecnicas_medicao/fields', array('edit_mode' => false)); ?>
<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'tecnicas_medicao', 'action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>