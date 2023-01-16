<div class = 'form-procurar'>
	<?= $this->element('/filtros/epc') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'epc', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Epc'));?>
</div>
<div class='lista'></div>
