<div class = 'form-procurar'>
	<?= $this->element('/filtros/servicos') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'servicos', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Serviços'));?>
</div>
<div class='lista'></div>
