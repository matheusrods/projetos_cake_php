<div class = 'form-procurar'>
	<?= $this->element('/filtros/clientes_responsaveis_monitoracao_biologicas') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'clientes_responsaveis_monitoracao_biologicas', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir responsável'));?>
</div>
<div class='lista'></div>
