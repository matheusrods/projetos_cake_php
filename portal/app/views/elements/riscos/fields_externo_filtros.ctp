<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false)) ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo', array('class' => 'input-medium', 'label' => 'Código Risco', 'type' => 'text')) ?>
	<?php echo $this->BForm->input('nome_agente', array('class' => 'input-xxlarge', 'label' => 'Risco')) ?>  
	<?php echo $this->BForm->input('ativo', array('class' => 'input-small', 'label' => 'Status', 'options' => array('0' => 'Inativos', '1' => 'Ativos'), 'empty' => 'Selecione', 'default' => ' ')); ?>
	<?php echo $this->BForm->input('RiscoExterno.codigo_externo', array('class' => 'input-small', 'label' => 'Código Externo')); ?>
</div>