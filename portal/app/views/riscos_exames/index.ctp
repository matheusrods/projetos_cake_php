<div class = 'form-procurar'>
	<?= $this->element('/filtros/riscos_exames') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'riscos_exames', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Riscos'));?>
</div>
<div class='lista'></div>
