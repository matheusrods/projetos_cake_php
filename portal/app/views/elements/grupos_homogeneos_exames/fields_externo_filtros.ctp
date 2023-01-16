<div class="row-fluid inline">
	<?php echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false)) ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo', array('class' => 'input-medium', 'label' => 'Código Ghe', 'type' => 'text')) ?>
	<?php echo $this->BForm->input('descricao', array('class' => 'input-xxlarge', 'label' => 'Descrição')) ?>  
	<?php echo $this->BForm->input('GrupoHomogeneoExterno.codigo_externo', array('class' => 'input-small', 'label' => 'Código Externo')); ?>
</div>