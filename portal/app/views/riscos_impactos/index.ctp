<div class='form-procurar'>
	<?php echo $this->element('/filtros/riscos_impactos')?>
</div>
<div class='actionbar-right' style="margin-bottom: 10px;">
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'riscos_impactos', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar novo risco ou impacto')); ?>
</div>

<div class='lista'></div>
