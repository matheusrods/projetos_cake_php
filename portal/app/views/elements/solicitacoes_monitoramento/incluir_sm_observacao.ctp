<h4>Observação</h4>
<div class='row-fluid inline'>
	<i>Numero de caracteres disponíveis <b class="count-obs"></b></i>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('observacao', array('label' => false, 'type' => 'textarea', 'class' => 'input-xxlarge', 'rows' => 5)); ?>
</div>

<?php echo $this->Javascript->codeBlock('
	
	$(function() {
		autocomplete_escolta("RecebsmEscolta");
		contadorChar("#RecebsmObservacao",".count-obs",500);
	})'
) ?>