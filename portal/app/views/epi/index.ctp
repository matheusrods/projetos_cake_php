<div class = 'form-procurar'>
	<?= $this->element('/filtros/epi') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'epi', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos EPIs'));?>
</div>
<div class='lista'></div>
