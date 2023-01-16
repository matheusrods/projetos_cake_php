 <div class="well">
	 <div class='row-fluid inline'>
		<?php if(empty($this->passedArgs)): ?>
			<?php echo $this->BForm->hidden('codigo'); ?>
		<?php endif;  ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('data_inicio', array('type' => 'text', 'label' => 'Data Inicio', 'class' => 'input-large data')); ?>
		<?php echo $this->BForm->input('data_fim', array('type' => 'text', 'label' => 'Data Fim', 'class' => 'input-large data')); ?>	
	</div>
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->input('codigo_risco', array('options' => $array_risco, 'empty' => '--- Selecione ---', 'label' => 'Risco', 'class' => 'input-xxlarge')); ?>
	</div>
	<div class='row-fluid inline'>	
		<?php echo $this->Buonny->input_codigo_cliente($this, 'unidade', 'Cliente', 'Cliente', 'Medicao'); ?>
	</div>
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->input('codigo_setor', array('options' => $array_setor, 'empty' => '--- Selecione ---', 'label' => 'Setor', 'class' => 'input-xxlarge')); ?>
	</div>
	<div class='row-fluid inline'>	
		<?php echo $this->BForm->input('codigo_cargo', array('options' => $array_cargo, 'empty' => '--- Selecione ---', 'label' => 'Cargo', 'class' => 'input-xxlarge')); ?>
	</div>	
</div>
<div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_mascaras();
		setup_datepicker(); 
	});
'); ?>