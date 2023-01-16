<div class = 'form-procurar'>
	<?= $this->element('/filtros/decretos_deficiencia') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'decretos_deficiencia', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Decretos para Deficiência'));?>
</div>
<div class='lista'></div>
