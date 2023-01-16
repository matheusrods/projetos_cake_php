<div class = 'form-procurar'>
	<?= $this->element('/filtros/cid') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'cid', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Cid'));?>
</div>
<div class='lista'></div>
