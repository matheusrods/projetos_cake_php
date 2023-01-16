<div class = 'form-procurar'>
	<?= $this->element('/filtros/medicamentos') ?>
</div>


<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'medicamentos', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Medicamentos'));?>
</div>

<div class='lista'></div>
