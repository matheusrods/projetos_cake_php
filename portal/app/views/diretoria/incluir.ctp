<?php echo $this->BForm->create('Diretoria', array('type' => 'post' ,'url' => array('controller' => 'diretoria','action' => 'incluir')));?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'label' => 'Descrição')); ?>
		<?php echo $this->BForm->hidden('ativo', array('value' => 1)); ?>
	</div>
	<div class="form-actions">
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		<?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>
