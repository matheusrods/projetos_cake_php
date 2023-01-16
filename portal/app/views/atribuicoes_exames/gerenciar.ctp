<div class = 'form-procurar'>
	<?= $this->element('/filtros/atribuicoes_exames') ?>
</div>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', array('controller' => 'atribuicoes_exames', 'action' => 'incluir',$this->data['AtribuicaoExame']['codigo_cliente']), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Cadastrar'));?>
</div>
<div class='lista'></div>
