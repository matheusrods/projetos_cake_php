<div class = 'form-procurar'>
	<?= $this->element('/filtros/clientes_responsaveis_registros_ambientais') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'clientes_responsaveis_registros_ambientais', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir responsável'));?>
</div>
<div class='lista'></div>
