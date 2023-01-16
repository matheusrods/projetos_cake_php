
<?php echo $this->BForm->create('TarefaDesenvolvimento', array('autocomplete' => 'off', 'url' => array('controller' => 'sistemas', 'action' => 'incluir_tarefas_desenvolvimento'))); ?>
		
	<div class="well">
		<?php echo $this->BForm->hidden('status',array('value' => 1)) ?>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('titulo',array('placeholder'=>'Título','maxlength'=>'false','class'=>'input-large','label'=>'Título'));?>
			<?php echo $this->BForm->input('tipo',array('label' => 'Tipo', 'empty' => 'Tipo','options' => $tipo, 'default' => 1,'class'=>'input-small'));?>		
		</div>	
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge', 'placeholder' => 'Descrição', 'label' => 'Descrição da Tarefa', 'type' => 'textarea')) ?>
		</div>	
	</div>
		<div class='form-actions'>
    	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    	<?= $html->link('Voltar', array('controller'=>'sistemas','action' => 'tarefas_desenvolvimento'), array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>
</div>
