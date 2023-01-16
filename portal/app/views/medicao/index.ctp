<div class = 'form-procurar'>
	<?= $this->element('/filtros/medicao') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'medicao', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novas Medições'));?>
</div>
<div class='lista'></div>
