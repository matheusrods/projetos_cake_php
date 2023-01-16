<style type="text/css">
	label {
		font-weight: bold;
	}
	.input.control-group.radio{
		padding-left: 0;
	}
	.input.control-group.radio input{
		float: left;
		width: 15px;
		position: relative;
		display: inline-block;
		margin-left: 0;
	}
	.input.control-group.radio label{
		float: left;
		/*width: 15px;*/
		position: relative;
		display: inline-block;
		margin-right: 20px;
	}
	.form-group{
		float: left;
		width: 100%;
		margin-bottom: 15px;
	}
	.control-group{
		width: 100%;
	}
	.control-group.input.time select{
		max-width: 50px;
	}
	.help-block.error-message{
		display: block;
		clear: both;
	}
</style>
<?php echo $this->BForm->hidden('codigo_funcionario_setor_cargo', array('value' => $codigo_funcionario_setor_cargo)) ?>
<div class='well'>
	<div class='row-fluid inline'>

		<div class="span5">
			<strong>Unidade: </strong><?php echo $this->Html->tag('span', $dados['Cliente']['razao_social']); ?>
		</div>
		<div class="span4">
			<strong>Funcionário: </strong><?php echo $this->Html->tag('span', $dados['Funcionario']['nome']); ?>
		</div>
		<div class="span3">
			<strong>CPF: </strong><?php echo $this->Html->tag('span', $buonny->documento($dados['Funcionario']['cpf'])); ?>
		</div>
	</div>
</div>
<hr>
<div class="well">
	<h4 class="text-center">Comunicação de acidente de trabalho - CAT</h4>
	<hr>
	<div class="row-fluid inline">
		<div class="form-group">
			<label>1) Emitente:</label>
			<?php echo $this->BForm->input('codigo_emitente', array('type' => 'radio', 'options' => $emitentes, 'legend' => false, 'hiddenField' => false, 'label' => array('style' => 'line-height: 20px'))) ?>
		</div>

			
		<div class="form-group">
			<div class="span4">
				<label>2) Tipo de CAT:</label>
				<?php echo $this->BForm->input('tipo_cat_codigo', array('type' => 'radio', 'options' => $cats, 'legend' => false, 'hiddenField' => false)) ?>			
			</div>
		
			<div class="span3">
				<?php echo $this->BForm->input('motivo_emissao', array('label' => 'Motivo Emissão CAT:', 'empty' => 'Selecione um Tipo', 'options' => array('1' => '1 - Empregador','2' => '2 - Ordem Judicial', '3' => '3 - Determinação de órgão fiscalizador'), 'legend' => false, 'class' => 'js-example-basic-single', 'hiddenField' => false)); ?>
			</div>
			<div class="span2" id="emissao_cpf" >
				<?php echo $this->BForm->input('cpf_motivo_emissao_cat', array('label' => 'CPF:', 'class' => 'input-small cpf','style' => 'width: 95%')) ?>
			</div>
		</div>
	</div>
