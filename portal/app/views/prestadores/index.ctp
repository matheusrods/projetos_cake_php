<div class = 'form-procurar'>
	<?= $this->element('/filtros/prestadores') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => 'prestadores', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Prestador'));?>
</div>
<div class='lista'></div>