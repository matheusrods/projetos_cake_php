<?php echo $this->BForm->create('MotivoAtendimento',array('url' => array('controller' => 'motivos_atendimentos','action' => 'editar'), 'type' => 'POST')) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->hidden('codigo')?>
		<?php echo $this->BForm->input('descricao',array('class' => 'input-large', 'label' => 'Descrição')) ?>
	</div>
	<div class="form-actions">
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	<?php echo $html->link('Voltar',array('controller'=>'motivos_atendimentos','action'=>'index') , array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>
