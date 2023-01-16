<div class='row-fluid inline margin-top-30'>
<?php if(!empty($this->data['Crmb']['codigo'])) echo $this->BForm->input('codigo'); ?>
	<div class="pull-left">
	<label for="">Período</label>
	<?php echo $this->Buonny->input_periodo($this,'Crmb','data_inicial','data_final',false); ?>
	</div>
	<?php echo $this->BForm->input('codigo_medico', array('options' => $medicos, 'empty' => 'Selecione', 'default' => !empty($this->data['Crmb']['codigo_medico'])? $this->data['Crmb']['codigo_medico'] : '')); ?>	
</div>

	
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'clientes_responsaveis_monitoracao_biologicas', 'action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(function() { setup_mascaras(); });
'); ?>
