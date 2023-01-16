<div class = 'form-procurar'>
	<?= $this->element('/filtros/tipos_acidentes') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'tipos_acidentes', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Tipos de Acidentes'));?>
</div>
<div class='lista'></div>
