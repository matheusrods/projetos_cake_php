<div class='form-procurar'>
	<?php echo $this->element('/filtros/unidades_medicao')?>
</div>
<div class='actionbar-right' style="margin-bottom: 10px;">
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'unidades_medicao', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar nova Unidade de Medida')); ?>
</div>

<div class='lista'></div>
