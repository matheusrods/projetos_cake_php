<div class = 'form-procurar'>
	<?= $this->element('/filtros/tecnicas_medicao') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'tecnicas_medicao', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Técnicas de Medição'));?>
</div>
<div class='lista'></div>
