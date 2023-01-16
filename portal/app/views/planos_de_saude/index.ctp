<div class = 'form-procurar'>
	<?= $this->element('/filtros/planos_de_saude') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'planos_de_saude', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Planos'));?>
</div>
<div class='lista'></div>
