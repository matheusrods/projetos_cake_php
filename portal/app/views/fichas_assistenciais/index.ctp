<div class = 'form-procurar'>
	<?= $this->element('/filtros/fichas_assistenciais') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'fichas_assistenciais', 
			'action' => 'selecionarPedidoDeExameAssistencial'), 
		array('escape' => false, 
			'class' => 'btn btn-success', 
			'title' =>'Cadastrar Novas Fichas Clínicas')
	);
	?>
</div>
<div class='lista'></div>