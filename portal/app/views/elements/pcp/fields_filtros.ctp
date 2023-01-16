<div class="row-fluid inline">
	<div style=<?php echo (empty($authUsuario['Usuario']['codigo_cliente']) ? "''": "'display:none'") ?> >
		<?php echo $this->Buonny->input_codigo_cliente($this,'codigo_cliente','Cliente',true,'TIpcpInformacaoPcp'); ?>
	</div>
	<?php echo $this->BForm->input('rota', array('class' => 'input-medium', 'label' => 'Rota')) ?>
	<?php echo $this->BForm->input('loja', array('class' => 'input-medium', 'label' => 'Loja')) ?>
	<?php echo $this->BForm->input('tipo_carga', array('class' => 'input-medium', 'label' => 'Tipo da carga')) ?>
	<?php echo $this->BForm->input('tipo_veiculo_geral', array('class' => 'input-medium', 'label' => 'Tipo Veículo')) ?>
	<?php echo $this->BForm->input('bandeira', array('class' => 'input-mini', 'label' => 'Bandeira')) ?>
</div>