<h4>Motorista</h4>
<?php if(!empty($this->data['RecebSm']['sem_motorista'])): ?>
	<p>Sem motorista</p>
<?php else: ?>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('motorista_nome', array('label' => 'Motorista', 'readonly' => true,'class' => 'input-xxlarge')) ?>
	</div>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('motorista_cpf', array('label' => 'CPF', 'readonly' => true)) ?>
		<?php echo $this->BForm->input('telefone', array('label' => 'Telefone', 'readonly' => true,'class' => 'input-medium')) ?>
		<?php echo $this->BForm->input('radio', array('label' => 'Radio', 'readonly' => true,'class' => 'input-medium')) ?>
	</div>
<?php endif; ?>