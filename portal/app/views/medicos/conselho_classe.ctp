<div class = 'form-procurar'>
	<?= $this->element('/filtros/conselho_classe') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'medicos', 'action' => 'incluir_conselho_classe'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Incluir Conselho Classe'));?>
</div>
<div class='lista'></div>
