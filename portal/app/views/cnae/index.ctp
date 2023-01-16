<div class = 'form-procurar'>
	<?= $this->element('/filtros/cnae') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'cnae', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Cnae'));?>
</div>
<div class='lista'></div>
