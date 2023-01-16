<div class = 'form-procurar'>
	<?= $this->element('/filtros/laboratorios') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'laboratorios', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Laboratórios'));?>
</div>
<div class='lista'></div>


