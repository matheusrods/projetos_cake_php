<div class='row-fluid inline'>
	<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true,'ChecklistViagem') ?>
	<?php echo $this->Buonny->input_periodo($this,'ChecklistViagem','data_inicial','data_final','Período') ?>
	<?php echo $this->BForm->input('loadplan', array('class' => 'input-small', 'placeholder' => 'Loadplan', 'label' => 'Loadplan', 'type' => 'text')) ?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'label' => 'Placa Veículo', 'placeholder' => 'Placa')) ?>

	<?php echo $this->BForm->input('placa_carreta', array('class' => 'input-small placa-veiculo', 'label' => 'Placa Carreta', 'placeholder' => 'Placa Carreta')) ?>
	<?php echo $this->BForm->input('nf', array('class' => 'input-small', 'placeholder' => 'NF', 'label' => 'NF', 'type' => 'text')) ?>

	<?php echo $this->BForm->input('pedido_cliente', array('class' => 'input-small', 'placeholder' => 'Pedido Cliente', 'label' => 'Pedido', 'type' => 'text')) ?>

	<?php echo $this->BForm->input('status', array('class' => 'input-small', 'placeholder' => 'Status', 'label' => 'Status', 'options'=>$status, 'empty'=>'Status')) ?>

	<?php echo $this->BForm->input('viag_codigo_sm', array('class' => 'input-small', 'placeholder' => 'SM', 'label' => 'SM', 'type' => 'text')) ?>
</div>