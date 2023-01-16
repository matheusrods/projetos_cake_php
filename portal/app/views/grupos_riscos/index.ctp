<div class = 'form-procurar'>
	<?= $this->element('/filtros/grupos_riscos') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'grupos_riscos', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Grupos de Riscos'));?>
</div>
<div class='lista'></div>
