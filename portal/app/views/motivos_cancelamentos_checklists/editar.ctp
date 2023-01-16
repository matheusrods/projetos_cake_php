<?php echo $this->BForm->create('TMcchMotivoCancelChecklist',array('url' => array('controller' => 'motivos_cancelamentos_checklists','action' => 'editar'), 'type' => 'POST')) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->hidden('mcch_codigo')?>
		<?php echo $this->BForm->input('mcch_descricao',array('class' => 'input-large', 'label' => 'Descrição')) ?>
	</div>
	<div class="form-actions">
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	<?php echo $html->link('Voltar',array('controller'=>'motivos_cancelamentos_checklists','action'=>'index') , array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>
