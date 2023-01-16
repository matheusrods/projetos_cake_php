<?php echo $this->Buonny->flash(); ?>
<?php echo $this->Bajax->form('Profissional', array('autocomplete' => 'off', 'url' => array('controller' => 'profissionais', 'action' => 'incluir'), 'callback'=>'pegar_dados_modal_para_tela')) ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('motorista_nome', array('class' => 'input-xxlarge', 'placeholder' => 'Nome', 'label' => false, 'type' => 'text')) ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input('motorista_cpf', array('label' => false, 'class' => 'input-medium formata-rne', 'placeholder' => 'CPF')) ?>
	<?php echo $this->BForm->input('telefone', array('label' => false, 'class' => 'input-medium telefone','placeholder' => 'Telefone')) ?>
	<?php echo $this->BForm->input('radio', array('label' => false, 'class' => 'input-medium','placeholder' => 'Radio')) ?>
	<?php echo $this->BForm->input('estrangeiro', array('label' => 'Estrangeiro','type' => 'checkbox')) ?>
	
</div>
<div class='form-actions'>
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Buonny->link_js('solicitacoes_monitoramento'); ?>
<?php echo $this->Javascript->codeBlock("setup_mascaras()", true); ?>