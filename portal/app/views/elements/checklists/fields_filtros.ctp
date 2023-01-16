<div class="row-fluid inline">
	<?php echo $this->Buonny->input_periodo($this,'TCveiChecklistVeiculo') ?>
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', False,'TCveiChecklistVeiculo') ?>
	<?php echo $this->BForm->input('veic_placa', array('class' => 'placa-veiculo input-mini', 'placeholder' => 'Placa', 'label' => false)); ?>
	<?php echo $this->Buonny->input_referencia($this, '#TCveiChecklistVeiculoCodigoCliente', 'TCveiChecklistVeiculo','refe_codigo', false,'Alvo Origem',false,false) ?>
	<?php echo $this->Buonny->input_referencia($this, '#TCveiChecklistVeiculoCodigoCliente', 'TCveiChecklistVeiculo','cvei_alvo_valido_refe_codigo', false,'Alvo Checklist',false,false) ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('cvei_usuario_adicionou', array('class' => 'input-medium', 'placeholder' => 'Operador', 'label' => false)); ?>
	<?php echo $this->BForm->input('cvei_status', array('class' => 'input-small', 'placeholder' => 'Status', 'label' => false, 'options' => $status, 'empty' => 'Status')); ?>
	<?php echo $this->BForm->input('veic_tvei_codigo', array('class' => 'input-medium', 'options' => $veiculos_tipos,'label' => false,'empty' => 'Selecione o Tipo de Veiculo')); ?>
</div>