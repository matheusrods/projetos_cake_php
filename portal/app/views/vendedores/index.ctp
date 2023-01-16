<div class = 'form-procurar'>
	<?= $this->element('/filtros/vendedores') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'vendedores', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novo Vendedor'));?>
</div>
<div>&nbsp;</div>
<div class='lista'></div>
