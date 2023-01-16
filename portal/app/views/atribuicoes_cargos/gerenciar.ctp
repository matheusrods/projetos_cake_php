<div class = 'form-procurar'>
	<?= $this->element('/filtros/atribuicoes_cargos') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'atribuicoes_cargos', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar'));?>
</div>
<div class='lista'></div>
