<div class = 'form-procurar'>
	<?= $this->element('/filtros/ibutg') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'ibutg', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos IBUTG'));?>
</div>
<div class='lista'></div>
