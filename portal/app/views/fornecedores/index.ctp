<div class = 'form-procurar'>
	<?= $this->element('/filtros/fornecedores') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Prestadores'));?>
</div>
<div class='lista'></div>