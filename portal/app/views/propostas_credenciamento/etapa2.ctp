<?php echo $this->BForm->create('PropostaCredenciamento', array('type' => 'post' ,'url' => array('controller' => 'propostas_credenciamento','action' => 'etapa2', $this->passedArgs[0])));?>
<?php echo $this->BForm->hidden('etapa', array('value' => 2)); ?>
<div class="row" style="margin-bottom: 50px; background: #FFF; margin-top: -20px; padding: 20px;">
	<h2 class="center">Complete sua Proposta de Credenciamento</h2>
	<div class="col-md-6">
		<div class="form-group clear">
			<h3 >Dados da Empresa:</h3><hr />
			<div class="input-group">
    			<span class="input-group-addon">CNPJ Matriz ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredenciamento.codigo_documento', array('onblur' => 'proposta.validaCNPJ(this, 0, "etapa2", "' . $this->passedArgs[0] .'", 0);', 'value' => $infoProposta['PropostaCredenciamento']['codigo_documento'], 'class' => 'form-control cnpj', 'label' => false, 'style' => 'width: 55%;')); ?>
    			
    			<img src="/portal/img/default.gif" id="cnpj_loading_0" style="padding: 0 0 0 10px; display: none;">
    			<label style="float: left; padding: 10px 0 0 10px; font-size: 10px; display: none;" id="link_auto_completar_cnpj"><a href="javascript:void(0);" onclick="proposta.carregaCNPJ();">COMPLETAR FORMULÁRIO</a></label>    			
			</div>			
			<div class="input-group">
    			<span class="input-group-addon">Razão Social ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredenciamento.razao_social', array('value' => $infoProposta['PropostaCredenciamento']['razao_social'], 'class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'data-required' => true)); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Nome Fantasia ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredenciamento.nome_fantasia', array('value' => $infoProposta['PropostaCredenciamento']['nome_fantasia'], 'class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
			</div>
		</div>
		
		<div class="form-group clear">
			<h3 >Endereço Matriz:</h3><hr />
			<div class="input-group">
    			<span class="input-group-addon">Cep ( * )<br /></span>
    			<?php echo $this->BForm->input('PropostaCredEndereco.0.cep', array('value' => isset($infoPropostaEndereco) ? $infoPropostaEndereco['PropostaCredEndereco']['cep'] : $this->data['PropostaCredEndereco'][0]['cep'], 'class' => 'form-control formata-cep', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
    			<img src="/portal/img/default.gif" id="carregando_0" style="padding: 10px 0 0 10px; display: none;">
    			<label style="float: left; padding: 10px 0 0 10px; font-size: 10px;" id="pesquisa_cep_0"><a href="javascript:void(0);" onclick="proposta.buscaCEP(0, 'PropostaCredEndereco', 'PropostaCredenciamento');">COMPLETAR ENDEREÇO</a></label>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Logradouro ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredEndereco.0.logradouro', array('value' => isset($infoPropostaEndereco) ? $infoPropostaEndereco['PropostaCredEndereco']['logradouro'] : $this->data['PropostaCredEndereco'][0]['logradouro'], 'class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Número ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredEndereco.0.numero', array('value' => isset($infoPropostaEndereco) ? $infoPropostaEndereco['PropostaCredEndereco']['numero'] : $this->data['PropostaCredEndereco'][0]['numero'], 'class' => 'form-control', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Complemento</span>
    			<?php echo $this->BForm->input('PropostaCredEndereco.0.complemento', array('value' => isset($infoPropostaEndereco) ? $infoPropostaEndereco['PropostaCredEndereco']['complemento'] : $this->data['PropostaCredEndereco'][0]['complemento'], 'class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Bairro ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredEndereco.0.bairro', array('value' => isset($infoPropostaEndereco) ? $infoPropostaEndereco['PropostaCredEndereco']['bairro'] : $this->data['PropostaCredEndereco'][0]['bairro'], 'class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Estado ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredEndereco.0.estado', array('value' => isset($infoPropostaEndereco) ? $infoPropostaEndereco['PropostaCredEndereco']['estado'] : $this->data['PropostaCredEndereco'][0]['estado'], 'label' => false, 'class' => 'form-control uf', 'style' => 'width: 100%; text-transform: uppercase;', 'empty' => false, 'options' => $estados, 'onchange' => 'proposta.buscaCidade(this, null, null, "PropostaCredEndereco0CodigoCidadeEndereco", null, null, 0)')) ?>
			</div>			
			<div class="input-group">
    			<span class="input-group-addon">Cidade ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredEndereco.0.cidade', array('value' => isset($infoPropostaEndereco) ? $infoPropostaEndereco['PropostaCredEndereco']['cidade'] : $this->data['PropostaCredEndereco'][0]['cidade'], 'class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'empty' => false)); ?>
			</div>
		</div>
		
		<div id="endereco_filial" class="form-group clear" style="display: <?php echo ((isset($this->data['PropostaCredEndereco']) && count($this->data['PropostaCredEndereco']) > 1) ? '' : 'none'); ?>;">
			<h3>Endereço Filial:</h3><hr />
			
			<div id="enderecos">
				<?php if(isset($this->data['PropostaCredEndereco']) && count($this->data['PropostaCredEndereco']) > 1) : ?>
					<?php foreach($this->data['PropostaCredEndereco'] as $key => $endereco) : ?>
						<?php if($key > 0) : ?>
							<div class="form-group clear" id="filial_<?php echo $key; ?>">
								<span style="font-size: 16px; font-weight: bold;">
									Filial
								</span>
								<a id="link_<?php echo $key; ?>" href="javascript:void(0);" class="label label-danger right" onclick="proposta.removeEndereco( $(this).attr('id') );">remover endereço</a>
								<div class="input-group">
					    			<span class="input-group-addon">CNPJ Filial ( * )</span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.codigo_documento', array('class' => 'form-control cnpj', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
					    			<img src="/portal/img/default.gif" id="cnpj_loading_<?php echo $key; ?>" style="padding: 0 0 0 10px; display: none;">
								</div>
								<div class="input-group">
					    			<span class="input-group-addon">Cep ( * )<br /></span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.cep', array('class' => 'form-control formata-cep', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
					    			<label id="pesquisa_cep_<?php echo $key; ?>" style="float: left; padding: 10px 0 0 10px; font-size: 10px;"><a href="http://www.buscacep.correios.com.br/sistemas/buscacep/" target="_blank">Pesquisar nos Correios</a></label>
					    			<img src="/portal/img/ajax-loader.gif" id="carregando_<?php echo $key; ?>" style="padding: 10px 0 0 10px; display: none;">
								</div>
								<div class="input-group">
					    			<span class="input-group-addon">Logradouro ( * )</span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.logradouro', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
								</div>
								<div class="input-group">
					    			<span class="input-group-addon">Número ( * )</span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.numero', array('class' => 'form-control', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
								</div>
								<div class="input-group">
					    			<span class="input-group-addon">Complemento</span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.complemento', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
								</div>
								<div class="input-group">
					    			<span class="input-group-addon">Bairro ( * )</span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.bairro', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
								</div>
								<div class="input-group">
					    			<span class="input-group-addon">Estado ( * )</span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.estado', array('label' => false, 'class' => 'form-control uf', 'style' => 'width: 100%; text-transform: uppercase;', 'empty' => false, 'options' => $estados, 'onchange' => 'proposta.buscaCidade(this, null, "PropostaCredEndereco' . $key . 'CodigoCidadeEndereco", null, null, ' . $key . ')')) ?>
								</div>			
								<div class="input-group">
					    			<span class="input-group-addon">Cidade ( * )</span>
					    			<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.cidade', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
								</div>
							</div>						
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>			
			</div>
		</div>
        <a href="javascript:void(0);" onclick="proposta.addEndereco('PropostaCredEndereco', 'PropostaCredenciamento', 'etapa2');" class="btn btn-warning btn-sm right">
          <span class="glyphicon glyphicon glyphicon-plus"></span> Incluir Endereço Filial
        </a>
        
		<div class="form-group clear">
			<h3 >Informações Bancárias:</h3><hr />
			
			<div class="input-group">
    			<span class="input-group-addon">Dia de recebimento:</span>
    			<div class="">
    				<?php echo $this->BForm->input('PropostaCredenciamento.melhor_dia_pagto', array('div' => false, 'label' => false, 'class' => 'form-control input-mini', 'legend' => false, 'options' => $dias, 'style' => 'width: 100px;', 'onchange' => '$("#dia").html($(this).val()); $("#info-pagto").show();')) ?>
    				<span id="info-pagto" style="font-size: 11px; display: none; padding: 10px 0 0 10px;">OK, seu Pagto. será feito todos os dias </span><span id="dia" style="font-size: 13px; padding-top: 10px; font-weight: bold;"></span>.
    			</div>
			</div>		
			<div class="input-group">
    			<span class="input-group-addon">Como prefere receber?</span>
    			<div class="inline_labels">
    				<?php echo $this->BForm->input('PropostaCredenciamento.cobranca_boleto', array('div' => false, array('class' => 'inline_labels'), 'legend' => false, 'value' => $infoProposta['PropostaCredenciamento']['cobranca_boleto'], 'options' => array('0' => 'Depósito em Conta', '1' => 'Vou gerar Boleto'), 'type' => 'radio', 'onchange' => 'proposta.mostraPagto(this);')) ?>
    			</div>
			</div>
			<span id="pagto_deposito" style="display: <?php echo (isset($infoProposta['PropostaCredenciamento']['cobranca_boleto']) && $infoProposta['PropostaCredenciamento']['cobranca_boleto'] == '1') ? 'none' : ''; ?>;">
				<div class="input-group">
	    			<span class="input-group-addon">Banco</span>
	    			<?php echo $this->BForm->input('PropostaCredenciamento.numero_banco', array('value' => $infoProposta['PropostaCredenciamento']['numero_banco'], 'label' => false, 'class' => 'form-control uf', 'style' => 'width: 100%; text-transform: uppercase;', 'empty' => false, 'options' => $bancos)) ?>
				</div>			
				<div class="input-group">
	    			<span class="input-group-addon">Tipo de Conta</span>
	    			<div class="inline_labels">
	    				<?php echo $this->BForm->input('PropostaCredenciamento.tipo_conta', array('value' => $infoProposta['PropostaCredenciamento']['tipo_conta'] ,'div' => false, array('class' => 'inline_labels'), 'legend' => false, 'options' => array('1' => 'Conta Corrente', '0' => 'Conta Poupança'), 'type' => 'radio')) ?>
	    			</div>
				</div>
				<div class="input-group">
	    			<span class="input-group-addon">Agência</span>
	    			<?php echo $this->BForm->input('PropostaCredenciamento.agencia', array('value' => $infoProposta['PropostaCredenciamento']['agencia'], 'class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
				</div>
				<div class="input-group">
	    			<span class="input-group-addon">Número de Conta</span>
	    			<?php echo $this->BForm->input('PropostaCredenciamento.numero_conta', array('value' => $infoProposta['PropostaCredenciamento']['numero_conta'],'class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
				</div>
				<div class="input-group">
	    			<span class="input-group-addon">Favorecido</span>
	    			<?php echo $this->BForm->input('PropostaCredenciamento.favorecido', array('value' => $infoProposta['PropostaCredenciamento']['favorecido'],'class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
				</div>			
			</span>			
		</div>
	</div>
  	<div class="col-md-6">
  	
		<div class="form-group clear">
			<h3 >Tipo de Serviço Prestado:</h3><hr />
			<span style="padding: 10px;"><?php echo $this->BForm->input('PropostaCredProduto.60', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge', 'value' => '1', 'onclick' => 'proposta.controlaServico(this, 60);', 'checked' => (isset($infoProposta['PropostaCredProduto']['60']) && ($infoProposta['PropostaCredProduto']['60'] == '1') ? '"checked"' : ''))); ?> Segurança</span>
			<span style="padding: 10px;"><?php echo $this->BForm->input('PropostaCredProduto.59', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge', 'value' => '1', 'onclick' => 'proposta.controlaServico(this, 59);', 'checked' => (isset($infoProposta['PropostaCredProduto']['59']) && ($infoProposta['PropostaCredProduto']['59'] == '1') ? '"checked"' : ''))); ?> Saúde</span>
			
			<?php if(isset($msg_tipo_servico) && !empty($msg_tipo_servico)) : ?>
				<div class="help-block error-message"><?php echo $msg_tipo_servico; ?></div>
			<?php endif; ?>
		</div>
		
		<div class="form-group clear tipo_engenharia" style="display: <?php echo (isset($infoProposta['PropostaCredProduto']['60']) && ($infoProposta['PropostaCredProduto']['60'] == '1')) ? '' : 'none'; ?>;">
			<h3 >Serviços de Engenharia:</h3><hr />
			<div id="engenharias">
				<?php if(isset($this->data['PropostaCredEngenharia']) && count($this->data['PropostaCredEngenharia'])) : ?>
					<?php foreach($this->data['PropostaCredEngenharia'] as $key => $exame) : ?>
						<div class="input-group">
							<div style="float: left; width: 102%">
			    				<span class="input-group-addon" style="width: 120px; float: left; height: 34px;">Serviço ( * )</span>
			    				<?php echo $this->BForm->input('PropostaCredEngenharia.'.($key).'.codigo_exame', array('label' => false, 'class' => 'form-control', 'style' => 'width: 70%; text-transform: uppercase;', 'multiple', 'empty' => false, 'options' => $engenharias)) ?>
			    				<!-- <a href="javascript:void(0);" class="btn btn-danger" style="margin-left: 3px; padding: 5px 10px; font-size: 12px; font-weight: normal;" onclick="removeEngenharia(this, '<?php echo $exame['codigo_exame']; ?>');"> x </a>  -->
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
		
		<div class="form-group clear tipo_exame" style="display: <?php echo (isset($infoProposta['PropostaCredProduto']['59']) && ($infoProposta['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
			<h3 >Relação de Exames:</h3><hr />
			<div id="exames">
				<?php if(isset($this->data['PropostaCredExame']) && count($this->data['PropostaCredExame']) > 1) : ?>
					<?php foreach($this->data['PropostaCredExame'] as $key => $exame) : ?>
						<div class="input-group">
							<div style="display:inline-flex">
			    				<span class="input-group-addon" style="width:65px;height: 34px;">Exame</span>
			    				<?php echo $this->BForm->input('PropostaCredExame.'.($key).'.codigo_exame', array('label' => false, 'class' => 'form-control', 'style' => 'width:108px;text-transform: uppercase;', 'multiple', 'empty' => false, 'options' => $exames , 'disabled' => 'disabled' )) ?>
			    				
			    				<span class="input-group-addon"  style="width:55px;height: 34px; margin-left: 5px;">Valor</span>
			    				<?php echo $this->BForm->input('PropostaCredExame.'.($key).'.valor', array('value' => ($exame['valor'] != "0,00") ? $exame['valor'] : $exame['valor_contra_proposta'], 'class' => 'form-control moeda', 'label' => false, 'style' => 'width:73px;text-align: right;margin-right:5px;', 'multiple')); ?>
			    				
			    				<span class="input-group-addon" style="width:80px;height: 34px;">Liberação</span>
			    				<?php echo $this->BForm->input('PropostaCredExame.'.($key).'.liberacao', array('label' => false, 'class' => 'form-control', 'required' => true, 'style' => 'width:108px;text-transform: uppercase;', 'options' => $tempo_liberacao, 'disabled' => 'disabled')) ?>
			    				
			    				<a href="javascript:void(0);" class="btn btn-danger" style="margin-left: 3px; adding: 5px 10px; font-size: 12px; font-weight: normal;" onclick="removeExame(this, '<?php echo $exame['codigo_exame']; ?>', '<?php echo $codigo; ?>');" title="Não faço este exame!"> x </a>
			    			
								
							</div>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="input-group">
						<div style="float: left; width: 102%">
		    				<span class="input-group-addon" style="width: 80px; float: left; height: 34px;">Exame ( * )</span>
		    				<?php echo $this->BForm->input('PropostaCredExame.0.codigo_exame', array('label' => false, 'class' => 'form-control', 'style' => 'width: 47%; text-transform: uppercase;', 'multiple', 'empty' => false, 'options' => $exames)) ?>
		    				<span class="input-group-addon"  style="width: 80px; float: left; height: 34px; margin-left: 5px;">Valor ( * )</span>
		    				<?php echo $this->BForm->input('PropostaCredExame.0.valor', array('class' => 'form-control moeda', 'label' => false, 'style' => 'float: left; width: 15%; text-align: right;', 'multiple')); ?>
		    			</div>
					</div>
				<?php endif; ?>
			</div>
	        <a href="javascript:void(0);" onclick="proposta.addExame(); setup_mascaras();" class="btn btn-warning btn-sm right">
	          <span class="glyphicon glyphicon glyphicon-plus"></span> Incluir Mais Serviços
	        </a>			
		</div>
		<div class="clear"></div>
		<div class="form-group clear tipo_exame" style="display: <?php echo (isset($infoProposta['PropostaCredProduto']['59']) && ($infoProposta['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
			<h3 >Corpo Clínico <span style="font-size: 16px;">(Profissionais que realizam exames clínicos)</span></h3><hr />
			<div id="corpo_clinico">
				<?php if(isset($this->data['Medico']) && count($this->data['Medico']) >= 1) : ?>
					<?php foreach($this->data['Medico'] as $key => $medico) : ?>
						<div class="input-group">
							<div style="float: left; width: 102%">
			    				<span class="input-group-addon" style="width: 80px; float: left; height: 34px;">Nome ( * )</span>
			    				<?php echo $this->BForm->input('Medico.'.($key).'.nome', array('value' => $medico['nome'], 'class' => 'form-control', 'label' => false, 'style' => 'float: left; width: 34%;', 'multiple')); ?>
			    				<?php echo $this->BForm->input('Medico.'.($key).'.codigo_conselho_profissional', array('value' => $medico['codigo_conselho_profissional'], 'label' => false, 'class' => 'form-control', 'style' => 'width: 85px; text-transform: uppercase;', 'empty' => false, 'options' => $list_conselhos)) ?>
			    				<?php echo $this->BForm->input('Medico.'.($key).'.numero_conselho', array('value' => $medico['numero_conselho'], 'class' => 'form-control', 'label' => false, 'style' => 'float: left; width: 15%;', 'multiple')); ?>
			    				<?php echo $this->BForm->input('Medico.'.($key).'.conselho_uf', array('value' => $medico['conselho_uf'], 'label' => false, 'class' => 'form-control uf', 'style' => 'width: 13%; text-transform: uppercase;', 'empty' => false, 'options' => $estados_medico)) ?>
			    			</div>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="input-group">
						<div style="float: left; width: 102%">
		    				<span class="input-group-addon" style="width: 80px; float: left; height: 34px;">Nome ( * )</span>
		    				<?php echo $this->BForm->input('Medico.0.nome', array('class' => 'form-control', 'label' => false, 'style' => 'float: left; width: 34%;', 'multiple')); ?>
		    				<?php echo $this->BForm->input('Medico.0.codigo_conselho_profissional', array('label' => false, 'class' => 'form-control', 'style' => 'width: 85px; text-transform: uppercase;', 'empty' => false, 'options' => $list_conselhos)) ?>
		    				<?php echo $this->BForm->input('Medico.0.numero_conselho', array('class' => 'form-control', 'label' => false, 'style' => 'float: left; width: 15%;', 'multiple')); ?>
		    				<?php echo $this->BForm->input('Medico.0.conselho_uf', array('label' => false, 'class' => 'form-control uf', 'style' => 'width: 13%; text-transform: uppercase;', 'empty' => false, 'options' => $estados_medico)) ?>
		    			</div>
					</div>
				<?php endif; ?>
			</div>
	        <a href="javascript:void(0);" onclick="proposta.addMedico();" class="btn btn-warning btn-sm right">
	          <span class="glyphicon glyphicon glyphicon-plus"></span> Incluir Mais Profissionais
	        </a>
		</div>
		<div class="form-group clear">
			<div class="form-group clear tipo_exame" style="display: <?php echo (isset($infoProposta['PropostaCredProduto']['59']) && ($infoProposta['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
				<h3>(Dias e Horários) de Atendimento</h3><hr />
				<?php if(isset($this->data['Horario']) && count($this->data['Horario']) >= 1) : ?>
					<?php foreach($this->data['Horario'] as $key => $horario) : ?>
						<div id="periodos" class="form-group clear">
							<div id="periodo_<?php echo $key; ?>" class="periodo">
								<div class="row">
									<div class="dias">
										<span>DIAS DA SEMANA:</span>
										<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.seg', array('type'=>'checkbox', 'checked' => ((isset($horario['Horario']) && strstr($horario['Horario']['dias_semana'], 'seg')) || (isset($horario['dias_semana']['seg']) && ($horario['dias_semana']['seg'] == '1')) ) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Seg.
										<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.ter', array('type'=>'checkbox', 'checked' => ((isset($horario['Horario']) && strstr($horario['Horario']['dias_semana'], 'ter')) || (isset($horario['dias_semana']['ter']) && ($horario['dias_semana']['ter'] == '1')) ) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Ter.
										<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.qua', array('type'=>'checkbox', 'checked' => ((isset($horario['Horario']) && strstr($horario['Horario']['dias_semana'], 'qua')) || (isset($horario['dias_semana']['qua']) && ($horario['dias_semana']['qua'] == '1')) ) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qua.
										<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.qui', array('type'=>'checkbox', 'checked' => ((isset($horario['Horario']) && strstr($horario['Horario']['dias_semana'], 'qui')) || (isset($horario['dias_semana']['qui']) && ($horario['dias_semana']['qui'] == '1')) ) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qui.
										<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.sex', array('type'=>'checkbox', 'checked' => ((isset($horario['Horario']) && strstr($horario['Horario']['dias_semana'], 'sex')) || (isset($horario['dias_semana']['sex']) && ($horario['dias_semana']['sex'] == '1')) ) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sex.
										<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.sab', array('type'=>'checkbox', 'checked' => ((isset($horario['Horario']) && strstr($horario['Horario']['dias_semana'], 'sab')) || (isset($horario['dias_semana']['sab']) && ($horario['dias_semana']['sab'] == '1')) ) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sab.
										<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.dom', array('type'=>'checkbox', 'checked' => ((isset($horario['Horario']) && strstr($horario['Horario']['dias_semana'], 'dom')) || (isset($horario['dias_semana']['dom']) && ($horario['dias_semana']['dom'] == '1')) ) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Dom.
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
						    			<span class="input-group-addon" style="width: 170px;">Horário: (*) </span>
						    			<div class="inline_labels" style="padding: 5px 5px 0 0;">
							    			<label class="titulo"> DE </label>
							    			<?php if(isset($horario['Horario']['de_hora'])) : ?>
							    				<?php echo $this->BForm->input('Horario.'.($key).'.de_hora', array('value' => sprintf("%04s", $horario['Horario']['de_hora']), 'class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
							    			<?php elseif(isset($horario['de_hora'])) : ?>
							    				<?php echo $this->BForm->input('Horario.'.($key).'.de_hora', array('value' => sprintf("%04s", $horario['de_hora']), 'class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
							    			<?php else : ?>
							    				<?php echo $this->BForm->input('Horario.'.($key).'.de_hora', array('value' => '', 'class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
							    			<?php endif; ?>
							    			
							    			<label class="titulo">  ATÉ </label>
							    			<?php if(isset($horario['Horario']['de_hora'])) : ?>
							    				<?php echo $this->BForm->input('Horario.'.($key).'.ate_hora', array('value' => sprintf("%04s", $horario['Horario']['ate_hora']), 'class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
							    			<?php elseif(isset($horario['de_hora'])) : ?>
							    				<?php echo $this->BForm->input('Horario.'.($key).'.ate_hora', array('value' => sprintf("%04s", $horario['ate_hora']), 'class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
							    			<?php else : ?>
							    				<?php echo $this->BForm->input('Horario.'.($key).'.ate_hora', array('value' => '', 'class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
							    			<?php endif; ?>
							    			<div style="clear: both;"></div>
						    			</div>
									</div>					
								</div>
							</div>
						</div>				
					<?php endforeach; ?>
				<?php else : ?>
					<div id="periodos" class="form-group clear">
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
					    			<span class="input-group-addon" style="width: 170px;">Horário: (*) </span>
					    			<div class="inline_labels" style="padding: 5px 5px 0 0;">
						    			<label class="titulo"> DE </label>
						    			<?php echo $this->BForm->input('Horario.0.de_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
						    			<label class="titulo">  ATÉ </label>
						    			<?php echo $this->BForm->input('Horario.0.ate_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
						    			<div style="clear: both;"></div>
					    			</div>
								</div>					
							</div>
						</div>
					</div>				
				<?php endif; ?>
			</div>
			<div class="input-group tipo_exame" style="display: <?php echo (isset($infoProposta['PropostaCredProduto']['59']) && ($infoProposta['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
				<div class="form-group clear">
					<h3 >Algum exame possui horário de atendimento diferenciado?</h3><hr />
					<div class="form-group clear">
						<?php echo $this->BForm->input('Horario.horario_atendimento_diferenciado', array('div' => false, array('class' => 'inline_labels'), 'legend' => false, 'value' => empty($this->data['Horario']['horario_atendimento_diferenciado']) ? 0 : $this->data['Horario']['horario_atendimento_diferenciado'], 'options' => array('1' => 'Sim', '0' => 'Não'), 'type' => 'radio')) ?>
					</div>
				</div>
			</div>
			<div id="tableHorarioDiferenciado" class="form-group clear servicoDiferenciado" style="display: none">
				<?php if(isset($horario_diferenciado['HorarioDiferenciado'])) : ?>
					<?php foreach($horario_diferenciado['HorarioDiferenciado'] as $key => $horario_dif) : ?>
						<div id="periodos_horario_diferenciado" class="form-group clear">
							<div id="horarioDif_<?php echo $key; ?>" class="periodo">
								<div class="row">
									<div class="exame">
										<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.codigo_servico', 
	                                        array(
	                                            'options' => $exames_credenciado_combo, 
	                                            'empty' => 'Selecione o Exame',
	                                            'value' => $horario_dif['codigo_servico'],
	                                            'label' => 'Exames', 
	                                            'class' => 'form-control input-medium', 
	                                            'style' => 'width: 77%; margin-bottom: 0; margin-top: -6px', 
	                                            'div' 	=> false, 
	                                            'required' => false)); 
	                                    ?>
									</div>
									<div class="dias">
										<span>DIAS DA SEMANA:</span>
										<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.dias_semana.seg', array('type'=>'checkbox', 'checked' => ((isset($horario_dif['HorarioDiferenciado']) && strstr($horario_dif['HorarioDiferenciado']['dias_semana'], 'seg')) || (isset($horario_dif['dias_semana']['seg']) && ($horario_dif['dias_semana']['seg'] == '1')) ) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Seg.
										<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.dias_semana.ter', array('type'=>'checkbox', 'checked' => ((isset($horario_dif['HorarioDiferenciado']) && strstr($horario_dif['HorarioDiferenciado']['dias_semana'], 'ter')) || (isset($horario_dif['dias_semana']['ter']) && ($horario_dif['dias_semana']['ter'] == '1')) ) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Ter.
										<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.dias_semana.qua', array('type'=>'checkbox', 'checked' => ((isset($horario_dif['HorarioDiferenciado']) && strstr($horario_dif['HorarioDiferenciado']['dias_semana'], 'qua')) || (isset($horario_dif['dias_semana']['qua']) && ($horario_dif['dias_semana']['qua'] == '1')) ) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qua.
										<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.dias_semana.qui', array('type'=>'checkbox', 'checked' => ((isset($horario_dif['HorarioDiferenciado']) && strstr($horario_dif['HorarioDiferenciado']['dias_semana'], 'qui')) || (isset($horario_dif['dias_semana']['qui']) && ($horario_dif['dias_semana']['qui'] == '1')) ) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qui.
										<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.dias_semana.sex', array('type'=>'checkbox', 'checked' => ((isset($horario_dif['HorarioDiferenciado']) && strstr($horario_dif['HorarioDiferenciado']['dias_semana'], 'sex')) || (isset($horario_dif['dias_semana']['sex']) && ($horario_dif['dias_semana']['sex'] == '1')) ) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sex.
										<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.dias_semana.sab', array('type'=>'checkbox', 'checked' => ((isset($horario_dif['HorarioDiferenciado']) && strstr($horario_dif['HorarioDiferenciado']['dias_semana'], 'sab')) || (isset($horario_dif['dias_semana']['sab']) && ($horario_dif['dias_semana']['sab'] == '1')) ) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sab.
										<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.dias_semana.dom', array('type'=>'checkbox', 'checked' => ((isset($horario_dif['HorarioDiferenciado']) && strstr($horario_dif['HorarioDiferenciado']['dias_semana'], 'dom')) || (isset($horario_dif['dias_semana']['dom']) && ($horario_dif['dias_semana']['dom'] == '1')) ) ? 'checked' : '', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Dom.
									</div>
					    			<?php if($key > 0) : ?>
								        <a href="javascript:void(0);" onclick="$(this).parents('.periodo').remove();" class="btn btn-danger btn-sm right" title="Remover Periodo">
								          <span class="glyphicon glyphicon-minus"></span>
								        </a>
					    			<?php else : ?>
								        <a href="javascript:void(0);" onclick="proposta.addHorarioDiferenciadoEtapa2(); jQuery('.hora').mask('99:99');" class="btn btn-warning btn-sm right" title="Adicionar Horario / Dias">
								          <span class="glyphicon glyphicon glyphicon-plus"></span>
								        </a>
					    			<?php endif; ?>
								</div>
								<div style="clear: both;"></div>
								<div id="horario">
									<div class="input-group">
						    			<span class="input-group-addon" style="width: 170px;">Horário: (*) </span>
						    			<div class="inline_labels" style="padding: 5px 5px 0 0;">
							    			<label class="titulo"> DE </label>
							    			<?php if(isset($horario_dif['HorarioDiferenciado']['de_hora'])) : ?>
							    				<?php echo $this->BForm->input('HorarioDiferenciado.'.($key).'.de_hora', array('value' => sprintf("%04s", $horario_dif['HorarioDiferenciado']['de_hora']), 'class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
							    			<?php elseif(isset($horario_dif['de_hora'])) : ?>
							    				<?php echo $this->BForm->input('HorarioDiferenciado.'.($key).'.de_hora', array('value' => sprintf("%04s", $horario_dif['de_hora']), 'class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
							    			<?php else : ?>
							    				<?php echo $this->BForm->input('HorarioDiferenciado.'.($key).'.de_hora', array('value' => '', 'class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
							    			<?php endif; ?>
							    			
							    			<label class="titulo">  ATÉ </label>
							    			<?php if(isset($horario_dif['HorarioDiferenciado']['de_hora'])) : ?>
							    				<?php echo $this->BForm->input('HorarioDiferenciado.'.($key).'.ate_hora', array('value' => sprintf("%04s", $horario_dif['HorarioDiferenciado']['ate_hora']), 'class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
							    			<?php elseif(isset($horario_dif['de_hora'])) : ?>
							    				<?php echo $this->BForm->input('HorarioDiferenciado.'.($key).'.ate_hora', array('value' => sprintf("%04s", $horario_dif['ate_hora']), 'class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
							    			<?php else : ?>
							    				<?php echo $this->BForm->input('HorarioDiferenciado.'.($key).'.ate_hora', array('value' => '', 'class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
							    			<?php endif; ?>
							    			<div style="clear: both;"></div>
						    			</div>
									</div>					
								</div>
							</div>
						</div>				
					<?php endforeach; ?>
				<?php else : ?>
					<div id="periodos_horario_diferenciado" class="form-group clear">
						<div id="horarioDif_0" class="periodo">
							<div class="row">
								<div class="exame">
									<?php echo $this->BForm->input('HorarioDiferenciado.0.codigo_servico', 
                                        array(
                                           'options' => $exames_credenciado_combo, 
	                                            'empty' => 'Selecione o Exame',
	                                            'label' => 'Exames', 
	                                            'class' => 'form-control input-medium', 
	                                            'style' => 'width: 77%; margin-bottom: 0; margin-top: -6px', 
	                                            'div' 	=> false, 
	                                            'required' => false)); 
                                    ?>
								</div>
								<div class="dias">
									<span>DIAS DA SEMANA:</span>
									<?php echo $this->BForm->input('HorarioDiferenciado.0.dias_semana.seg', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Seg.
									<?php echo $this->BForm->input('HorarioDiferenciado.0.dias_semana.ter', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Ter.
									<?php echo $this->BForm->input('HorarioDiferenciado.0.dias_semana.qua', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qua.
									<?php echo $this->BForm->input('HorarioDiferenciado.0.dias_semana.qui', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qui.
									<?php echo $this->BForm->input('HorarioDiferenciado.0.dias_semana.sex', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sex.
									<?php echo $this->BForm->input('HorarioDiferenciado.0.dias_semana.sab', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sab.
									<?php echo $this->BForm->input('HorarioDiferenciado.0.dias_semana.dom', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Dom.
								</div>
						        <a href="javascript:void(0);" onclick="proposta.addHorarioDiferenciadoEtapa2(); jQuery('.hora').mask('99:99');" class="btn btn-warning btn-sm right" title="Adicionar Horario / Dias">
						          <span class="glyphicon glyphicon glyphicon-plus"></span>
						        </a>				    			
							</div>
							<div style="clear: both;"></div>
							<div id="horario">
								<div class="input-group">
					    			<span class="input-group-addon" style="width: 170px;">Horário: (*) </span>
					    			<div class="inline_labels" style="padding: 5px 5px 0 0;">
						    			<label class="titulo"> DE </label>
						    			<?php echo $this->BForm->input('HorarioDiferenciado.0.de_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
						    			<label class="titulo">  ATÉ </label>
						    			<?php echo $this->BForm->input('HorarioDiferenciado.0.ate_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'maxlength' => '5')); ?>
						    			<div style="clear: both;"></div>
					    			</div>
								</div>					
							</div>
						</div>
					</div>				
				<?php endif; ?>
			</div>
			
			<h3 >Contatos e Funcionamento:</h3><hr />
			<div class="input-group tipo_exame" style="display: <?php echo (isset($infoProposta['PropostaCredProduto']['59']) && ($infoProposta['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
    			<span class="input-group-addon">Responsável Técnico ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredenciamento.responsavel_tecnico_nome', array('value' => $infoProposta['PropostaCredenciamento']['responsavel_tecnico_nome'], 'class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
			</div>
			<div class="input-group tipo_exame" style="display: <?php echo (isset($infoProposta['PropostaCredProduto']['59']) && ($infoProposta['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
    			<span class="input-group-addon">Conselho ( * )</span>
    			
    			<?php echo $this->BForm->input('PropostaCredenciamento.codigo_conselho_profissional', array('value' => $infoProposta['PropostaCredenciamento']['codigo_conselho_profissional'], 'label' => false, 'class' => 'form-control', 'style' => 'width: 85px; text-transform: uppercase;', 'empty' => false, 'options' => $list_conselhos)) ?>
    			<?php echo $this->BForm->input('PropostaCredenciamento.responsavel_tecnico_numero_conselho', array('value' => $infoProposta['PropostaCredenciamento']['responsavel_tecnico_numero_conselho'], 'class' => 'form-control', 'label' => false, 'style' => 'width: 48%;')); ?>
    			<?php echo $this->BForm->input('PropostaCredenciamento.responsavel_tecnico_conselho_uf', array('value' => $infoProposta['PropostaCredenciamento']['responsavel_tecnico_conselho_uf'], 'label' => false, 'class' => 'form-control uf', 'style' => 'width: 25%; text-transform: uppercase;', 'empty' => false, 'options' => $estados_medico)) ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Responsável Administrativo ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredenciamento.responsavel_administrativo', array('value' => $infoProposta['PropostaCredenciamento']['responsavel_administrativo'], 'class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Telefone ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredenciamento.telefone', array('value' => $infoProposta['PropostaCredenciamento']['telefone'], 'class' => 'form-control telefone', 'maxLength' => 14, 'label' => false, 'style' => 'width: 55%;')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Fax</span>
    			<?php echo $this->BForm->input('PropostaCredenciamento.fax', array('value' => $infoProposta['PropostaCredenciamento']['fax'], 'class' => 'form-control telefone', 'maxLength' => 14, 'label' => false, 'style' => 'width: 55%;')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Celular</span>
    			<?php echo $this->BForm->input('PropostaCredenciamento.celular', array('value' => $infoProposta['PropostaCredenciamento']['celular'], 'class' => 'form-control telefone', 'maxLength' => 15, 'label' => false, 'style' => 'width: 55%;')); ?>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">E-mail ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredenciamento.email', array('value' => $infoProposta['PropostaCredenciamento']['email'], 'disabled' => 'disabled', 'class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
			</div>
			
			<div class="input-group tipo_exame" style="display: <?php echo (isset($infoProposta['PropostaCredProduto']['59']) && ($infoProposta['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
    			<span class="input-group-addon">Tipo de Atendimento ( * )</span>
    			<div class="inline_labels">
    				<?php echo $this->BForm->input('PropostaCredenciamento.tipo_atendimento', array('div' => false, array('class' => 'inline_labels'), 'legend' => false, 'value' => $infoProposta['PropostaCredenciamento']['tipo_atendimento'], 'options' => array('1' => 'Hora Marcada', '0' => 'Ordem de Chegada'), 'type' => 'radio')) ?>
    			</div>
			</div>
			<div class="input-group">
    			<span class="input-group-addon">Possui disponibilidade de acesso ao Portal RHHealth? ( * )</span>
    			<div class="inline_labels">
    				<?php echo $this->BForm->input('PropostaCredenciamento.acesso_portal', array('div' => false, array('class' => 'inline_labels'), 'legend' => false, 'value' => $infoProposta['PropostaCredenciamento']['acesso_portal'], 'options' => array('1' => 'Sim', '0' => 'Não'), 'type' => 'radio')) ?>
    			</div>
			</div>
			<div class="input-group tipo_exame" style="display: <?php echo (isset($infoProposta['PropostaCredProduto']['59']) && ($infoProposta['PropostaCredProduto']['59'] == '1')) ? '' : 'none'; ?>;">
    			<span class="input-group-addon">Todos os Exames são feitos em um único local ? ( * )</span>
    			<div class="inline_labels">
    				<?php echo $this->BForm->input('PropostaCredenciamento.exames_local_unico', array('div' => false, array('class' => 'inline_labels'), 'legend' => false, 'value' => $infoProposta['PropostaCredenciamento']['exames_local_unico'], 'options' => array('1' => 'Sim', '0' => 'Não'), 'type' => 'radio')) ?>
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
	<div id="modelo_corpo_clinico" style="display: none;">
		<div class="input-group">
			<div style="float: left; width: 102%">
    			<span class="input-group-addon" style="width: 80px; float: left; height: 34px;">Nome ( * )</span>
    			<?php echo $this->BForm->input('Medico.X.nome', array('class' => 'form-control', 'label' => false, 'style' => 'float: left; width: 34%;', 'multiple')); ?>
    			<?php echo $this->BForm->input('Medico.X.codigo_conselho_profissional', array('label' => false, 'class' => 'form-control', 'style' => 'width: 85px; text-transform: uppercase;', 'empty' => false, 'options' => $list_conselhos)) ?>
    			<?php echo $this->BForm->input('Medico.X.numero_conselho', array('class' => 'form-control', 'label' => false, 'style' => 'float: left; width: 15%;', 'multiple')); ?>
    			<?php echo $this->BForm->input('Medico.X.conselho_uf', array('label' => false, 'class' => 'form-control uf', 'style' => 'width: 13%; text-transform: uppercase;', 'empty' => false, 'options' => $estados_medico)) ?>
    		</div>
		</div>
	</div>
	<div id="modelo_exames" style="display: none;">
		<div class="input-group">
			<div style="float: left; width: 102%">
    			<span class="input-group-addon" style="width: 80px; float: left; height: 34px;">Exame ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredExame.X.codigo_exame', array('label' => false, 'class' => 'form-control', 'style' => 'width: 47%; text-transform: uppercase;', 'multiple', 'empty' => false, 'options' => $exames)) ?>
    			<span class="input-group-addon"  style="width: 80px; float: left; height: 34px; margin-left: 5px;">Valor ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredExame.X.valor', array('class' => 'form-control moeda', 'label' => false, 'style' => 'float: left; width: 15%; text-align: right;', 'multiple')); ?>
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
		<div class="form-group clear">
			<span style="font-size: 16px; font-weight: bold;" id="titulo_X">
				Filial
			</span>
			<a id="link_X" href="javascript:void(0);" class="label label-danger right" onclick="proposta.removeEndereco( $(this).attr('id') );">remover endereço</a>
			<div class="input-group">
    			<span class="input-group-addon">CNPJ Filial ( * )</span>
    			<?php echo $this->BForm->input('PropostaCredEndereco.X.codigo_documento', array('class' => 'form-control', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
    			<img src="/portal/img/default.gif" id="cnpj_loading_X" style="padding: 0 0 0 10px; display: none;">
			</div>
			<div class="input-group">
	    		<span class="input-group-addon">Cep ( * )<br /></span>
	    		<?php echo $this->BForm->input('PropostaCredEndereco.X.cep', array('class' => 'form-control formata-cep', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
    			<img src="/portal/img/default.gif" id="carregando_X" style="padding: 10px 0 0 10px; display: none;">
    			<label style="float: left; padding: 10px 0 0 10px; font-size: 10px;" id="pesquisa_cep_X"><a href="javascript:void(0);">COMPLETAR ENDEREÇO</a></label>
			</div>
			<div class="input-group">
	    		<span class="input-group-addon">Logradouro ( * )</span>
	    		<?php echo $this->BForm->input('PropostaCredEndereco.X.logradouro', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
			</div>
			<div class="input-group">
	    		<span class="input-group-addon">Número ( * )</span>
	    		<?php echo $this->BForm->input('PropostaCredEndereco.X.numero', array('class' => 'form-control', 'label' => false, 'style' => 'width: 55%;', 'multiple')); ?>
			</div>
			<div class="input-group">
	    		<span class="input-group-addon">Complemento</span>
	    		<?php echo $this->BForm->input('PropostaCredEndereco.X.complemento', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
			</div>
			<div class="input-group">
	    		<span class="input-group-addon">Bairro ( * )</span>
	    		<?php echo $this->BForm->input('PropostaCredEndereco.X.bairro', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;', 'multiple')); ?>
			</div>
			<div class="input-group">
	    		<span class="input-group-addon">Estado ( * )</span>
	    		<?php echo $this->BForm->input('PropostaCredEndereco.X.estado', array('label' => false, 'class' => 'form-control uf', 'style' => 'width: 100%; text-transform: uppercase;', 'empty' => false, 'options' => $estados)) ?>
			</div>			
			<div class="input-group">
	    		<span class="input-group-addon">Cidade ( * )</span>
    			<span id="cidade_combo_X">
    				<?php echo $this->BForm->input('PropostaCredEndereco.X.cidade', array('class' => 'form-control', 'label' => false, 'style' => 'width: 100%;')); ?>
    			</span>
    			<div id="carregando_cidade_X" style="display: none; border: 1px solid #CCCCCC; padding: 8px;">
    				<img src="/portal/img/ajax-loader.gif" border="0"/>
    			</div>
			</div>
		</div>
	</div>
	<div id="modelo_periodo" style="display:none;">
		<div id="periodos" class="form-group clear">
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
	<div id="modelo_periodo_diferenciado" style="display:none;">
		<div id="periodos_horario_diferenciado" class="form-group clear">
			<div id="horarioDif_X" class="periodo">
				<div class="row">
					<div class="exame">
						<?php echo $this->BForm->input('HorarioDiferenciado.X.codigo_servico', 
                            array(
                                'options' => $exames_credenciado_combo, 
                                'empty' => 'Selecione o Exame',
                                'label' => 'Exames', 
                                'class' => 'form-control input-medium', 
                                'style' => 'width: 77%; margin-bottom: 0; margin-top: -6px', 
                                'div' 	=> false, 
                                'required' => false)); 
						?>
					</div>
					<div class="dias">
						<span>DIAS DA SEMANA:</span>
						<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.seg', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Seg.
						<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.ter', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Ter.
						<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.qua', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qua.
						<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.qui', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qui.
						<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.sex', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sex.
						<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.sab', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sab.
						<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.dom', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Dom.
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
			    			<?php echo $this->BForm->input('HorarioDiferenciado.X.de_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
			    			<label class="titulo">  ATÉ </label>
			    			<?php echo $this->BForm->input('HorarioDiferenciado.X.ate_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
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
	    		Verificando CNPJ na Receita Federal!
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
	    		<a href="javascript:void(0);" class="btn btn-success right" onclick="$('#troca_imagem').hide(); proposta.enviaCaptcha(this, 0, 'etapa2');"><i class="icon-white icon-ok-sign"></i> Enviar</a>
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
	    	<div class="modal-body">
				<div id="msg_error"></div>	    		
	    		<br /><br />
	    	</div>
	    </div>
	</div>
</div>
<?php echo $this->Javascript->codeBlock('
	$(function() { 
		setup_mascaras(); setup_time();
	});
		
	$(document).ready(function() {
		$("#PropostaCredenciamentoEtapa2Form").on("submit", function() {
	    	$("#PropostaCredenciamentoEtapa2Form select").prop("disabled", false);
		});
		$(".servicoDiferenciado").hide();
		if( $("#HorarioHorarioAtendimentoDiferenciado0").is(\':checked\') ){
			$(".servicoDiferenciado").hide();
		}
		if( $("#HorarioHorarioAtendimentoDiferenciado1").is(\':checked\') ){
			$(".servicoDiferenciado").show();
		}
		$("input:radio[id=\'HorarioHorarioAtendimentoDiferenciado1\']").click(function() {
			$(".servicoDiferenciado").show();
    	});
	    $("input:radio[id=\'HorarioHorarioAtendimentoDiferenciado0\']").click(function() {
	    	$(".servicoDiferenciado").hide();
	    });
	});
	
	function removeExame(element, codigo, codigo_proposta) {
		$(element).parents("div.input-group").fadeOut( 2500, "linear" ).remove();
		
		$.ajax({
	        type: "POST",
	        url: "/portal/propostas_credenciamento/remove_exame/",
	        dataType: "json",
	        data: "codigo=" + codigo + "&codigo_proposta=" + codigo_proposta,
	        beforeSend: function() { },
	        
	        success: function(result) {
	        		
	        },
	        complete: function() { }
	    });		
	}	
'); ?>
<?php echo $this->Buonny->link_js('proposta_credenciamento'); ?>
