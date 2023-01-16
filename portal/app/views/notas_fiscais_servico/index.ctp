<div class = 'form-procurar'>
	<?= $this->element('/filtros/nota_fiscal_servico') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> incluir', array('controller' => 'notas_fiscais_servico', 'action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar nota fiscal'));?>
</div>
<div class='lista'></div>
