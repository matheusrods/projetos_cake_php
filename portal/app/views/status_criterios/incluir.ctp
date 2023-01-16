<div class="criterios form">
	<?php echo $this->BForm->create('StatusCriterio',array('autocomplete' => 'off', 'url' => array('controller' => 
	'status_criterios', 'action' => 'incluir')));?>
	<?php  echo $this->element('status_criterios/fields') ?>
</div>