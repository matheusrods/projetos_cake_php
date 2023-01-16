<?php echo $this->BForm->create('Usuario', array('type' => 'post' ,'url' => array('controller' => 'usuarios','action' => 'diretoria_usuario_editar')));?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('codigo_exibicao', array('disabled'=> true,'options' => $gestores,'class' => 'input-xlarge', 'label' => 'Gestor')); ?>
		<?php echo $this->BForm->input('codigo_diretoria', array('empty' => 'Selecione a Diretoria','options' => $diretorias,'class' => 'input-xlarge', 'label' => 'Diretoria')); ?>
		<?php echo $this->BForm->input('codigo'); ?>

	</div>
	<div class="form-actions">
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		<?= $html->link('Voltar', array('action' => 'diretoria_usuario'), array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>
