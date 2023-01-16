<div class = 'form-procurar'>
	<?= $this->element('/filtros/tipo_digitalizacao') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'tipo_digitalizacao', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar'));?>
</div>
<div class='lista'></div>