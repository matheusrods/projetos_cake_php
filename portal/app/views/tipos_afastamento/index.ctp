<div class = 'form-procurar'>
	<?= $this->element('/filtros/tipos_afastamento') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'tipos_afastamento', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Tipos de Afastamento'));?>
</div>
<div class='lista'></div>
