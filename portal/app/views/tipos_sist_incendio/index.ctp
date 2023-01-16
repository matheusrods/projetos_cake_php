<div class = 'form-procurar'>
	<?= $this->element('/filtros/tipos_sist_incendio') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'tipos_sist_incendio', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Tipos de Sistemas de Incêncio'));?>
</div>
<div class='lista'></div>
