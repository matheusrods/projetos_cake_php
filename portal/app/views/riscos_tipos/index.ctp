<div class='form-procurar'>
	<?php echo $this->element('/filtros/riscos_tipo')?>
</div>
<div class='actionbar-right' style="margin-bottom: 10px;">
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'riscos_tipos', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar novo tipo de risco')); ?>
</div>

<div class='lista'></div>
