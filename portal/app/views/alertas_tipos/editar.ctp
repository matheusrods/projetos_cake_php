<?php echo $this->BForm->create('AlertaTipo',array('url' => array('controller' => 'alertas_tipos','action' => 'editar'), 'type' => 'POST')) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->hidden('codigo')?>
		<?php echo $this->BForm->input('descricao',array('class' => 'input-xxlarge', 'label' => 'Descrição')) ?>
		<?php echo $this->BForm->input('codigo_alerta_agrupamento',array('class' => 'input-large', 'label' => 'Grupo do alerta','options' => $agrupamento)) ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('interno',array('type'=>'checkbox','value'=>'S', 'class' => 'input-large', 'label' => 'Alerta Interno')) ?>
	</div>
	<div class="form-actions">
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		<?php echo $html->link('Voltar',array('controller'=>'alertas_tipos','action'=>'index') , array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>