<div class = 'form-procurar'>
	<?= $this->element('/filtros/criterio_distribuicao') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Criterio de Distribuição'));?>
</div>
<div class='lista'></div>