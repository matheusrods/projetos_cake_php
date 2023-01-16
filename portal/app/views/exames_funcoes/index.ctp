<div class = 'form-procurar'>
	<?= $this->element('/filtros/exames_funcoes') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'exames_funcoes', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar'));?>
</div>
<div class='lista'></div>
