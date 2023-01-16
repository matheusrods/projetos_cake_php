<div class = 'form-procurar'>
	<?= $this->element('/filtros/digitalizacao_terceiros') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'tipo_digitalizacao', 'action' => 'incluir_digitalizacao'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar'));?>
</div>
<div class='lista'></div>