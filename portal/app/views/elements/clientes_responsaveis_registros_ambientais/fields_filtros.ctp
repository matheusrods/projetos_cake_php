<div class="row-fluid inline">
<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', null, 'Código do Cliente', 'Crra'); ?>

<?php echo $this->BForm->input('medico', array('label' => 'Nome do Profissional', 'style' => 'width:420px;', 'div' => array('style' => 'margin-left:15px'))); ?>	

<?php echo $this->BForm->input('codigo_conselho_profissional', array('class' => 'input-mini', 'label' => 'Conselho', 'options' => $conselho_profissional, 'empty' => '', 'default' => '')) ?>  
<?php echo $this->BForm->input('numero_conselho', array('class' => 'input-medium', 'label' => 'Número do Conselho')) ?>  

	<div style="margin-left:15px; float:left">
		<label for="">Período</label>
			<?php echo $this->Buonny->input_periodo($this,'Crra','data_inicial','data_final',false); ?>
	</div>
</div>        