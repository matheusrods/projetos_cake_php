<div class = 'form-procurar'>
	<?= $this->element('/filtros/tpecas_analitico') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'tpecas', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Peça'));?>
</div>
<div class='lista'></div>