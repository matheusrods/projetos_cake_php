<div class = 'form-procurar'>
	<?= $this->element('/filtros/atribuicoes') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'atribuicoes', 'action' => 'incluir',$this->data['Atribuicao']['codigo_cliente']), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar'));?>
</div>
<div class='lista'></div>
