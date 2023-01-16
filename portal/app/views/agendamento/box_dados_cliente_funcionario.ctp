<div class='inline well'>
	<?php echo $this->BForm->input('Empresa.razao_social', array('value' => $dados_cliente_funcionario['Empresa']['razao_social'], 'class' => 'input-large', 'label' => 'Empresa' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Empresa.codigo_documento', array('value' => $dados_cliente_funcionario['Empresa']['codigo_documento'], 'class' => 'input-large', 'label' => 'CNPJ' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Funcionario.nome', array('value' => $dados_cliente_funcionario['Funcionario']['nome'], 'class' => 'input-large', 'label' => 'Funcionario' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Funcionario.cpf', array('value' => $dados_cliente_funcionario['Funcionario']['cpf'], 'class' => 'input-large', 'label' => 'CPF' , 'readonly' => true, 'type' => 'text')); ?>
	<div class="clear"></div>
	<?php echo $this->BForm->input('Funcionario.data_nascimento', array('value' => $dados_cliente_funcionario['Funcionario']['data_nascimento'], 'class' => 'input-large', 'label' => 'Data nascimento' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Cliente.razao_social', array('value' => $dados_cliente_funcionario['Cliente']['razao_social'], 'class' => 'input-large', 'label' => 'Unidade' , 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Setor.descricao', array('value' => $dados_cliente_funcionario['Setor']['descricao'], 'class' => 'input-large', 'label' => 'Setor', 'readonly' => true, 'type' => 'text')); ?>
	<?php echo $this->BForm->input('Cargo.descricao', array('value' => $dados_cliente_funcionario['Cargo']['descricao'], 'class' => 'input-large', 'label' => 'Cargo' , 'readonly' => true, 'type' => 'text')); ?>
	<div class="clear"></div>
</div>