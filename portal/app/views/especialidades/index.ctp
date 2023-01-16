<div class = 'form-procurar'>
	<?= $this->element('/filtros/especialidades') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'especialidades', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Especialidades'));?>
</div>
<div class='lista'></div>
