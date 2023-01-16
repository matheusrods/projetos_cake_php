<div class = 'form-procurar'>
	<?= $this->element('/filtros/tipos_retornos') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Tipos de Retorno'));?>
</div>
<div class='lista'></div>