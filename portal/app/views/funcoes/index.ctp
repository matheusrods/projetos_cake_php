<div class = 'form-procurar'>
	<?= $this->element('/filtros/funcoes') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'funcoes', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Função'));?>
</div>
<div class='lista'></div>
