<div class = 'form-procurar'>
	<?= $this->element('/filtros/aparelhos_audiometricos') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'aparelhos_audiometricos', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Aparelhos Audiometricos'));?>
</div>
<div class='lista'></div>
