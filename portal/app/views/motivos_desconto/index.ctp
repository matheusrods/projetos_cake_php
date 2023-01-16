<div class = 'form-procurar'>
	<?= $this->element('/filtros/motivos_desconto') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'motivos_desconto', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Motivos Desconto'));?>
</div>
<div class='lista'></div>
