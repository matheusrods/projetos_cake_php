<?php echo $this->BForm->create('PropostaSemValidacao', array('type' => 'post' ,'url' => array('controller' => 'propostas_credenciamento','action' => 'incluir')));?>

	<!-- HASH RHHEALTH -->
	<?php echo $this->BForm->hidden('PropostaSemValidacao.codigo_empresa', array('value' => 'MQ==')); ?>
<div class="row" style="margin-bottom: 50px; background: #FFF; margin-top: -20px; padding: 20px;">
	<h2 class="center">Proposta de Credenciamento</h2>
	<div class="col-md-6">
		<div class="form-group">
			<h3 >Dados da Empresa:</h3><hr />
			<div class="input-group">
	    		<span class="input-group-addon">CNPJ Matriz</span>
	    		<?php echo $this->BForm->input('PropostaSemValidacao.codigo_documento', array('onblur' => 'proposta.validaCNPJ(this, 0, "incluir", "", 0);', 'class' => 'form-control cnpj', 'label' => false, 'style' => 'width: 55%;')); ?>
	    		
    			<img src="/portal/img/default.gif" id="cnpj_loading_0" style="padding: 0 0 0 10px; display: none;">
    			<label style="float: left; padding: 10px 0 0 10px; font-size: 10px; display: none;" id="link_auto_completar_cnpj"><a href="javascript:void(0);" onclick="proposta.carregaCNPJ();">COMPLETAR FORMULÁRIO</a></label>
			</div>			
			<div class="input-group">
    			<span class="input-group-addon">Razão Social</span>
    			<?php echo $this->BForm->input('PropostaSemValidacao.razao_social', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'data-required' => true)); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Nome Fantasia ( * )</span>
    			<?php echo $this->BForm->input('PropostaSemValidacao.nome_fantasia', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
			</div>
		</div>
		
		<div class="form-group">
			<h3 >Endereço Matriz:</h3><hr />
			<div class="input-group">
    			<span class="input-group-addon">Cep<br /></span>
    			<?php echo $this->BForm->input('PropostaCredEndereco2.0.cep', array('class' => 'form-control formata-cep', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
    			<img src="/portal/img/default.gif" id="carregando_0" style="padding: 10px 0 0 10px; display: none;">
    			<label style="float: left; padding: 10px 0 0 10px; font-size: 10px;" id="pesquisa_cep_0"><a href="javascript:void(0);" onclick="proposta.buscaCEP(0, 'PropostaCredEndereco2', 'PropostaSemValidacao');">COMPLETAR ENDEREÇO</a></label>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Logradouro</span>
    			<?php echo $this->BForm->input('PropostaCredEndereco2.0.logradouro', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Número</span>
    			<?php echo $this->BForm->input('PropostaCredEndereco2.0.numero', array('class' => 'form-control', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Complemento</span>
    			<?php echo $this->BForm->input('PropostaCredEndereco2.0.complemento', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Bairro</span>
    			<?php echo $this->BForm->input('PropostaCredEndereco2.0.bairro', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Estado ( * )</span>
	    			<?php echo $this->BForm->input('PropostaCredEndereco2.0.estado', array('label' => false, 'class' => 'form-control uf', 'style' => 'width: 100%; text-transform: uppercase;', 'empty' => false, 'options' => $estados, 'onchange' => 'proposta.buscaCidade(this, null, null, "PropostaCredEndereco20CodigoCidadeEndereco", null, null, 0)')) ?>
			</div>			
			<div class="input-group">
    			<span class="input-group-addon">Cidade ( * )</span>
    			<span id="cidade_combo_0">
    				<?php echo $this->BForm->input('PropostaCredEndereco2.0.cidade', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'empty' => false)); ?>
    			</span>
    			<div id="carregando_cidade_0" style="display: none; border: 1px solid #CCCCCC; padding: 8px;">
    				<img src="/portal/img/ajax-loader.gif" border="0"/> (Carregando a lista de cidades)
    			</div>    			
			</div>
		</div>
		
		<div id="endereco_filial" class="form-group" style="display: <?php echo ((count($this->data['PropostaCredEndereco2']) > 1) ? '' : 'none'); ?>;">
			<h3>Endereço Filial:</h3><hr />
			<div id="enderecos">
				<?php if(isset($this->data['PropostaCredEndereco']) && count($this->data['PropostaCredEndereco']) > 1) : ?>
					<?php foreach($this->data['PropostaCredEndereco'] as $key => $endereco) : ?>
						<?php if($key > 0) : ?>
							<h4 >Filial: <?php echo $key; ?>:</h4><hr />
							<div class="form-group">
								<div class="input-group">
					    			<span class="input-group-addon">CNPJ Filial ( * )</span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco2.'.$key.'.codigo_documento', array('onblur' => 'proposta.validaCNPJ(this, 0, "incluir", "");', 'class' => 'form-control cnpj', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
					    			<img src="/portal/img/default.gif" id="cnpj_loading_<?php echo $key; ?>" style="padding: 0 0 0 10px; display: none;">
								</div>
								<div class="input-group">
					    			<span class="input-group-addon">Cep ( * )<br /></span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco2.'.$key.'.cep', array('class' => 'form-control formata-cep', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
								</div>
								<div class="input-group">
					    			<span class="input-group-addon">Logradouro ( * )</span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco2.'.$key.'.logradouro', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
								</div>
								<div class="input-group">
					    			<span class="input-group-addon">Número ( * )</span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco2.'.$key.'.numero', array('class' => 'form-control', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
								</div>
								<div class="input-group">
					    			<span class="input-group-addon">Complemento</span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco2.'.$key.'.complemento', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
								</div>
								<div class="input-group">
					    			<span class="input-group-addon">Bairro ( * )</span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco2.'.$key.'.bairro', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
								</div>
								<div class="input-group">
					    			<span class="input-group-addon">Estado ( * )</span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco2.'.$key.'.estado', array('label' => false, 'class' => 'form-control uf', 'style' => 'width: 100%; text-transform: uppercase;', 'empty' => false, 'options' => $estados, 'onchange' => 'proposta.buscaCidade(this, null, null, "PropostaCredEndereco2' . $key . 'CodigoCidadeEndereco", null, null, ' . $key . ')')) ?>
								</div>								
								<div class="input-group">
					    			<span class="input-group-addon">Cidade ( * )</span>
					    			<span id="cidade_combo_<?php echo $key; ?>">
					    				<?php echo $this->BForm->input('PropostaCredEndereco2.'.$key.'.cidade', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'empty' => false)); ?>
					    			</span>
					    			<div id="carregando_cidade_<?php echo $key; ?>" style="display: none; border: 1px solid #CCCCCC; padding: 8px;">
					    				<img src="/portal/img/ajax-loader.gif" border="0"/> (Carregando a lista de cidades)
					    			</div> 
								</div>
							</div>						
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>			
			</div>
		</div>
		
		<!-- 
        <a href="javascript:void(0);" onclick="proposta.addEndereco('PropostaCredEndereco2', 'PropostaSemValidacao', 'incluir');" class="btn btn-warning btn-sm right">
			<span class="glyphicon glyphicon glyphicon-plus"></span> Incluir Endereço Filial
        </a>
         -->
         
		<div class="form-group">
			<h3 >Informações Bancárias:</h3><hr />
			
			<div class="input-group">
    			<span class="input-group-addon">Dia de recebimento:</span>
    			<div class="">
    				<?php echo $this->BForm->input('PropostaSemValidacao.melhor_dia_pagto', array('div' => false, 'label' => false, 'class' => 'form-control input-mini', 'legend' => false, 'options' => $dias, 'style' => 'width: 100px;', 'onchange' => '$("#dia").html($(this).val()); $("#info-pagto").show();')) ?>
    				<span id="info-pagto" style="font-size: 11px; display: none; padding: 10px 0 0 10px;">OK, seu Pagto. será feito todos os dias </span><span id="dia" style="font-size: 13px; padding-top: 10px; font-weight: bold;"></span>
    			</div>
			</div>		
			<div class="input-group">
    			<span class="input-group-addon">Como prefere receber?</span>
    			<div class="inline_labels">
    				<?php echo $this->BForm->input('PropostaSemValidacao.cobranca_boleto', array('div' => false, array('class' => 'inline_labels'), 'legend' => false, 'options' => array('1' => 'Vou gerar Boleto', '0' => 'Depósito em Conta'), 'type' => 'radio', 'onchange' => 'proposta.mostraPagto(this);')) ?>
    			</div>
			</div>
			<span id="pagto_deposito" style="display: none;">
				<div class="input-group">
    			<span class="input-group-addon">Banco</span>
    			<?php echo $this->BForm->input('PropostaSemValidacao.numero_banco', array('label' => false, 'class' => 'form-control uf', 'style' => 'width: 100%; text-transform: uppercase;', 'empty' => false, 'options' => $bancos)) ?>
			</div>			
			<div class="input-group">
    			<span class="input-group-addon">Tipo de Conta</span>
    			<div class="inline_labels">
    				<?php echo $this->BForm->input('PropostaSemValidacao.tipo_conta', array('div' => false, array('class' => 'inline_labels'), 'legend' => false, 'options' => array('1' => 'Conta Corrente', '0' => 'Conta Poupança'), 'type' => 'radio')) ?>
    			</div>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Agência</span>
    			<?php echo $this->BForm->input('PropostaSemValidacao.agencia', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Número de Conta</span>
    			<?php echo $this->BForm->input('PropostaSemValidacao.numero_conta', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Favorecido</span>
    			<?php echo $this->BForm->input('PropostaSemValidacao.favorecido', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
			</div>
			</span>
		</div>        		
	</div>
	
  	<div class="col-md-6">
		<div class="form-group clear">
			<h3 >Tipo de Serviço Prestado:</h3><hr />
			<span style="padding: 10px;"><?php echo $this->BForm->input('PropostaCredProduto.60', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge', 'value' => '1', 'onclick' => 'proposta.controlaServico(this, 60);', 'checked' => (isset($this->data['PropostaCredProduto']['60']) && ($this->data['PropostaCredProduto']['60'] == '1') ? '"checked"' : ''))); ?> Segurança</span>
			<span style="padding: 10px;"><?php echo $this->BForm->input('PropostaCredProduto.59', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge', 'value' => '1', 'onclick' => 'proposta.controlaServico(this, 59);', 'checked' => (isset($this->data['PropostaCredProduto']['59']) && ($this->data['PropostaCredProduto']['59'] == '1') ? '"checked"' : ''))); ?> Saúde</span>
			
			<?php if(isset($msg_tipo_servico) && !empty($msg_tipo_servico)) : ?>
				<div class="help-block error-message"><?php echo $msg_tipo_servico; ?></div>
			<?php endif; ?>
		</div>
			
		<div class="form-group clear tipo_engenharia" style="display: <?php echo (isset($this->data['PropostaCredProduto']['60']) && ($this->data['PropostaCredProduto']['60'] == '1')) ? '' : 'none'; ?>;">
			<h3 >Serviços de Engenharia:</h3><hr />
			<div id="engenharias">
				<?php if(isset($this->data['PropostaCredEngenharia']) && count($this->data['PropostaCredEngenharia'])) : ?>
					<?php foreach($this->data['PropostaCredEngenharia'] as $key => $exame) : ?>
						<div class="input-group">
							<div style="float: left; width: 102%">
			    				<span class="input-group-addon" style="width: 120px; float: left; height: 34px;">Serviço ( * )</span>
			    				<?php echo $this->BForm->input('PropostaCredEngenharia.'.($key).'.codigo_exame', array('label' => false, 'class' => 'form-control', 'style' => 'width: 70%; text-transform: uppercase;', 'multiple', 'empty' => false, 'options' => $engenharias)) ?>
			    			</div>
						</div>							
					<?php endforeach; ?>
				<?php else : ?>
					<div class="input-group">
						<div style="float: left; width: 102%">
		    				<span class="input-group-addon" style="width: 120px; float: left; height: 34px;">Serviço ( * )</span>
		    				<?php echo $this->BForm->input('PropostaCredEngenharia.0.codigo_exame', array('label' => false, 'class' => 'form-control', 'style' => 'width: 70%; text-transform: uppercase;', 'multiple', 'empty' => false, 'options' => $engenharias)) ?>
		    			</div>
					</div>
				<?php endif; ?>
			</div>
	        <a href="javascript:void(0);" onclick="proposta.addEngenharia(); setup_mascaras();" class="btn btn-warning btn-sm right">
	          <span class="glyphicon glyphicon glyphicon-plus"></span> Incluir Mais Serviços
	        </a>			
		</div>		
		
		<div class="form-group clear tipo_exame" style="display: <?php echo (isset($this->data['PropostaCredProduto']['59']) && ($this->data['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
			<h3 >Relação de Serviços:</h3><hr />
			
			<div class="row" >
				<a id="botao-tabela" href="javascript:void(0);" onclick="proposta.mostraTabela();" class="btn btn-danger btn-sm left" style="margin: 0 20px 0 20px;"><span class="glyphicon glyphicon-th-list"></span> Tabela Padrão</a>
				<a id="botao-tabela" href="javascript:void(0);" class="btn btn-danger btn-sm header right" style="margin: 0 20px 0 20px;"><span class="glyphicon glyphicon-th-list"></span> Cadastrar em Massa</a>
			</div>

			<div id="content" style="display:none; margin-top: 15px">
								
				<div class="well">
					<span class="" id="agenda_em_massa">
						<div class="input-group">
							<div style="float: left; width: 102%">

								<span class="input-group-addon" style="width: 80px; float: left; height: 34px;">Liberação</span>
								<?php echo $this->BForm->input('PropostaCredExame.tempo_liberacao', array('label' => false, 'class' => 'form-control', 'style' => 'width: 45%; text-transform: uppercase;', 'empty' => false, 'options' => $tempo_liberacao)) ?>
						
							</div>
						</div>

						<div class="input-group">
							<div class="btn btn-success right incluir_liberacao">Incluir</div>
						</div>	
					</span>
				</div>
				
			</div>

			<div class="input-group">
				<input type="checkbox" class="liberacao_select_all" style="margin:10px;"> Selecionar Todos</span>
			</div>			
							
			<div id="exames">
				<?php if(isset($this->data['PropostaCredExame']) && count($this->data['PropostaCredExame']) > 1) : ?>
					<?php foreach($this->data['PropostaCredExame'] as $key => $exame) : ?>
						<div class="input-group">
							<div style="float: left; width: 102%">
								<input type="checkbox" name="data[PropostaCredExame][<?= $key; ?>][liberacao_check]" id="PropostaCredExame<?= $key; ?>liberacao_check" class="select_liberacao" style="float: left; margin:10px;"/>
					
			    				<span class="input-group-addon" style="float: left; height: 34px;">Exame</span>
			    				<?php echo $this->BForm->input('PropostaCredExame.'.($key).'.codigo_exame', array('label' => false, 'class' => 'form-control', 'style' => 'width: 45%; text-transform: uppercase;', 'multiple', 'empty' => false, 'options' => $exames)) ?>
			    				<span class="input-group-addon"  style="float: left; height: 34px; margin-left: 5px;">Valor</span>
			    				<?php echo $this->BForm->input('PropostaCredExame.'.($key).'.valor', array('class' => 'form-control moeda', 'label' => false, 'style' => 'float: left; width: 15%; text-align: right;', 'multiple')); ?>

								<span class="input-group-addon" style="float: left; height: 34px;">Liberação</span>
			    				<?php echo $this->BForm->input('PropostaCredExame.'.($key).'.liberacao', array('label' => false, 'class' => 'form-control', 'required' => true, 'style' => 'width: 45%; text-transform: uppercase;', 'options' => $tempo_liberacao)) ?>
			    				
			    			</div>
						</div>						
					<?php endforeach; ?>
				<?php else : ?>
					<div class="input-group">
						<div style="display:inline-flex">

							<input type="checkbox" name="data[PropostaCredExame][0][liberacao_check]" id="PropostaCredExame0liberacao_check" class="select_liberacao" style="float: left; margin:10px;"/>
					
		    				<span class="input-group-addon" style="width:65px;height: 34px;">Exame</span>
		    				<?php echo $this->BForm->input('PropostaCredExame.0.codigo_exame', array('label' => false, 'class' => 'form-control', 'style' => 'width:108px;text-transform: uppercase;', 'multiple', 'empty' => false, 'options' => $exames)) ?>
		    				<span class="input-group-addon"  style="width:55px;height: 34px; margin-left: 5px;">Valor</span>
		    				<?php echo $this->BForm->input('PropostaCredExame.0.valor', array('class' => 'form-control moeda', 'label' => false, 'style' => 'width:73px;text-align: right;margin-right:5px;', 'multiple')); ?>
		    			
							<span class="input-group-addon" style="width:80px;height: 34px;">Liberação</span>
		    				<?php echo $this->BForm->input('PropostaCredExame.0.liberacao', array('label' => false, 'class' => 'form-control', 'required' => true, 'style' => 'width:108px;text-transform: uppercase;', 'options' => $tempo_liberacao)) ?>
		    				
						</div>
					</div>
				<?php endif; ?>
			</div>
	        <a href="javascript:void(0);" onclick="proposta.addExame(); setup_mascaras();" class="btn btn-warning btn-sm right">
	          <span class="glyphicon glyphicon glyphicon-plus"></span> Incluir Mais Exames
	        </a>
		</div>
		<div class="form-group clear tipo_exame" style="display: <?php echo (isset($this->data['PropostaCredProduto']['59']) && ($this->data['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
			<h3 >Corpo Clínico <span style="font-size: 16px;">(Profissionais que realizam exames clínicos)</span></h3><hr />
			
			<div id="corpo_clinico">
				<?php if(isset($this->data['Medico']) && count($this->data['Medico']) > 1) : ?>
					<?php foreach($this->data['Medico'] as $key => $medico) : ?>
						<div class="input-group">
							<div style="float: left; width: 102%">
			    				<span class="input-group-addon" style="width: 80px; float: left; height: 34px;">Nome</span>
			    				<?php echo $this->BForm->input('Medico.'.($key).'.nome', array('class' => 'form-control', 'label' => false, 'style' => 'float: left; width: 40%;', 'multiple')); ?>
			    				<?php echo $this->BForm->input('Medico.'.($key).'.codigo_conselho_profissional', array('label' => false, 'class' => 'form-control', 'style' => 'width: 85px; text-transform: uppercase;', 'empty' => false, 'options' => $list_conselhos)) ?>
			    				<?php echo $this->BForm->input('Medico.'.($key).'.numero_conselho', array('class' => 'form-control', 'label' => false, 'style' => 'float: left; width: 15%;', 'multiple')); ?>
			    				<?php echo $this->BForm->input('Medico.'.($key).'.conselho_uf', array('label' => false, 'class' => 'form-control uf', 'style' => 'width: 13%; text-transform: uppercase;', 'empty' => false, 'options' => $estados_medicos)) ?>
			    			</div>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="input-group">
						<div style="float: left; width: 102%">
		    				<span class="input-group-addon" style="width: 80px; float: left; height: 34px;">Nome</span>
		    				<?php echo $this->BForm->input('Medico.0.nome', array('class' => 'form-control', 'label' => false, 'style' => 'float: left; width: 40%;', 'multiple')); ?>
		    				<?php echo $this->BForm->input('Medico.0.codigo_conselho_profissional', array('label' => false, 'class' => 'form-control', 'style' => 'width: 85px; text-transform: uppercase;', 'empty' => false, 'options' => $list_conselhos)) ?>
		    				<?php echo $this->BForm->input('Medico.0.numero_conselho', array('class' => 'form-control', 'label' => false, 'style' => 'float: left; width: 15%;', 'multiple')); ?>
		    				<?php echo $this->BForm->input('Medico.0.conselho_uf', array('label' => false, 'class' => 'form-control uf', 'style' => 'width: 13%; text-transform: uppercase;', 'empty' => false, 'options' => $estados_medicos)) ?>
		    			</div>
					</div>
				<?php endif; ?>
			</div>
	        <a href="javascript:void(0);" onclick="proposta.addMedico();" class="btn btn-warning btn-sm right">
	          <span class="glyphicon glyphicon glyphicon-plus"></span> Incluir Mais Profissionais
	        </a>
		</div>
		
		<div class="form-group clear tipo_exame" style="display: <?php echo (isset($this->data['PropostaCredProduto']['59']) && ($this->data['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
			<h3>(Dias e Horários) de Atendimento</h3><hr />
			<?php if(isset($this->data['Horario']) && count($this->data['Horario']) > 1) : ?>
				<?php foreach($this->data['Horario'] as $key => $horario) : ?>
					<div id="periodos" class="form-group">
						<div id="periodo_<?php echo $key; ?>" class="periodo">
							<div class="row">
								<div class="dias">
									<span>DIAS DA SEMANA:</span>
									<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.seg', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Seg.
									<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.ter', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Ter.
									<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.qua', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qua.
									<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.qui', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qui.
									<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.sex', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sex.
									<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.sab', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sab.
									<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.dom', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Dom.
								</div>
				    			<?php if($key > 0) : ?>
							        <a href="javascript:void(0);" onclick="$(this).parents('.periodo').remove();" class="btn btn-danger btn-sm right" title="Remover Periodo">
							          <span class="glyphicon glyphicon-minus"></span>
							        </a>							        			    			
				    			<?php else : ?>
							        <a href="javascript:void(0);" onclick="proposta.addPeriodo(); jQuery('.hora').mask('99:99');" class="btn btn-warning btn-sm right" title="Adicionar Horario / Dias">
							          <span class="glyphicon glyphicon glyphicon-plus"></span>
							        </a>				    			
				    			<?php endif; ?>
							</div>
							<div style="clear: both;"></div>
							<div id="horario">
								<div class="input-group">
					    			<span class="input-group-addon" style="width: 170px;">Horário: </span>
					    			<div class="inline_labels" style="padding: 5px 5px 0 0;">
						    			<label class="titulo"> DE </label>
						    			<?php echo $this->BForm->input('Horario.'.($key).'.de_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
						    			<label class="titulo">  ATÉ </label>
						    			<?php echo $this->BForm->input('Horario.'.($key).'.ate_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
						    			<div style="clear: both;"></div>
					    			</div>
								</div>					
							</div>
						</div>
					</div>				
				<?php endforeach; ?>
			<?php else : ?>
				<div id="periodos" class="form-group">
					<div id="periodo_0" class="periodo">
						<div class="row">
							<div class="dias">
								<span>DIAS DA SEMANA:</span>
								<?php echo $this->BForm->input('Horario.0.dias_semana.seg', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Seg.
								<?php echo $this->BForm->input('Horario.0.dias_semana.ter', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Ter.
								<?php echo $this->BForm->input('Horario.0.dias_semana.qua', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qua.
								<?php echo $this->BForm->input('Horario.0.dias_semana.qui', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qui.
								<?php echo $this->BForm->input('Horario.0.dias_semana.sex', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sex.
								<?php echo $this->BForm->input('Horario.0.dias_semana.sab', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sab.
								<?php echo $this->BForm->input('Horario.0.dias_semana.dom', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Dom.
							</div>
					        <a href="javascript:void(0);" onclick="proposta.addPeriodo(); jQuery('.hora').mask('99:99');" class="btn btn-warning btn-sm right" title="Adicionar Horario / Dias">
					          <span class="glyphicon glyphicon glyphicon-plus"></span>
					        </a>				    			
						</div>
						<div style="clear: both;"></div>
						<div id="horario">
							<div class="input-group">
				    			<span class="input-group-addon" style="width: 170px;">Horário: </span>
				    			<div class="inline_labels" style="padding: 5px 5px 0 0;">
					    			<label class="titulo"> DE </label>
					    			<?php echo $this->BForm->input('Horario.0.de_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
					    			<label class="titulo">  ATÉ </label>
					    			<?php echo $this->BForm->input('Horario.0.ate_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
					    			<div style="clear: both;"></div>
				    			</div>
							</div>					
						</div>
					</div>
				</div>				
			<?php endif; ?>
		</div>
			  	
		<div class="form-group">
			<h3 >Contatos e Funcionamento:</h3><hr />
			<div class="input-group tipo_exame" style="display: <?php echo (isset($this->data['PropostaCredProduto']['59']) && ($this->data['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
    			<span class="input-group-addon">Responsável Técnico</span>
    			<?php echo $this->BForm->input('PropostaSemValidacao.responsavel_tecnico_nome', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
			</div>
			<div class="input-group tipo_exame" style="display: <?php echo (isset($this->data['PropostaCredProduto']['59']) && ($this->data['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
    			<span class="input-group-addon">
    				<?php echo $this->BForm->input('PropostaSemValidacao.codigo_conselho_profissional', array('label' => false, 'class' => 'form-control', 'style' => 'width: 80%; border: 0; padding: 0; height: 20px; text-transform: uppercase;', 'empty' => false, 'options' => $list_conselhos)) ?>
    			</span>
    			<?php echo $this->BForm->input('PropostaSemValidacao.responsavel_tecnico_numero_conselho', array('class' => 'form-control', 'label' => false, 'style' => 'width: 75%;')); ?>
    			<?php echo $this->BForm->input('PropostaSemValidacao.responsavel_tecnico_conselho_uf', array('label' => false, 'class' => 'form-control uf', 'style' => 'width: 25%; text-transform: uppercase;', 'empty' => false, 'options' => $estados_medicos)) ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Responsável Administrativo</span>
    			<?php echo $this->BForm->input('PropostaSemValidacao.responsavel_administrativo', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Telefone ( * )</span>
    			<?php echo $this->BForm->input('PropostaSemValidacao.telefone', array('class' => 'form-control telefone', 'maxLength' => 14,'label' => false, 'style' => 'width: 55%;')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Fax</span>
    			<?php echo $this->BForm->input('PropostaSemValidacao.fax', array('class' => 'form-control telefone','maxLength' => 14, 'label' => false, 'style' => 'width: 55%;')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Celular</span>
    			<?php echo $this->BForm->input('PropostaSemValidacao.celular', array('class' => 'form-control telefone', 'maxLength' => 15, 'label' => false, 'style' => 'width: 55%;')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">E-mail ( * )</span>
    			<?php echo $this->BForm->input('PropostaSemValidacao.email', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
			</div>
			<div class="input-group tipo_exame" style="display: <?php echo (isset($this->data['PropostaCredProduto']['59']) && ($this->data['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
    			<span class="input-group-addon">Todos os Exames são feitos em um único local ?</span>
    			<div class="inline_labels">
    				<?php echo $this->BForm->input('PropostaSemValidacao.exames_local_unico', array('div' => false, array('class' => 'inline_labels'), 'legend' => false, 'options' => array('1' => 'Sim', '0' => 'Não'), 'type' => 'radio')) ?>
    			</div>
			</div>			
			<div class="input-group tipo_exame" style="display: <?php echo (isset($this->data['PropostaCredProduto']['59']) && ($this->data['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
    			<span class="input-group-addon">Tipo de Atendimento</span>
    			<div class="inline_labels">
    				<?php echo $this->BForm->input('PropostaSemValidacao.tipo_atendimento', array('div' => false, array('class' => 'inline_labels'), 'legend' => false, 'options' => array('1' => 'Hora Marcada', '0' => 'Ordem de Chegada'), 'type' => 'radio')) ?>
    			</div>
			</div>
			<div class="input-group">
    			<span class="input-group-addon" style="white-space: normal;width: 360px;">Possui disponibilidade para utilização do portal RHhealth (acesso via web)</span>
    			<div class="inline_labels" style="height: 42px">
    				<?php echo $this->BForm->input('PropostaSemValidacao.acesso_portal', array('div' => false, array('class' => 'inline_labels'), 'legend' => false, 'options' => array('1' => 'Sim', '0' => 'Não'), 'type' => 'radio')) ?>
    			</div>
			</div>
		</div>  	
  	</div>
</div>
<div class="row" style="padding: 0 25px 25px 0;">
	<div class="form-actions right">
		<a href="/" class="btn btn-default btn-lg"><i class="glyphicon glyphicon-fast-backward"></i> Voltar</a>
		<button type=submit class="btn btn-success btn-lg"><i class="glyphicon glyphicon-share"></i> Enviar para Análise</button>
	</div>
</div>
    
<?php echo $this->BForm->end(); ?>
<!-- modelos clonados no javascript -->
<div id="modelos">
	<div class="modal fade" id="modal_carregando">
		<div class="modal-dialog modal-sm" style="position: static;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="gridSystemModalLabel">Aguarde, carregando tabela...</h4>
				</div>
		    	<div class="modal-body">
		    		<img src="/portal/img/ajax-loader.gif" style="padding: 10px;">
		    	</div>
		    </div>
		</div>
	</div>
	<div class="modal fade" id="modal_tabela_padrao">
		<div class="modal-dialog modal-lg" style="position: static;">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title" id="gridSystemModalLabel">Tabela de Preços Padrão:</h4>
					<label><a href="javascript:void(0);" onclick="proposta.populaCamposExames(); $('#modal_tabela_padrao').modal('hide');" class="btn btn-success btn-sm right" title="Incluir">Incluir na Proposta!</a></label>
					<div class="clear"></div>
				</div>
		    	<div class="modal-body" style="height: 600px; overflow: scroll;">
					<table style="width: 100%" class="table-striped">
						<tr>
							<td class="center" style="width: 110px;"><a href="javascript:void(0);" onclick="proposta.checkAll_Exames(this);">Marcar Todos</a></td>
							<td>Exame</td>
							<td style="text-align: right;">Valor Base</td>
						</tr>			
						<?php foreach($tabela_padrao as $key => $campo) : ?>
							<tr>
								<td style="width: 110px; text-align: center;"><input class="checkbox_exames" type="checkbox" value="<?php echo $campo['codigo']; ?>" name="tabela.<?php echo $key; ?>.exame"></td>
								<td><?php echo strtoupper(utf8_encode($campo['nome'])); ?></td>
								<td style="text-align: right;">R$ <?php echo $campo['valor']; ?></td>
							</tr>				
						<?php endforeach; ?>
					</table>
		    	</div>
		    </div>
		</div>
	</div>
	<div id="modelo_corpo_clinico" style="display: none;">
		<div class="input-group">
			<div style="float: left; width: 102%">
    			<span class="input-group-addon" style="width: 80px; float: left; height: 34px;">Nome ( * )</span>
    			<?php echo $this->BForm->input('Medico.X.nome', array('class' => 'form-control', 'label' => false, 'style' => 'float: left; width: 40%;', 'multiple')); ?>
    			<?php echo $this->BForm->input('Medico.X.codigo_conselho_profissional', array('label' => false, 'class' => 'form-control', 'style' => 'width: 85px; text-transform: uppercase;', 'empty' => false, 'options' => $list_conselhos)) ?>
    			<?php echo $this->BForm->input('Medico.X.numero_conselho', array('class' => 'form-control', 'label' => false, 'style' => 'float: left; width: 15%;', 'multiple')); ?>
    			<?php echo $this->BForm->input('Medico.X.conselho_uf', array('label' => false, 'class' => 'form-control uf', 'style' => 'width: 13%; text-transform: uppercase;', 'empty' => false, 'options' => $estados_medicos)) ?>
    		</div>
		</div>
	</div>
	<div id="modelo_exames" style="display: none;">
		<div class="input-group">
			<div style="display:inline-flex;">
				<input type="checkbox" name="data[PropostaCredExame][X][liberacao_check]" id="PropostaCredExameXliberacao_check" class="select_liberacao" style="float: left; margin:10px;"/>
    			<span class="input-group-addon" style="width:65px;height: 34px;">Exame</span>
    			<?php echo $this->BForm->input('PropostaCredExame.X.codigo_exame', array('label' => false, 'class' => 'form-control', 'style' => 'width:108px;text-transform: uppercase;', 'multiple', 'empty' => false, 'options' => $exames)) ?>
    			<span class="input-group-addon"  style="width:55px;height: 34px; margin-left: 5px;">Valor</span>
    			<?php echo $this->BForm->input('PropostaCredExame.X.valor', array('class' => 'form-control moeda', 'label' => false, 'style' => 'width:73px;text-align: right;margin-right:5px;', 'multiple')); ?>

				<span class="input-group-addon" style="width:80px;height: 34px;">Liberação</span>
    			<?php echo $this->BForm->input('PropostaCredExame.X.liberacao', array('label' => false, 'class' => 'form-control', 'required' => true, 'style' => 'width:108px;text-transform: uppercase;', 'multiple', 'empty' => false, 'options' => $tempo_liberacao)) ?>
    		</div>
		</div>
	</div>
	<div id="modelo_engenharias" style="display: none;">
		<div class="input-group">
			<div style="float: left; width: 102%">
    			<span class="input-group-addon" style="width: 120px; float: left; height: 34px;">Servico ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredEngenharia.X.codigo_exame', array('label' => false, 'class' => 'form-control', 'style' => 'width: 70%; text-transform: uppercase;', 'multiple', 'empty' => false, 'options' => $engenharias)) ?>
    		</div>
		</div>
	</div>	
	<div id="modelo_endereco" style="display: none;">
		<div class="form-group">
			<h4>Filial [X]</h4>
			<div class="input-group">
    			<span class="input-group-addon">CNPJ Filial ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredEndereco2.X.codigo_documento', array('class' => 'form-control', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
				<img src="/portal/img/default.gif" id="cnpj_loading_X" style="padding: 0 0 0 10px; display: none;">
			</div>
			<div class="input-group">
	    		<span class="input-group-addon">Cep ( * )<br /></span>
	    		<?php echo $this->BForm->input('PropostaCredEndereco2.X.cep', array('class' => 'form-control formata-cep', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
    			<img src="/portal/img/default.gif" id="carregando_X" style="padding: 10px 0 0 10px; display: none;">
    			<label style="float: left; padding: 10px 0 0 10px; font-size: 10px;" id="pesquisa_cep_X"><a href="javascript:void(0);">COMPLETAR ENDEREÇO</a></label>
			</div>
			<div class="input-group">
	    		<span class="input-group-addon">Logradouro ( * )</span>
	    		<?php echo $this->BForm->input('PropostaCredEndereco2.X.logradouro', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
			</div>
			<div class="input-group">
	    		<span class="input-group-addon">Número ( * )</span>
	    		<?php echo $this->BForm->input('PropostaCredEndereco2.X.numero', array('class' => 'form-control', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
			</div>
			<div class="input-group">
	    		<span class="input-group-addon">Complemento</span>
	    		<?php echo $this->BForm->input('PropostaCredEndereco2.X.complemento', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
			</div>
			<div class="input-group">
	    		<span class="input-group-addon">Bairro ( * )</span>
	    		<?php echo $this->BForm->input('PropostaCredEndereco2.X.bairro', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
			</div>
			<div class="input-group">
	    		<span class="input-group-addon">Estado ( * )</span>
	    		<?php echo $this->BForm->input('PropostaCredEndereco2.X.estado', array('label' => false, 'class' => 'form-control uf', 'style' => 'width: 100%; text-transform: uppercase;', 'empty' => false, 'options' => $estados)) ?>
			</div>			
			<div class="input-group">
	    		<span class="input-group-addon">Cidade ( * )</span>
    			<span id="cidade_combo_<?php echo $key; ?>">
    				<?php echo $this->BForm->input('PropostaCredEndereco2.'.$key.'.cidade', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'empty' => false)); ?>	
    			</span>
    			<div id="carregando_cidade_<?php echo $key; ?>" style="display: none; border: 1px solid #CCCCCC; padding: 8px;">
    				<img src="/portal/img/ajax-loader.gif" border="0"/> (Carregando a lista de cidades)
    			</div>
			</div>
		</div>
	</div>
	<div id="modelo_periodo" style="display:none;">
		<div id="periodos" class="form-group">
			<div id="periodo_X" class="periodo">
				<div class="row">
					<div class="dias">
						<span>DIAS DA SEMANA:</span>
						<?php echo $this->BForm->input('Horario.X.dias_semana.seg', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Seg.
						<?php echo $this->BForm->input('Horario.X.dias_semana.ter', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Ter.
						<?php echo $this->BForm->input('Horario.X.dias_semana.qua', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qua.
						<?php echo $this->BForm->input('Horario.X.dias_semana.qui', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qui.
						<?php echo $this->BForm->input('Horario.X.dias_semana.sex', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sex.
						<?php echo $this->BForm->input('Horario.X.dias_semana.sab', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sab.
						<?php echo $this->BForm->input('Horario.X.dias_semana.dom', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Dom.
					</div>
			        <a href="javascript:void(0);" onclick="$(this).parents('.periodo').remove();" class="btn btn-danger btn-sm right" title="Remover Periodo">
			          <span class="glyphicon glyphicon-minus"></span>
			        </a>				    			
				</div>
				<div style="clear: both;"></div>
				<div id="horario">
					<div class="input-group">
		    			<span class="input-group-addon" style="width: 170px;">Horário: </span>
		    			<div class="inline_labels" style="padding: 5px 5px 0 0;">
			    			<label class="titulo"> DE </label>
			    			<?php echo $this->BForm->input('Horario.X.de_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
			    			<label class="titulo">  ATÉ </label>
			    			<?php echo $this->BForm->input('Horario.X.ate_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
			    			<div style="clear: both;"></div>
		    			</div>
					</div>					
				</div>
			</div>
		</div>
	</div>
</div>
<!-- FIM modelos clonados no javascript -->
<div class="modal fade" id="modal">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Aguarde...</h4>
			</div>
	    	<div class="modal-body">
	    		Verificando na Receita Federal!
	    		<br />
	    		<img src="/portal/img/ajax-loader.gif">
	    		<br />
	    	</div>
	    </div>
	</div>
</div>
<div class="modal fade" id="modal_receita">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Confirmação Humana:</h4>
			</div>
	    	<div class="modal-body">
	    		<img src="/portal/img/ajax-loader-medio.gif" id="carregando_captcha" border="0" style="padding-left: 5px; display: none;"/>
	    		<img src="/portal/multi_empresas/getcaptcha?<?php echo time(); ?>" id="img_captcha" border="0">
	    		<br /><br />
	    		<?php echo $this->BForm->input('texto_captcha', array('class' => 'form-control', 'label' => false, 'placeholder' => 'Digite o texto acima', 'style' => 'width: 100%;', 'data-required' => true)); ?>
	    		<a href="javascript:void(0);" onclick="proposta.trocaCaptcha();" id="troca_imagem">Trocar Imagem!</a>
				<img src="/portal/img/ajax-loader.gif" id="carregando_receita" border="0" style="padding: 10px 0 0 10px; display: none;"/>
	    		<br />
	    		<a href="javascript:void(0);" class="btn btn-success right" onclick="$('#troca_imagem').hide(); proposta.enviaCaptcha(this, 0, 'incluir');"><i class="icon-white icon-ok-sign"></i> Enviar</a>
	    		<a href="javascript:void(0);" class="btn btn-danger right" onclick="$('#modal_receita').modal('hide');" style="margin-right: 5px;"><i class="icon-white icon-remove-sign"></i> Fechar</a>
	    		<br /><br />
	    	</div>
	    </div>
	</div>
</div>
<div class="modal fade" id="modal_BO">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Houve algum erro!</h4>
			</div>
	    	<div class="modal-body">,
				<div id="msg_error"></div>	    		
	    		<br /><br />
	    	</div>
	    </div>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('$(function() { setup_mascaras(); setup_time(); });'); ?>
<?php echo $this->Buonny->link_js('proposta_credenciamento'); ?>

<script>

    $(function(){

        $(".liberacao_select_all").on("change", function(){

            if ($(this).is(":checked")) {				
                $('#exames .select_liberacao').prop('checked','checked');
            } else {
                $('#exames .select_liberacao').removeProp('checked');
            }
        })

		$(".header").click(function () {

			$content = $("#content");
			//open up the content needed - toggle the slide- if visible, slide up, if not slidedown.
			$content.slideToggle(500, function () {

			});

		});


		$(".incluir_liberacao").on("click", function(){
			var tempo_liberacao =$("#PropostaCredExameTempoLiberacao").val();

			if (tempo_liberacao.lenght == 0) {
				alert("Selecione uma opção no tempo Tempo de Liberação.");
				return;
			}

			$("#exames .input-group").each(function(){

				var selectId = $(this).find("select[id$='Liberacao']").attr('id');

				var tem_liberacao = $(this).find("input[id$='liberacao_check']").attr('id');

				if ($("#" + tem_liberacao + "").is(":checked")) {
				
					$("#" + selectId + " option").each(function(){

						if ($(this).val() == tempo_liberacao) {
							$(this).attr("selected", "selected");
						}

					});
				}							
			});	
			return true;
		});
		
    })
</script>