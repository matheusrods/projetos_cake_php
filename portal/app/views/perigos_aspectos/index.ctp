<div class='form-procurar'>
	<?php echo $this->element('/filtros/perigos_aspectos')?>
</div>
<div class='actionbar-right' style="margin-bottom: 10px;">
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'perigos_aspectos', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar novo perigos ou aspecto')); ?>
</div>

<div class='lista'></div>
