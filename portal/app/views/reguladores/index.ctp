<div class = 'form-procurar'>
	<?= $this->element('/filtros/reguladores') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'reguladores', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Reguladores'));?>
</div>
<div class='lista'></div>