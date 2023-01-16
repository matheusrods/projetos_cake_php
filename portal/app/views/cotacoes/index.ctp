<div class='form-procurar'>
	<?= $this->element('/filtros/cotacoes') ?>
</div>
<div class='actionbar-right margin-bottom-10'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Nova cotação', 'data-toggle' => 'tooltip'));?>
</div>
<div class='lista'></div>