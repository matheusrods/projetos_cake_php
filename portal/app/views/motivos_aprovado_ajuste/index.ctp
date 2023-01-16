<div class = 'form-procurar'>
	<?= $this->element('/filtros/motivos_aprovado_ajuste') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'motivos_aprovado_ajuste', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar Motivos de Aprovação com Ajuste'));?>
</div>
<div class='lista'></div>
