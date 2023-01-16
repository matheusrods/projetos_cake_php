<?php $pagador['ClientEmpresa']['Raz_Social'] = isset($pagador['ClientEmpresa']['Raz_Social']) ? $pagador['ClientEmpresa']['Raz_Social'] : NULL ?>
<h4>Cliente</h4>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('codigo_cliente', array('label' => false, 'readonly' => true, 'class' => 'input-small')) ?>
	<?php echo $this->BForm->input('cliente_tipo', array('label' => false, 'readonly' => true, 'class' => 'input-xlarge', 'value' => $pagador['ClientEmpresa']['Raz_Social'])) ?>
	
</div>