<div class = 'form-procurar'>
	<?= $this->element('/filtros/clientes') ?>
</div>

<div class='actionbar-right'>
	<?php if(isset($codigo_cliente) && $codigo_cliente) : ?>
		<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir', $codigo_cliente), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Cliente'));?>
	<?php else : ?>
		<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array( 'controller' => $this->name, 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Cliente'));?>
	<?php endif; ?>
</div>
<div class='lista'></div>