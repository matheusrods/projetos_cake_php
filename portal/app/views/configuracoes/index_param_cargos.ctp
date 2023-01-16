<div class = 'form-procurar'>
	<?= $this->element('/filtros/param_cargos') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'configuracoes', 'action' => 'incluir_param_cargos'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Parâmetro'));?>
</div>
<div class='lista'></div>
