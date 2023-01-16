<h4>Embarcador / Transportador</h4>
<div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('embarcador') ?>
	<?php echo $this->BForm->input('embarcador_nome', array('label' => 'Embarcador', 'readonly' => true, 'class'=> 'input-xxlarge', 'value' => $embarcador['ClientEmpresa']['Raz_Social'])) ?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('transportador') ?>
	<?php echo $this->BForm->input('transportador_nome', array('label' => 'Transportador', 'readonly' => true, 'class'=> 'input-xxlarge', 'value' => $transportador['ClientEmpresa']['Raz_Social'])) ?>
</div>