<div class='row-fluid inline'>
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->BForm->input('nome', array('label' => false, 'placeHolder' => 'Nome', 'class' => 'input-xlarge')); ?>
    <?php echo $this->BForm->input('validade', array('label' => false, 'placeHolder' => 'Validade', 'class' => 'input-small data', 'type' => 'text')); ?>
    <?php echo $this->BForm->input('ativo', array('label' => false, 'placeHolder' => 'Status', 'class' => 'input-small', 'options' => array('Inativo', 'Ativo'))); ?>
</div>
<div class='row-fluid inline'>
	<?php echo $this->BForm->input('codigo_regra', array('label' => 'Regra', 'class' => 'input-large', 'options' => $regras, 'empty' => 'Selecione')); ?>
	<?php echo $this->BForm->input('quantidade', array('label' => 'Qtd', 'class' => 'input-mini numeric', 'title' => 'Quantidade Disponível')); ?>
	<?php echo $this->BForm->input('quantidade_por_beneficiado', array('label' => 'Qtd por Beneficiado', 'class' => 'input-medium numeric', 'title' => 'Quantidade concedida por beneficiado')); ?>
	<?php echo $this->BForm->input('valor', array('label' => 'Valor', 'class' => 'input-mini numeric moeda', 'title' => 'Valor Disponível')); ?>
	<?php echo $this->BForm->input('valor_por_beneficiado', array('label' => 'Valor por Beneficiado', 'class' => 'input-medium numeric moeda', 'title' => 'Valor concedido por beneficiado')); ?>
</div>
<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php $this->addScript($this->Javascript->codeBlock('setup_datepicker(); setup_mascaras()'));