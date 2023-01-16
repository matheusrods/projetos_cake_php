<div class = 'form-procurar'>
	<?= $this->element('/filtros/motivos_afastamento') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'motivos_afastamento', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Motivos de Afastamento'));?>
</div>
<div class='lista'></div>
