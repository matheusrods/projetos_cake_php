<div class = 'form-procurar'>
	<?= $this->element('/filtros/sist_combate_incendio') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'sist_combate_incendio', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Sistemas de Combate de Incêndio'));?>
</div>
<div class='lista'></div>