</div>
<div class="well">
	<h4 class="text-center">Emitente</h4>
	<hr>
	<div class="row-fluid inline">
		<div class="form-group">
			<h5>Empregador:</h5>
			<?php echo $this->BForm->hidden('cliente_codigo', array('value' => $dados['Cliente']['codigo'])) ?>
			<?php echo $this->BForm->input('razao_social', array('label' => '3) Razão Social / Nome:', 'disabled' => true, 'class' => 'input-xxlarge', 'value' => $dados['Cliente']['razao_social'])) ?>
		</div>
		<div class="span5 no-margin-left">
			<div class="row-fluid">
				<div class="form-group">
					<label>4) Tipo:</label>
					<?php echo $this->BForm->input('tipo_cnpj', array('disabled' => true, 'legend' => false, 'type' => 'radio', 'options' => array(1 => 'CNPJ', 2 => 'CPF'), 'default' => 1)) ?>
					<div class="clear"></div>
					<?php
					if(trim($dados['Cliente']['codigo_documento_real']) != '') {
						$cnpj = $dados['Cliente']['codigo_documento_real'];
					} 
					else {
						print "ali";
						$cnpj = $dados['Cliente']['codigo_documento'];
					}
					
					echo $this->BForm->input('razao_social', array('label' => false, 'disabled' => true,  'value' => $buonny->documento($cnpj))) ?>
				</div>
			</div>
		</div>
		<div class="span2">
			<?php echo $this->BForm->input('cnae', array('label' => '5) Cnae:', 'disabled' => true, 'class' => 'input-small', 'value' => $dados['Cliente']['cnae'])) ?>
		</div>
		<div class="span5">
			<?php echo $this->BForm->input('endereco', array('label' => '6) Endereço:', 'disabled' => true, 'style' => 'width: 95%', 'value' => $dados['ClienteEndereco']['logradouro'].', '.$dados['ClienteEndereco']['numero'] )) ?>
		</div>
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span2">
				<?php echo $this->BForm->input('complemento', array('label' => 'Complemento:', 'disabled' => true, 'style' => 'width: 95%', 'value' => $dados['ClienteEndereco']['complemento'])) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('bairro', array('label' => 'Bairro:', 'disabled' => true, 'style' => 'width: 95%', 'value' => $dados['ClienteEndereco']['bairro'])) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('cep', array('label' => 'CEP:', 'disabled' => true, 'style' => 'width: 95%', 'value' => $buonny->cep($dados['ClienteEndereco']['cep']))) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('cidade', array('label' => '7) Cidade:', 'disabled' => true, 'style' => 'width: 95%', 'value' => $dados['ClienteEndereco']['cidade'])) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('estado', array('label' => '8) Estado:', 'disabled' => true, 'style' => 'width: 95%', 'default' => $dados['ClienteEndereco']['estado_descricao'])) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('telefone', array('label' => '9) Telefone:', 'disabled' => true, 'style' => 'width: 95%', 'value' => $dados['Funcionario']['telefone'])) ?>
			</div>
		</div>
	</div>
	<hr>
	<div class="row-fluid inline">
		<h5>Acidentado:</h5>
		<div class="form-group">
			<?php echo $this->Form->hidden('codigo_funcionario'); ?>
			<?php echo $this->BForm->input('nome', array('label' => '10) Nome:', 'disabled' => true, 'class' => 'input-xxlarge', 'value' => $dados['Funcionario']['nome'])) ?>
		</div>
		<div class="form-group">
			<?php echo $this->BForm->input('Funcionario.nome_mae', array('label' => '11) Nome da mãe:',  'class' => 'input-xxlarge')) ?>
		</div>
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span3 no-margin-left">
				<?php echo $this->BForm->input('Funcionario.data_nascimento', array('type' => 'text', 'label' => '12) Data de nascimento:', 'class' => 'input-small datepickerjs date', 'multiple', 'disabled' => (empty($this->data['Funcionario']['data_nascimento']))? false : true)) ?>
			</div>
			<div class="span3">
				<label>13) Sexo:</label>
				<?php echo $this->BForm->input('Funcionario.sexo', array('type' => 'radio', 'options' =>  array('M' => 'Masculino', 'F' => 'Feminino'), 'legend' => false, 'hiddenField' => false, 'disabled' => (empty($this->data['Funcionario']['sexo']))? false : true)) ?>
			</div>
			<div class="span6">
				<label>14) Estado civil:</label>
				<?php echo $this->BForm->input('Funcionario.estado_civil', array('type' => 'radio', 'options' => $estados_civis, 'legend' => false, 'hiddenField' => false, 'disabled' => (empty($this->data['Funcionario']['estado_civil']))? false : true)) ?>
			</div>
		</div>
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span3 no-margin-left">
				<?php echo $this->BForm->input('Funcionario.ctps', array('label' => '15) CPTS Nº:', 'class' => 'input-medium', 'disabled' => (empty($this->data['Funcionario']['ctps']))? false : true)) ?>
			</div>
			<div class="span3">
				<?php echo $this->BForm->input('Funcionario.ctps_uf', array('label' => '16) CPTS - UF:', 'options' => $estados, 'empty' => '', 'class' => 'input-small', 'disabled' => (empty($this->data['Funcionario']['ctps_uf']))? false : true)) ?>
			</div>
			<div class="span6">
				<label for="CatRemuneracaoMensal">17) Remuneração mensal:</label>
				<?php echo $this->BForm->input('remuneracao_mensal', array('div' => 'input-prepend input-append', 'between' => '<span class="add-on">R$</span>', 'after' => '<span class="add-on">.00</span>', 'label' => false)) ?>
			</div>
		</div>
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span3">
				<?php echo $this->BForm->input('Funcionario.rg', array('label' => '18) RG:', 'class' => 'input-medium', 'disabled' => (empty($this->data['Funcionario']['rg']))? false : true)) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('Funcionario.rg_data_emissao', array('type' => 'text', 'label' => 'Data emissão:', 'class' => 'input-small datepickerjs date', 'multiple', 'disabled' => (empty($this->data['Funcionario']['rg_data_emissao']))? false : true)) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('Funcionario.rg_orgao', array('label' => 'Órgão expedidor:', 'class' => 'input-small', 'disabled' => (empty($this->data['Funcionario']['rg_orgao']))? false : true)) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('Funcionario.rg_uf', array('options' => $estados, 'label' => '19) RG - UF:', 'empty' => '', 'class' => 'input-small', 'disabled' => (empty($this->data['Funcionario']['rg_uf']))? false : true)) ?>
			</div>	
			<div class="span2">
				<?php echo $this->BForm->input('Funcionario.cpf', array('label' => '20) CPF:', 'class' => 'input-small cpf','style' => 'width: 95%','disabled' => (empty($this->data['Funcionario']['cpf']))? false : true)) ?>
			</div>
			<div class="row-fluid inline">
				<div class="form-group">
					<div class="span2">
						<?php echo $this->BForm->input('Funcionario.nit', array('label' => '21) PIS/PASEP/NIT:', 'disabled' => (empty($this->data['Funcionario']['nit']))? false : true)) ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<?php echo $this->BForm->input('endereco', array('label' => '22) Endereço:', 'class' => 'input-xxlarge', 'disabled' => true, 'value' => $dados['FuncionarioEndereco']['logradouro'])) ?>
		</div>	
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span2">
				<?php echo $this->BForm->input('complemento', array('label' => 'Complemento:', 'disabled' => true, 'style' => 'width: 95%', 'value' => $dados['FuncionarioEndereco']['complemento'])) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('bairro', array('label' => 'Bairro:', 'disabled' => true, 'style' => 'width: 95%', 'value' => $dados['FuncionarioEndereco']['bairro'])) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('cep', array('label' => 'CEP:', 'disabled' => true, 'style' => 'width: 95%', 'value' => $buonny->cep($dados['FuncionarioEndereco']['cep']))) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('cidade', array('label' => '23) Cidade:', 'disabled' => true, 'style' => 'width: 95%', 'value' => $dados['FuncionarioEndereco']['cidade'])) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('estado_funcionario', array('label' => '24) Estado:', 'disabled' => true, 'style' => 'width: 95%', 'value' => $dados['FuncionarioEndereco']['estado_abreviacao'])) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('telefone_funcionario', array('label' => '25) Telefone:', 'disabled' => true, 'style' => 'width: 95%', 'default' => $dados['Funcionario']['tel_fun'])) ?>
			</div>
		</div>
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span6">
				<?php echo $this->BForm->input('ocupacao', array('label' => '26) Ocupação:', 'disabled' => true, 'style' => 'width: 95%', 'default' => $dados['Funcionario']['cargo'])) ?>
			</div>
			<div class="span6">
				<?php echo $this->BForm->input('cbo', array('label' => '27) CBO:', 'disabled' => true, 'style' => 'width: 95%', 'default' => $dados['Funcionario']['cargo_cbo'])) ?>
			</div>
		</div>
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span6">
				<label>28) *Filiação à Previdência Social:</label>
				<?php echo $this->BForm->input('fil_prev_social_codigo', array('type' => 'radio', 'options' => $filiacoes, 'legend' => false, 'hiddenField' => false)) ?>
			</div>
			<div class="span3">
				<label>29) *Aposentado:</label>
				<?php echo $this->BForm->input('aposentado', array('type' => 'radio', 'options' => array(1 => 'Sim', 0 => 'Não'), 'legend' => false, 'hiddenField' => false)) ?>
			</div>
			<div class="span3">
				<label>30) *Áreas:</label>
				<?php echo $this->BForm->input('area_codigo', array('type' => 'radio', 'options' => $areas, 'legend' => false, 'hiddenField' => false)) ?>
			</div>
		</div>
	</div>
	<hr>
	<div class="row-fluid inline">
		<h5>Acidente ou doença:</h5>
		<div class="form-group">
			<div class="span2 no-margin-left">
				<?php echo $this->BForm->input('data_acidente', array('type' => 'text', 'label' => '31) Data do acidente:', 'class' => 'input-small data')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('hora_acidente', array('timeFormat' => 24, 'label' => '32) Hora do acidente:', 'class' => 'input-small', 'value' => empty($this->data['Cat']['hora_acidente']) ? '00:00' : $this->data['Cat']['hora_acidente'])) ?>
			</div>
			<div class="span3">
				<?php echo $this->BForm->input('apos_qts_hs_trabalho', array('timeFormat' => 24, 'label' => '33) Após quantas horas de trabalho?', 'class' => 'input-small', 'value' => empty($this->data['Cat']['apos_qts_hs_trabalho']) ? '00:00' : $this->data['Cat']['apos_qts_hs_trabalho'])) ?>
			</div>
			<div class="span3">
				<label>34) Tipo:</label>
				<?php echo $this->BForm->input('codigo_esocial_24', array('label' => false, 	
																		  'empty' => 'Selecione um Tipo', 
																		  'options' => $tipos, 
																		  'legend' => false, 
																		  'class' => 'js-example-basic-single', 
																		  'hiddenField' => false)
						)?>
			</div>
			<div class="span2">
				<label>35) Houve afastamento?</label>
				<?php echo $this->BForm->input('houve_afastamento', array('type' => 'radio', 'options' => array(1 => 'Sim', 0 => 'Não'), 'legend' => false, 'hiddenField' => false)) ?>
			</div>
		</div>
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span2 no-margin-left">
				<?php echo $this->BForm->input('ultimo_dia_trabalhado', array('type' => 'text', 'label' => '36) Último dia trabalhado:', 'class' => 'input-small data')) ?>			</div>
			<div class="span2">
				<?php echo $this->BForm->input('local_acidente', array('label' => '37) Local do acidente:', 'options' => $local_acidente,'empty' => 'Selecione', 'class' => 'input-medium')) ?>
			</div>
			<div class="span3">
				<?php echo $this->BForm->input('especificacao_local_acidente', array('label' => '38) Especificação do local do acidente:', 'class' => 'input-medium')) ?>
			</div>
			<div class="span3">
				<?php echo $this->BForm->input('codigo_documento', array('label' => '39) CNPJ:', 'class' => 'input-medium')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('uf_documento', array('label' => '40) UF:', 'options' => $estados, 'empty' => 'selecione', 'class' => 'input-small')) ?>
			</div>
		</div>
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span5" id='showw' >
				<?php echo $this->BForm->input('codigo_esocial_13', array('label' => '41) Parte do corpo:', 'options' => $partecorpo,'empty' => 'Selecione..', 'class' => 'js-example-basic-single','style'=>'width : 430px')) ?>
			</div>
			<div class="span3" id="hidee" style='margin-left:20px;<?php 
			if(!empty($this->data['Cat']['codigo_esocial_13'])){?> display: block;<?}else{?>display: none; <?php }?>'>
				<?php echo $this->BForm->input('lateralidade_corpo', array('label' => '42) Parte do corpo atingida:','options' => $part_corpo, 'class' => 'input-medium','empty' => 'Selecione..')) ?>

			</div>
			<div class="span2" style='margin-left:-10px'>
				<?php echo $this->BForm->input('codigo_esocial_14_15', array('label' => '43) Agente causador:', 'options' => $agente_causador,'empty' => 'Selecione..', 'class' => 'js-example-basic-single','style'=>'width : 380px')) ?>
			</div>
		</div>
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span6 no-margin-left">
				<?php echo $this->BForm->input('codigo_esocial_16', array('label' => '44) Descrição da situação geradora do acidente ou doença:', 'options' => $acidentes,'empty' => 'Selecione..', 'class' => 'js-example-basic-single','style'=>'width : 430px')) ?>
			</div>
			<div class="span6">
				<div class="row-fluid">
					<div class="span6">
						<div class="form-group">
							<label>45) Houve registro policial?</label>
							<?php echo $this->BForm->input('resistro_policial', array('type' => 'radio', 'options' => array(1 => 'Sim', 0 => 'Não'), 'legend' => false, 'hiddenField' => false)) ?>
							<label>46) Houve morte?</label>
							<?php echo $this->BForm->input('morte', array('type' => 'radio', 'options' => array(1 => 'Sim', 0 => 'Não'), 'legend' => false, 'hiddenField' => false)) ?>
						</br>
						</div>
					</div>
					</br>
					</br>
					<div class="span5">
					</br>
					<?php echo $this->BForm->input('data_obito', array('type' => 'text', 'label' => '47) Data Óbito:', 'class' => 'input-small data'))?>
					</div>
				</div>
				
			</div>
			<div class="span6 no-margin-left">
				<?php echo $this->BForm->input('observacao_cat', array('type' => 'textarea', 'label' => 'Observação CAT:', 'class' => 'input-medium')); ?>
			</div>
			<hr>
		</div>

		<h5>48)Endereço Acidente:</h5>
		<div class="row-fluid inline">
			<div class="form-group">
				<div class="span1 no-margin-left">
					<?php echo $this->BForm->input('cep_acidentado', array('class' => 'input-small formata-cep', 'label' => 'CEP', 'maxlength' => '8', 'onchange' => '$("#pesquisa_cep").show();')); ?>
				</div>
				<div class="span2">
					<label style="float: left; padding: 30px 0px 0px 10px; font-size: 10px;" id="pesquisa_cep"><a href="javascript:void(0);" onclick="cat.buscaCEP();">COMPLETAR ENDEREÇO</a></label>		
				</div>
				<div class="span1">
					<img src="/portal/img/default.gif" id="carregando" style="padding: 30px 0 0 1px; display: none;">
				</div>
				<div class="span2">
					<?php echo $this->BForm->input('codigo_pais', array('label' => 'Código Pais', 'options' => $codigos_paises,'empty' => 'Selecione..', 'class' => 'input-medium js-example-basic-single')) ?>
				</div>
				<div class="span2">
					<?php echo $this->BForm->input('tipo_inscricao', array('label' => 'Tipo Inscrição', 'options' => $tipo_inscricao,'empty' => 'Selecione..', 'class' => 'input-medium')) ?>
				</div>
				<div class="span1 no-margin-left">
					<?php echo $this->BForm->input('cod_postal', array('class' => 'input-small ', 'label' => 'Cod Postal', 'maxlength' => '8')); ?>
				</div>
				<div class="span2" id="caepf_title">
					<?php echo $this->BForm->input('codigo_caepf', array('label' => 'CAEPF', 'class' => 'input-medium')) ?>
				</div>
				<div class="span2" id="cno_title">
					<?php echo $this->BForm->input('codigo_cno', array('label' => 'CNO', 'class' => 'input-medium')) ?>
				</div>
				<div class="row-fluid inline">
					<div class="form-group">
						<div class="span6 no-margin-left">
							<?php echo $this->BForm->input('acidentado_endereco' , array('label' => 'Endereço:', 'class' => 'input-xxlarge')) ?>
						</div>
						<div class="span2">
							<?php echo $this->BForm->input('acidentado_numero', array('label' => 'Número:', 'class' => 'input-small', 'style' => 'width: 70%')) ?>
						</div>
						<div class="span3">
							<?php echo $this->BForm->input('acidentado_complemento', array('label' => 'Complemento:', 'style' => 'width: 95%')) ?>
						</div>
					</div>
				</div>
				<div class="row-fluid inline">
					<div class="form-group">
						<div class="span3">
							<?php echo $this->BForm->input('acidentado_bairro', array('label' => 'Bairro:', 'style' => 'width: 95%', 'class' => 'input-medium')) ?>
						</div>
						<div class="span2">
							<?php echo $this->BForm->input('acidentado_cidade', array('label' => 'Cidade:', 'style' => 'width: 95%', 'class' => 'input-medium', 'readonly' => 'readonly')) ?>
						</div>
						<div class="span2">
							<?php echo $this->BForm->input('acidente_estado', array('label' => 'Estado', 'style' => 'width: 40%;','maxlength' =>'2', 'readonly' => 'readonly')) ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	<hr>
	<div class="row-fluid inline">
		<h5>Testemunhas:</h5>
		<div class="form-group">
			<div class="span12 no-margin-left">
				<?php echo $this->BForm->input('t1_nome', array('label' => '49) Nome:', 'class' => 'input-xxlarge')) ?>
			</div>
		</div>
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span6 no-margin-left">
				<?php echo $this->Form->hidden('t1_codigo_endereco'); ?>
				<?php echo $this->BForm->input('t1_endereco', array('label' => '50) Endereço:', 'class' => 'input-xxlarge')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('t1_numero', array('label' => 'Número:', 'class' => 'input-small')) ?>
			</div>
			<div class="span3">
				<?php echo $this->BForm->input('t1_complemento', array('label' => 'Complemento:', 'style' => 'width: 95%')) ?>
			</div>
		</div>
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span2">
				<?php echo $this->BForm->input('t1_bairro', array('label' => 'Bairro:', 'style' => 'width: 95%')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('t1_cep', array('label' => 'CEP:', 'style' => 'width: 95%')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('t1_cidade', array('label' => 'Cidade:', 'style' => 'width: 95%')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('t1_estado', array('label' => '51) Estado:', 'style' => 'width: 95%', 'empty' => 'Selecione')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('t1_telefone', array('label' => 'Telefone:', 'style' => 'width: 95%')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('t1_celular', array('label' => 'Celular:', 'style' => 'width: 95%')) ?>
			</div>
		</div>
	</div>
	<div>&nbsp;</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span12 no-margin-left">
				<?php echo $this->BForm->input('t2_nome', array('label' => '52) Nome:', 'class' => 'input-xxlarge')) ?>
			</div>
		</div>
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span6 no-margin-left">
				<?php echo $this->Form->hidden('t2_codigo_endereco'); ?>
				<?php echo $this->BForm->input('t2_endereco', array('label' => '53) Endereço:', 'class' => 'input-xxlarge')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('t2_numero', array('label' => 'Número:', 'class' => 'input-small')) ?>
			</div>
			<div class="span3">
				<?php echo $this->BForm->input('t2_complemento', array('label' => 'Complemento:', 'style' => 'width: 95%')) ?>
			</div>
		</div>
	</div>
	<div class="row-fluid inline">
		<div class="form-group">
			<div class="span2">
				<?php echo $this->BForm->input('t2_bairro', array('label' => 'Bairro:', 'style' => 'width: 95%')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('t2_cep', array('label' => 'CEP:', 'style' => 'width: 95%')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('t2_cidade', array('label' => 'Cidade:', 'style' => 'width: 95%')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('t2_estado', array('label' => '54) Estado:', 'style' => 'width: 95%', 'empty' => 'Selecione')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('t2_telefone', array('label' => 'Telefone:', 'style' => 'width: 95%')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('t2_celular', array('label' => 'Celular:', 'style' => 'width: 95%')) ?>
			</div>
		</div>
	</div>

	<div class='row-fluid inline'>
		<div class="form-group">
			<div class="span4">
				<?php echo $this->BForm->input('local', array('label' => 'Local:', 'class' => 'input-xlarge')) ?>
			</div>
			<div class="span3">
				<?php echo $this->BForm->input('data', array('type' => 'text', 'label' => 'Data:', 'class' => 'input-small data')) ?>
			</div>
		</div>
	</div>
  </div>


	<hr>
	<div class="row-fluid inline">
		<h5>Atestado Médico:</h5>
		<div class="form-group">
			<div class="span8">
				<?php echo $this->BForm->input('cnes', array('label' => 'CNES (Cadastro Nacional de Estabelecimento de Saúde):', 'class' => 'input-xxlarge')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('data_atendimento', array('type' => 'text', 'label' => 'Data Atendimento:', 'class' => 'input-small data')) ?>
			</div>
			<div class="span2">
				<?php echo $this->BForm->input('hora_atendimento', array('timeFormat' => 24, 'label' => 'Hora Atendimento:', 'class' => 'input-small', 'value' => empty($this->data['Cat']['hora_atendimento']) ? '00:00' : $this->data['Cat']['hora_atendimento'])) ?>
			</div>
		</div>

		<div class="form-group">
			<div class="span3">
				<?php echo $this->BForm->input('indicativo_internacao', array('label' => 'Indicação de Internação:', 'empty' => 'Selecione um Tipo', 'options' => array('S' => 'Sim','N' => 'Não'), 'legend' => false)); ?>
			</div>
			<div class="span4">
				<?php echo $this->BForm->input('duracao_estimada_tratamento', array('type' => 'text', 'label' => 'Duração estimada do Tratamento (em dias):', 'class' => 'input-large')); ?>
			</div>
		</div>
		<div class="form-group">
			<div class="span3">
				<?php echo $this->BForm->input('natureza_lesao', array('label' => 'Natureza da Lesão:', 'empty' => 'Selecione uma Natureza',  'options' => $natureza_lesao, 'class' => 'js-example-basic-single',  'legend' => false)); ?>
			</div>
			<div class="span8">
				<?php echo $this->BForm->input('descricao_complementar_lesao', array('type' => 'text', 'label' => 'Descrição Complementar Lesão:', 'class' => 'input-xxlarge')); ?>
			</div>
		</div>
		<div class="form-group">			
			<div class="span4">
				<?php echo $this->BForm->input('diagnostico_provavel', array('type' => 'text', 'label' => 'Diagnóstico Provável:', 'class' => 'input-xlarge')); ?>
			</div>

			<?php $codigo_inicial_cid  = 0; ?>

			<!-- CID10 -->
			<?php if(isset($codigo_atestado) && $codigo_atestado) : ?>

				<?php
				//verifica se existe dados de cids
				if(!empty($dados_cids)) {
					//conta quantos cids tem para este atestado
					$i = count($dados_cids)-1;
					//varre os dados do cid
					foreach ($dados_cids as $key4 => $value) {
				?>
						<div class="inputs-config span8 hide" style="margin-left: 0; margin-right: 1%; display: block;">
							<div class="checkbox-canvas" style="background-color: #f5f5f5;">
								<div class="row-fluid">
									<div class="span8">
										<?php echo $this->BForm->input('cid10.'.$key4.'.doenca', array('value' => $value['Cid']['descricao'], 
																'class' => 'js-cid-10', 
																'label' => 'CID10', 
																'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
																'div' => 'control-group input text width-full padding-left-10', 
																'after' => '<span style="margin-top: -7px" class="btn btn-default js-remove-cid pointer pull-right" data-toggle="tooltip" title="Remover doença"><i class="icon-minus" ></i></span>')); ?>
									</div>
								</div>
							</div>
						</div>
				<?php 
					} //FINAL FOREACH $dadosCid

					$codigo_inicial_cid  = $key4 + 1;
				}//fim if cid
				?>
			<?php endif; ?>
					
			<div class="js-encapsulado">
				<div class="inputs-config span8" style="margin-left: 0; margin-right: 1%">
					<div class="checkbox-canvas" style="background-color: #f5f5f5;">
						<div class="row-fluid">
							<div class="span8">
								<?php echo $this->BForm->input('cid10.'.$codigo_inicial_cid.'.doenca', 
									array('label' => 'CID10', 
										'class' => 'js-cid-10', 
										'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
										'div' => 'control-group input text width-full padding-left-10', 
										'required' => false, 
										// 'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-cid pointer pull-right" data-toggle="tooltip" title="Adicionar nova doença"><i class="icon-plus" ></i></span style="margin-top: -7px">'
									)
								); 
								?>
							</div>	
						</div>
					</div>
				</div>
				<div class="js-memory hide">
					<div class="inputs-config hide span8" style="margin-left: 0; margin-right: 1%">
						<div class="checkbox-canvas" style="background-color: #f5f5f5;">
							<div class="row-fluid">
								<div class="span8">
									<?php echo $this->BForm->input('cid10.xx.doenca', 
										array('label' => 'CID10', 
											'class' => 'js-cid-10', 
											'style' => 'width: 90%; margin-bottom: 0; margin-top: -6px', 
											'div' => 'control-group input text width-full padding-left-10', 
											'required' => false, 
											//'after' => '<span style="margin-top: -7px" class="btn btn-default js-add-cid pointer pull-right" data-toggle="tooltip" title="Adicionar nova doença"><i class="icon-plus" ></i></span style="margin-top: -7px">'
											)
									); 
									?>
								</div>	
							</div>
						</div>
					</div>
				</div>
			</div>



		</div>
		<div class="form-group">
			<div class="span6">
				<?php echo $this->BForm->input('observacao', array('type' => 'textarea', 'label' => 'Observação:', 'class' => 'input-xxlarge')); ?>
			</div>
			<div class="span6">
				<?php echo $this->Buonny->input_codigo_medico_readonly($this, 'codigo_medico', 'Médico', 'Médico','Cat', null, 'numero_conselho_pcmso', 'uf_conselho_pcmso', 'nome_medico_pcmso', 'cpf_medico_pcmso'); ?>

				<?php echo $this->BForm->input('numero_conselho_pcmso', array('style' => 'width: 80px;', 'label' => 'CRM', 'title' => ('CRM'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['numero_conselho'] : '')); ?>
				<?php echo $this->BForm->input('uf_conselho_pcmso', array('style' => 'width: 50px;', 'label' => 'UF', 'title' => ('UF'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['conselho_uf']  : '')); ?>
				<?php echo $this->BForm->input('nome_medico_pcmso', array('style' => 'width: 260px;', 'label' => 'Nome do Médico', 'title' => ('NOME'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['nome']  : '')); ?>
				<?php echo $this->BForm->input('cpf_medico_pcmso', array( 'class' => 'input-medium cpf', 'label' => 'CPF do Médico', 'title' => ('CPF'), 'readonly' => true, 'value' => (isset($this->data['Medico'])) ? $this->data['Medico']['cpf']  : '')); ?>
			</div>
		</div>

		<div class="form-group" >
			<div class="span2">
				<?php echo $this->BForm->input('data_cat_origem', array('type' => 'text', 'label' => 'Data CAT Origem:', 'class' => 'input-small data', 'value' => (empty($this->data['Cat']['data_cat_origem']))? false : true)) ?>
			</div>
			<div class="span4">
				<?php echo $this->BForm->input('numero_cat_origem', array('type' => 'text', 'label' => 'Número CAT Origem:', 'class' => 'input-small')); ?>
			</div>
		</div>


	</div>

<div class="form-actions">	
	<div>
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		<?= $html->link('Voltar', array('action' => 'index'), array('class' => 'btn')); ?>	
	</div>
</div>    
<?php $this->addScript($this->Buonny->link_js('corretoras.js')); ?>
<?php $this->addScript($this->Buonny->link_js('cat.js')); ?>
<?php $this->addScript($this->Buonny->link_js('clientes.js')); ?>