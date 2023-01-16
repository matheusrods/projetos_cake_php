<?php echo $this->BForm->create('AlertaTipo',array('url' => array('controller' => 'alertas_tipos','action' => 'incluir'), 'type' => 'POST')) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('descricao',array('class' => 'input-large', 'label' => 'Descrição')) ?>
		<?php echo $this->BForm->input('codigo_alerta_agrupamento',array('class' => 'input-large', 'label' => 'Grupo do alerta','options' => $agrupamento)) ?>
	</div>
	<div class="form-actions">
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		<?php echo $html->link('Voltar',array('controller'=>'alertas_tipos','action'=>'index') , array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>
