<?php echo $this->BForm->hidden("Profissional.codigo") ?>
<div class="row-fluid inline">
	<?php echo $this->BForm->input("Profissional.nome", array('label' => 'Nome do Profissional', 'autocomplete'=>'off','class' => 'input-large just-letters nome_sobrenome')) ?>
	<?php echo $this->BForm->input("Profissional.nome_pai", array('label' => 'Nome do Pai','autocomplete'=>'off', 'class' => 'input-large just-letters nome_sobrenome')) ?>
	<?php echo $this->BForm->input("Profissional.nome_mae", array('label' => 'Nome da Mãe', 'autocomplete'=>'off', 'class' => 'input-large just-letters nome_sobrenome')) ?>
</div>
<div class="row-fluid inline">
	<?php echo $this->BForm->input("Profissional.data_inclusao", array('label' => 'Data do cadastro', 'class' => 'input-small', 'type'=>'text', 'readonly' => true )) ?>
	<?php echo $this->BForm->input("FichaScorecard.data_inclusao", array('label' => 'Data da ultima ficha', 'class' => 'input-small', 'type'=>'text', 'readonly' => true )) ?>
</div>
<div class="row-fluid inline">
	<div class="span5">
		<h5>RG - Registro Geral</h5>
		<?php echo $this->BForm->input("Profissional.rg", array('label' => 'RG', 'class' => 'input-small')) ?>
		<?php echo $this->BForm->input("Profissional.codigo_estado_rg", array('label' => 'UF', 'class' => 'input-mini', 'options'=>$endereco_estado, 'empty'=>'UF')) ?>
		<?php echo $this->BForm->input("Profissional.rg_data_emissao", array('label' => 'Data Emissão', 'class' => 'input-small data', 'type'=>'text')) ?> 
	</div>
	<div class="span7">
		<h5>Naturalidade</h5>
		<?php echo $this->BForm->input("Profissional.data_nascimento", array('label' => 'Data Nascimento', 'class' => 'input-small data', 'type'=>'text')) ?>
		<?php echo $this->BForm->input('Profissional.cidade_naturalidade_profissional', array('class' => 'input-large ui-autocomplete-input', 'placeholder' => 'Informe uma Cidade', 'empty' => 'Cidade', 'label' => 'Cidade')) ?>
        <?php echo $this->BForm->input('Profissional.codigo_endereco_cidade_naturalidade', array('type' => 'hidden', 'id'=>'codigo_cidade')) ?>
        <?php echo $this->BForm->input('Profissional.codigo_estado_naturalidade', array('type' => 'hidden', 'id' => 'codigo_estado')) ?>
 	</div> 	
</div>
<?$tipo_prof = isset($this->data['FichaScorecard']['codigo_profissional_tipo']) ? $this->data['FichaScorecard']['codigo_profissional_tipo'] : NULL;?>
<div class="row-fluid inline dados-cnh">
	<h5>CNH - Carteira Nacional de Habilitação</h5>
	<?php echo $this->BForm->input("Profissional.cnh", array('label' => 'Número de registro', 'class' => 'input-medium just-number cnh', 'size' => 11, 'maxlength' => 11)) ?>
	<?php echo $this->BForm->input("Profissional.codigo_tipo_cnh", array('label' => 'Categoria', 'class' => 'input-mini', 'options'=>$tipo_cnh, 'empty'=>'Categoria')) ?>
	<?php echo $this->BForm->input("Profissional.cnh_vencimento", array('label' => 'Vencimento', 'class' => 'input-small data', 'type'=>'text')) ?>
	<?php echo $this->BForm->input("Profissional.codigo_endereco_estado_emissao_cnh", array('label' => 'UF de emissão', 'class' => 'input-mini', 'options'=>$endereco_estado, 'empty'=>'UF')) ?>
	<?php echo $this->BForm->input("Profissional.data_primeira_cnh", array('label' => 'Data da primeira CNH', 'class' => 'input-small data', 'type'=>'text')) ?>
	<?php echo $this->BForm->input("Profissional.codigo_seguranca_cnh", array('label' => 'Código de segurança <i class="icon-question-sign"></i>', 'class' => 'input-medium just-number codigo-seguranca', 'type'=>'text', 'size' => 11, 'maxlength' => 11)) ?>
</div>

<div class="row-fluid inline mop-data">
	<?php echo $this->BForm->input("Profissional.possui_mopp", array('label' => 'Possui MOPP', 'class' => 'input-mini', 'options'=>array('1'=>'Sim','0'=>'Não'), 'empty'=>'')) ?>
	<span id="divMopp" style="display:<?=(!empty($this->data['Profissional']['possui_mopp']) ? '' : 'none')?>;">
		<?php echo $this->BForm->input("Profissional.data_inicio_mopp", array('label' => 'Data Início MOPP', 'class' => 'input-small data', 'type'=>'text')) ?>
	<span>
</div>

<div class="row-fluid inline cep-data">
	<h5>Endereço</h5>
	<?php echo $this->Buonny->input_cep_endereco($this, array(), $profissional_enderecos, true, false, 'ProfissionalEndereco'); ?>
</div>
<div id="lista-contatos-profissional">
	<?php echo $this->element('fichas_scorecard/lista_contatos', array(
		'titulo'				=> 'Contatos do Profissional', 
		'listaContatos' 		=> isset($this->data['ProfissionalContato']) ? $this->data['ProfissionalContato'] : array(), 
		'tipo'					=> 'profissional',
		'model'					=> 'ProfissionalContato',
		'tipo_retorno'			=> $tipo_retorno_profissional,
		'tipos_retorno_fixo' 	=> array(TipoRetorno::TIPO_RETORNO_CELULAR_MOTORISTA),
		'disabled'				=> $disabled
	))?>
	<?php echo $this->BForm->input("FichaScorecard.observacao", array('label' => 'Observações', 'type'=>'textarea', 'class'=>'span9')) ?>
</div>
<?php echo $this->Javascript->codeBlock('
	$("#ProfissionalPossuiMopp").change(function(){
		if ($(this).val()==1) {
			$("#divMopp").show();
		} else {
			$("#divMopp").hide();
		}		
	});
', false);?>