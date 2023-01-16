
<div class='row-fluid inline margin-top-30'>
<?php if(!empty($this->data['Crra']['codigo'])) echo $this->BForm->input('codigo'); ?>
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', null, 'Código do Cliente', 'Crra'); ?>
	
	<?php echo $this->BForm->input('codigo_medico', array('options' => $medicos, 'empty' => 'Selecione', 'default' => !empty($this->data['Crra']['codigo_medico'])? $this->data['Crra']['codigo_medico'] : '', 'label' => 'Código do Profissional', 'style' => 'width:420px;', 'div' => array('style' => 'margin-left:30px'))); ?>	
	
	<div class="pull-left" style="margin-left:30px">
	<label for="">Período</label>
		<?php echo $this->Buonny->input_periodo($this,'Crra','data_inicial','data_final',false); ?>
	</div>
</div>

	
 <div class='form-actions'>
	 <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	 <?= $html->link('Voltar', array('controller' => 'clientes_responsaveis_registros_ambientais', 'action' => 'index'), array('class' => 'btn')); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	$(function() { setup_mascaras(); });
'); ?>