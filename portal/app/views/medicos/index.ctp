<div class = 'form-procurar'>
	<?= $this->element('/filtros/medicos') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'medicos', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Incluir Novo Profissional'));?>
</div>
<div class='lista'></div>
