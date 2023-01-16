<div class='row-fluid inline'>
    <?php $descricao = (!empty($mrexame['MotivoRecusaExame']['descricao']) ? $mrexame['MotivoRecusaExame']['descricao'] : ''); ?>
	<?php echo $this->BForm->input('MotivoRecusaExame.descricao', array('type' => 'text', 'label' => 'Descrição (*)', 'class' => 'input-xxlarge', 'value' => $descricao)); ?>
    <?php $ativo = (!empty($mrexame['MotivoRecusaExame']['ativo']) ? $mrexame['MotivoRecusaExame']['ativo'] : ''); ?>
    <?php echo $this->BForm->input('MotivoRecusaExame.ativo', array('type' => 'select', 'label' => 'Status (*)', 'class' => 'input', 'default' => $ativo, 'empty' => 'Status', 'options' => array(1 => 'Ativado', 0 => 'Desativado'))); ?>
</div>

<div class='form-actions'>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
	<?= $html->link('Voltar', array('controller' => 'motivos_recusa', 'action' => 'exames_index'), array('class' => 'btn')); ?>
</div>
