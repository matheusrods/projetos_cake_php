<div class = 'form-procurar'>
	<?= $this->element('/filtros/tipo_glosas') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'tipo_glosas', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Tipo Glosas'));?>
</div>
<div class='lista'></div>
