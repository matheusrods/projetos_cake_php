<div class="criterios form">
	<?php echo $this->Form->create('StatusCriterio', array('url' => array('action' => 'editar', $this->data['StatusCriterio']['codigo'])))	;?>
	<?php echo $this->BForm->hidden('codigo', array('value' => $statuscriterios['StatusCriterio']['codigo']));?>

	<?php echo $this->element('status_criterios/fields') ?>
</div>