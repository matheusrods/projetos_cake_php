<div class = 'form-procurar'>
	<?= $this->element('/filtros/riscos_atributos_detalhes') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'riscos_atributos_detalhes', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Novos Efeitos Críticos'));?>
</div>
<div class='lista'></div>
