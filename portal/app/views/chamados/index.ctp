<div class='form-procurar'>
	<?php echo $this->element('/filtros/chamados')?>
</div>
<div class='actionbar-right' style="margin-bottom: 15px">
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'chamados', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar novo Chamado')); ?>
</div>

<div class='lista'></div>
