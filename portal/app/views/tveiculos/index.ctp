<div class = 'form-procurar'>
	<?= $this->element('/filtros/tveiculos_analitico') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'tveiculos', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Veículo'));?>
</div>
<div class='lista'></div>