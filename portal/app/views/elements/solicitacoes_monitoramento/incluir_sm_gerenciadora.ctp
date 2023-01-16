<h4>Gerenciadora de Risco</h4>
<div class='row-fluid inline'>
	<?php echo $this->BForm->hidden('gerenciadora') ?>
	<?php echo $this->BForm->input('razao_social', array('label' => 'Gerenciadora', 'readonly' => true, 'class'=> 'input-xxlarge')) ?>
	<?php echo $this->BForm->input('liberacao', array('label' => 'Nº Liberação', 'readonly' => true, 'class'=> 'input-small')) ?>
</div>