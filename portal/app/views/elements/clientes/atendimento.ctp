<div class="row-fluid inline">
    <?php echo $this->BForm->input('codigo_gestor', array('label' => 'Gestor Comercial', 'class' => 'text-large', 'options' => $gestores, 'empty' => 'Selecione')); ?>
    <?php echo $this->BForm->input('codigo_gestor_contrato', array('label' => 'Gestor Contrato', 'class' => 'text-large', 'options' => $gestores, 'empty' => 'Selecione')); ?>
    <?php echo $this->BForm->input('codigo_gestor_operacao', array('label' => 'Gestor Operação', 'class' => 'text-large', 'options' => $gestores, 'empty' => 'Selecione')); ?>
    <?php echo $this->BForm->input('codigo_plano_saude', array('label' => 'Plano de Saúde', 'class' => 'input-medium', 'options' => $plano_saude, 'empty' => 'Selecione')); ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('codigo_corretora', array('label' => 'Corretora', 'options' => $corretoras, 'empty' => 'Selecione uma opção')); ?>
	
	<?php echo $this->Buonny->input_codigo_medico_readonly($this, 'codigo_medico_pcmso', 'Coord PCMSO', 'Coord PCMSO','Cliente', null, 'numero_conselho_pcmso', 'uf_conselho_pcmso', 'nome_medico_pcmso', 'cpf_medico_pcmso'); ?>
	
	<?php echo $this->BForm->input('codigo_medico', array('type' => 'hidden', 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['codigo'] : ''))?>

	<?php echo $this->BForm->input('codigo_empresa_session', array('type' => 'hidden', 'value' => $_SESSION['Auth']['Usuario']['codigo_empresa'])); ?> <!-- //hidden para auxiliar na validacao do javascript sobre o o cadastro do cpf que só poder obrigatorio, menos para a empresa 5  -->
	
	<?php echo $this->BForm->input('numero_conselho_pcmso', array('style' => 'width: 80px;', 'label' => 'CRM', 'title' => ('CRM'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['numero_conselho'] : '')); ?>
	<?php echo $this->BForm->input('uf_conselho_pcmso', array('style' => 'width: 50px;', 'label' => 'UF', 'title' => ('UF'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['conselho_uf']  : '')); ?>
	<?php echo $this->BForm->input('nome_medico_pcmso', array('style' => 'width: 260px;', 'label' => 'Nome do Médico', 'title' => ('NOME'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['nome']  : '')); ?>
	<?php echo $this->BForm->input('cpf_medico_pcmso', array( 'class' => 'input-medium cpf', 'label' => 'CPF do Médico', 'title' => ('CPF'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['cpf']  : '')); ?>
	
	<?php if($_SESSION['Auth']['Usuario']['codigo_empresa'] != 5): ?>
		<div id="CadastroCpfMedicoButton">
			<?php echo $this->Html->link('Cadastrar CPF do Médico', array('controller' => 'medicos', 'action' => 'editar', $this->data['Medico']['codigo']), array('target' => '_black', 'id' => 'botaoOk', 'style' => 'margin-top: 27px', 'class' => 'btn btn-danger btn-small')); ?>
		</div>
	<?php endif; ?>
</div>