<div class = 'form-procurar'>
	<?= $this->element('/filtros/tratativas_eventos_sistema') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'tratativas_eventos_sistema', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Tratativa de Evento do Sistema'));?>
</div>
<div class='lista'></div>