<style>
	legend {font-size: 13px; margin-bottom: 0;}
	.control-group {padding:0; margin: 0}
	.nav-tabs > .active { background: 3E75A5; }
	.radio input[type='radio'], .checkbox input[type='checkbox'] { margin: 3px 5px 0 10px; }
	.pendente { background: #EACCCC; color: #A34646; font-weight: bold; text-decoration: blink; border: 2px solid #A34646; }
</style>
<?php if($status != StatusPropostaCred::PRECADASTRO) : //1?>
	<?php echo $this->BForm->create('PropostaCredenciamento', array('type' => 'post', 'url' => array('controller' => 'propostas_credenciamento','action' => 'editar', $this->passedArgs[0]),'onsubmit' => 'bloquear_div_post()')); ?>
		<div class="alert alert-success" id="botao-status" style="display: none; border: 1px solid #468847;">
			<?php echo $this->BForm->hidden('codigo', array('value' => $this->passedArgs[0])); ?>
			<b>Negociação Definida - Solicitar Aceite da Proposta de Credenciamento</b>
			<?php echo $this->BForm->input('PropostaCredenciamento.novo_status', array('class' => 'input-xxlarge', 'label' => '', 'options' => array('13' => '13 - Solicitar Aceite de Proposta de Credenciamento', '10' => 'Reprovar Proposta'))); ?>
			<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		</div>
	<?php echo $this->BForm->end(); ?>
<?php endif; ?>
<?php echo $this->BForm->create('PropostaCredenciamento', array('type' => 'post', 'url' => array('controller' => 'propostas_credenciamento', 'action' => 'editar', $this->passedArgs[0]),'onsubmit' => 'bloquear_div_post()')); ?>
	<?php echo $this->BForm->hidden('codigo', array('value' => $this->passedArgs[0])); ?>
	<?php if($status == StatusPropostaCred::PRECADASTRO) : //1 ?>
		<div class="alert alert-success">
			<h5>AGUARDANDO INTERAÇÃO DO FORNECEDOR</h5>
			<label>Aguardando o fornecedor preencher o restante das informações na proposta.</label>
		</div>
		<div class='form-actions'>
			<?php echo $this->BForm->input('novo_status', array('class' => 'input-xxlarge', 'disabled' => 'disabled', 'label' => false, 'options' => $array_status, 'default' => $status)); ?>
		</div>
	<?php elseif($status == StatusPropostaCred::TERMO_RECUSADO) : //11?>
		<div class="alert alert-danger">
			<h5 style="color: #888;">FORNECEDOR REPROVOU A PROPOSTA DE CREDENCIAMENTO</h5>
		<?php if(isset($motivos_recusa[$this->data['PropostaCredenciamento']['codigo_motivo_recusa']])) : ?>
			<b>MOTIVO DA RECUSA: </b> - <?php echo $motivos_recusa[$this->data['PropostaCredenciamento']['codigo_motivo_recusa']]; ?>
		<?php endif; ?>
		</div>
		<div class='form-actions'>
			<?php echo $this->BForm->input('novo_status', array('class' => 'input-xxlarge', 'disabled' => 'disabled', 'label' => false, 'options' => $array_status, 'default' => $status)); ?>
		</div>				
	<?php elseif($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES) : //2?>
		<div class="alert alert-danger" id="msg-analisar-valores-servicos">
			<h5 style="color: #A34646;">FAVOR ANALISAR OS VALORES DOS SERVIÇOS!</h5>
		</div>
		<div class='form-actions'>
			<?php echo $this->BForm->input('novo_status', array('class' => 'input-xxlarge', 'disabled' => 'disabled', 'label' => false, 'options' => $array_status, 'default' => $status)); ?>
		</div>
	<?php elseif($status == StatusPropostaCred::RENEGOCIAR_VALOR_MINIMO) : //16?>
		<div class="alert alert-danger" id="msg-analisar-valores-servicos">
			<h5 style="color: #A34646;">VALORES DE EXAMES PENDENTES DE RENEGOCIAÇÃO DO VALOR MÍNIMO!</h5>
		</div>
	<?php elseif($status == StatusPropostaCred::VALOR_MINIMO_NEGOCIADO) : //17?>
		<div class="alert alert-info" id="msg-analisar-valores-servicos">
			<h5 style="color: #0F4F6D;">VALOR MÍNIMO DE EXAMES RENEGOCIADOS NECESSITANDO AVALIAÇÃO DA CHEFIA!</h5>
		</div>
		<div class='form-actions'>
			<?php echo $this->BForm->input('novo_status', array('class' => 'input-xxlarge', 'disabled' => 'disabled', 'label' => false, 'options' => $array_status, 'default' => $status)); ?>
		</div>				
	<?php elseif($status == StatusPropostaCred::AGUARDANDO_AVALIACAO_CONTRA_PROPOSTA) : //3?>
		<div class="alert alert-success">
			<h5>AGUARDANDO INTERAÇÃO DO FORNECEDOR</h5>
			<label>Aguardando a análise da contra proposta enviada.</label>
		</div>
		<div class='form-actions'>
			<?php echo $this->BForm->input('novo_status', array('class' => 'input-xxlarge', 'disabled' => 'disabled', 'label' => false, 'options' => $array_status, 'default' => $status)); ?>
		</div>		
	<?php elseif($status == StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA) : //4?>
		<div class="alert alert-danger" id="msg-exames-sem-retorno" style="border: 3px solid #a34646;">
			<h5 style="color: #a34646;">EXISTEM EXAMES COM RETORNO PARA CONTRA PROPOSTA ENVIADA!</h5>
		</div>
		<div class='form-actions'>
			<?php echo $this->BForm->input('novo_status', array('class' => 'input-xxlarge', 'disabled' => 'disabled', 'label' => false, 'options' => $array_status, 'default' => $status)); ?>
		</div>				
	<?php elseif($status == StatusPropostaCred::VALORES_APROVADOS) : //13?>
		<div class="alert alert-success">
			<h5>AGUARDANDO INTERAÇÃO DO FORNECEDOR</h5>
			<label>Aguardando Aceitação da Proposta de Credenciamento.</label>
		</div>
		<div class='form-actions'>
			<?php echo $this->BForm->input('novo_status', array('class' => 'input-xxlarge', 'disabled' => 'disabled', 'label' => false, 'options' => $array_status, 'default' => $status)); ?>
		</div>	
	<?php elseif($status == StatusPropostaCred::AGUARDANDO_ENVIO_TERMO) : //13?>
		<div class="alert alert-success">
			<h5 style="color: #888;">AGUARDANDO ENVIO DO TERMO DA PROPOSTA DE CREDENCIAMENTO</h5>
			<label>Aguardando o envio da proposta de credenciamento digitalizada.</label>
		</div>
	<?php elseif($status == StatusPropostaCred::PROPOSTA_ACEITA) : //6?>
		<div class="alert alert-danger">
			<h5 style="color: #AA5858;">PROPOSTA DE CREDENCIAMENTO ENVIADO PELO FORNECEDOR!</h5>
			(Acessar a documentação e validar a proposta de credenciamento, após validar solicitar restante da documentação para gerar o contrato de parceria.)
		</div>
		<div class="form-actions" id="solicita_documento" style="display: none;">
			<?php if( ($qtd_documentos == $qtd_enviados) && ($qtd_enviados != 0) ) : ?>
				<?php echo $this->BForm->input('novo_status', array('class' => 'input-xxlarge', 'label' => false, 'options' => array('7' => '7 - Aprovar Fornecedor', '10' => '10 - Reprovar Fornecedor'))); ?>
			<?php elseif( $qtd_documentos >= $qtd_enviados ) : ?>
				<?php echo $this->BForm->input('novo_status', array('class' => 'input-xxlarge', 'label' => false, 'options' => array('7' => '7 - Solicitar o Restante da Documentação', '10' => '10 - Reprovar Proposta'))); ?>
			<?php else : ?>
				<?php echo $this->BForm->input('novo_status', array('class' => 'input-xxlarge', 'label' => false, 'options' => array('9' => '9 - Aprovar Proposta', '10' => '10 - Reprovar Proposta'))); ?>
			<?php endif; ?>
			
			<?php echo $this->BForm->button('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		</div>
	<?php elseif($status == StatusPropostaCred::DOCUMENTACAO_SOLICITADA) : //7?>
		<div class='form-actions'>
			<?php echo $this->BForm->input('novo_status', array('class' => 'input-xxlarge', 'disabled' => 'disabled', 'label' => false, 'options' => $array_status, 'default' => $status)); ?>
			<label>Aguardando envio da documentação.</label>
		</div>
	<?php elseif($status == StatusPropostaCred::AGUARDANDO_ANALISE_DOCUMENTOS) : //8?>
		<div class="alert alert-success">
			<h5 style="color: #888;">TODOS OS DOCUMENTOS OBRIGATÓRIOS JÁ FORAM ENVIADOS!</h5>
			(Ao aprovar a proposta, vai gerar o contrato e será enviado um e-mail para o fornecedor baixar o contrato e enviar assinado.)
		</div>			
		
		<div class="form-actions" id="solicita_contrato" style="display: none;">
			<?php echo $this->BForm->input('novo_status', array('class' => 'input-xxlarge', 'label' => false, 'options' => array('9' => '9 - Aprovar e Gerar Contrato', '10' => '10 - Reprovado'))); ?>
			<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		</div>
	<?php elseif($status == StatusPropostaCred::APROVADO) : //9?>
		<div class="alert alert-success">
			<h5 style="color: #888;">PROPOSTA APROVADA!!!</h5>
			(Aguardando envio do contrato assinado)
		</div>
		<div class='form-actions'>
			<a class="btn btn-success" href="/portal/propostas_credenciamento/contrato/<?php echo $this->data['PropostaCredenciamento']['codigo']; ?>" target="_blank">Imprimir Contrato</a>
			(Visualisar o contrato gerado)
		</div>				
	<?php elseif($status == StatusPropostaCred::REPROVADO) : //10?>
		<div class="alert alert-danger" style="border: 1px solid #DD5447;">
			<h5 style="color: #DD5447;">PROPOSTA REPROVADA!!!</h5>
		</div>
	<?php elseif($status == StatusPropostaCred::AGUARDANDO_ANALISE_PROPOSTA) : //12?>
		<div class="alert alert-alert">
			<h5 style="color: #AA5858;">ANALISAR FORNECEDOR DE SEGURANÇA E APROVAR/REPROVAR SEUS SERVIÇOS!</h5>
		</div>		
		<!-- 
		<div class='form-actions'>
			<?php echo $this->BForm->input('novo_status', array('class' => 'input-xxlarge', 'label' => false, 'options' => array('13' => '13 - Solicitar - Aceite de Proposta de Credenciamento', '10' => '10 - Reprovado'))); ?>
			<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
		</div>
		 -->
	<?php endif; ?>	
<?php
	$array_libera_reenvio_senha = array(
		StatusPropostaCred::AGUARDANDO_AVALIACAO_CONTRA_PROPOSTA => 1,
		StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA => 1,
		StatusPropostaCred::VALORES_APROVADOS => '1',
		StatusPropostaCred::PROPOSTA_ACEITA => '1',
		StatusPropostaCred::DOCUMENTACAO_SOLICITADA => '1',
		StatusPropostaCred::AGUARDANDO_ANALISE_DOCUMENTOS => '1',
		StatusPropostaCred::APROVADO => '1',
		StatusPropostaCred::TERMO_RECUSADO => '1',
		StatusPropostaCred::AGUARDANDO_ENVIO_TERMO => '1',
		StatusPropostaCred::CONTRATO_ASSINADO_ENVIADO => '1'
	);
	
	$array_libera_solicita_renegociacao_valor_minimo = array(
		StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA => 1,
		StatusPropostaCred::VALOR_MINIMO_NEGOCIADO => 1
	);
	
	$array_nao_mostra_botao_inativar = array( StatusPropostaCred::REPROVADO => 1, StatusPropostaCred::TERMO_RECUSADO => 1 );
?>
<div class="form-actions" style="text-align: right;">
	<?php if(!array_key_exists($status, $array_nao_mostra_botao_inativar)) : ?>
		<a href="javascript:void(0);" onclick="manipula_modal('modal_inativar', 1);" class="btn btn-danger">INATIVAR A PROPOSTA</a>
	<?php else : ?>
		<a href="javascript:void(0);" onclick="manipula_modal('modal_ativar', 1);" class="btn btn-success">ATIVAR PROPOSTA</a>
	<?php endif; ?>
	
	<?php if($status == StatusPropostaCred::PRECADASTRO) : //1?>
		<a href="javascript:void(0);" onclick="manipula_modal('modal_pre_cadastro', 1);" class="btn btn-info">REENVIAR E-MAIL PRÉ-CADASTRO</a>
	<?php endif; ?>
	
	<?php if(array_key_exists($status, $array_libera_reenvio_senha)) : ?>
		<a href="javascript:void(0);" onclick="manipula_modal('modal_senha', 1);" class="btn btn-info">REENVIAR SENHA</a>
	<?php endif; ?> 	
</div>
<ul class="nav nav-tabs">
	<li id="aba-dados_proposta" class="<?php echo (($aba == 'dados_proposta') || is_null($aba)) ? 'active' : '' ?>"><a href="#dados_proposta" data-toggle="tab">Dados da Proposta</a></li>
	<?php if(isset($tipos_produto['59']) && !empty($tipos_produto['59'])) : ?>
		<li id="aba-dados_exames" class="<?php echo ($aba == 'dados_exames') ? 'active' : '' ?>"><a href="#dados_exames" data-toggle="tab" class="<?php echo (($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES) || ($status == StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA)) ? 'pendente' : ''; ?>">Valores de Exames</a></li>
	<?php endif; ?>
	<?php if(isset($tipos_produto['60']) && !empty($tipos_produto['60'])) : ?>
		<li id="aba-dados_engenharias" class="<?php echo ($aba == 'dados_engenharias') ? 'active' : '' ?>"><a href="#dados_engenharias" data-toggle="tab" class="<?php echo (($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES) || ($status == StatusPropostaCred::AGUARDANDO_ANALISE_PROPOSTA)) ? 'pendente' : ''; ?>">Serviços de Engenharia</a></li>
    <?php endif; ?>
	<li id="aba-dados_documentacao" class="<?php echo ($aba == 'dados_documentacao') ? 'active' : '' ?>"><a href="#dados_documentacao" data-toggle="tab" class="<?php echo (($status == StatusPropostaCred::AGUARDANDO_ANALISE_DOCUMENTOS) || ($status == StatusPropostaCred::PROPOSTA_ACEITA)) ? 'pendente' : ''; ?>">Documentação</a></li>
	<li id="aba-dados_historico" class="<?php echo ($aba == 'dados_historico') ? 'active' : '' ?>"><a href="#dados_historico" data-toggle="tab">Histórico de Status</a></li>
	<li id="aba-dados_fotos" class="<?php echo ($aba == 'dados_fotos') ? 'active' : '' ?>"><a href="#dados_fotos" data-toggle="tab">Fotos do Estabelecimento</a></li>
</ul>
   
<div class="tab-content">
    <div class="tab-pane <?php echo (($aba == 'dados_proposta') || is_null($aba)) ? 'active' : '' ?>" id="dados_proposta">
    <?php echo $this->BForm->create('PropostaCredenciamento', array('type' => 'post', 'url' => array('controller' => 'propostas_credenciamento', 'action' => 'editar', $this->passedArgs[0]))); ?>
    	<?php echo $this->BForm->hidden('PropostaCredenciamento.acao', array('value' => 'atualiza_dados')); ?>
    	<?php echo $this->BForm->hidden('PropostaCredenciamento.codigo', array('value' => $this->passedArgs[0])); ?>
    	<?php echo $this->BForm->hidden('PropostaCredenciamento.codigo_status_proposta_credenciamento', array('value' => $this->data['PropostaCredenciamento']['codigo_status_proposta_credenciamento'])); ?>
		<div class="row">
			<div class="span6">
				<h3 >Dados da Empresa:</h3>
			   	<?php echo $this->BForm->input('PropostaCredenciamento.razao_social', array('class' => 'form-control', 'label' => 'Razão Social:', 'style' => 'width: 500px;', 'data-required' => true)); ?>
			    <?php echo $this->BForm->input('PropostaCredenciamento.nome_fantasia', array('class' => 'form-control', 'label' => 'Nome Fantasia:', 'style' => 'width: 500px;')); ?>
			    <?php echo $this->BForm->input('PropostaCredenciamento.codigo_documento', array('class' => 'form-control cnpj', 'label' => 'CNPJ:', 'style' => 'width: 200px;')); ?>
				<hr />
				<?php 
				if(isset($this->data['PropostaCredEndereco']) && is_array($this->data['PropostaCredEndereco'])){
					foreach($this->data['PropostaCredEndereco'] as $key => $endereco) : ?>
						<?php echo $this->BForm->hidden('PropostaCredEndereco.'.$key.'.codigo', array('value' => $endereco['codigo'])); ?>
						
						<?php if($endereco['matriz']) : ?>
							<h3 >Endereço Matriz:</h3>
						<?php else : ?>
							<h3 >Endereço Filial:</h3>
						<?php endif; ?>
						
						<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.cep', array('value' => $endereco['cep'], 'class' => 'form-control', 'label' => 'Cep:', 'style' => 'width: 200px;')); ?>
						<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.logradouro', array('value' => $endereco['logradouro'], 'class' => 'form-control', 'label' => 'Logradouro:', 'style' => 'width: 500px;')); ?>
						<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.numero', array('value' => $endereco['numero'], 'class' => 'form-control', 'label' => 'Número:', 'style' => 'width: 200px;')); ?>
						<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.complemento', array('value' => $endereco['complemento'], 'class' => 'form-control', 'label' => 'Complemento:', 'style' => 'width: 500px;')); ?>
						<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.bairro', array('value' => $endereco['bairro'], 'class' => 'form-control', 'label' => 'Bairro:', 'style' => 'width: 500px;')); ?>
						<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.estado', array('value' => $endereco['estado'], 'class' => 'form-control', 'label' => 'Estado:', 'style' => 'width: 300px;', 'options' => $estados, 'onchange' => 'proposta.buscaCidade(this, null, null, "PropostaCredEndereco'.$key.'CodigoCidadeEndereco", null, null, '.$key.')')); ?>
						<?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.cidade', array('value' => $endereco['cidade'], 'class' => 'form-control', 'label' => 'Cidade:', 'style' => 'width: 300px;')); ?>
						<div id="carregando_cidade_<?php echo $key; ?>" style="display: none; border: 1px solid #CCCCCC; padding: 8px;">
							<img src="/portal/img/ajax-loader.gif" border="0"/>
						</div>
					<?php 
					endforeach; 
				} ?>
				
				<hr />
				<h3 >Informações Bancárias:</h3>
				<?php echo $this->BForm->input('PropostaCredenciamento.melhor_dia_pagto', array('class' => 'form-control', 'label' => 'Dia de recebimento:', 'style' => 'width: 200px;', 'options' => $dias)); ?>
				<?php echo $this->BForm->input('PropostaCredenciamento.cobranca_boleto', array('div' => true, 'legend' => 'Como prefere receber?', 'options' => array('0' => 'Depósito em Conta', '1' => 'Vou gerar Boleto'), 'type' => 'radio', 'onchange' => 'proposta.mostraPagto(this);')) ?>
					
				<span id="pagto_deposito" style="display: <?php echo (isset($this->data['PropostaCredenciamento']['cobranca_boleto']) && $this->data['PropostaCredenciamento']['cobranca_boleto'] == '1') ? 'none' : ''; ?>;">
				   	<?php echo $this->BForm->input('PropostaCredenciamento.numero_banco', array('class' => 'form-control', 'label' => 'Banco:', 'style' => 'width: 500px;', 'options' => $bancos)); ?>
					<?php echo $this->BForm->input('PropostaCredenciamento.tipo_conta', array('div' => true, 'label' => 'Tipo de Conta:', 'options' => array('1' => 'Conta Corrente', '0' => 'Conta Poupança'), 'type' => 'radio')) ?>
			    	<?php echo $this->BForm->input('PropostaCredenciamento.agencia', array('class' => 'form-control', 'label' => 'Agência:', 'style' => 'width: 500px;')); ?>
			    	<?php echo $this->BForm->input('PropostaCredenciamento.numero_conta', array('class' => 'form-control', 'label' => 'Número de Conta:', 'style' => 'width: 500px;')); ?>
			    	<?php echo $this->BForm->input('PropostaCredenciamento.favorecido', array('class' => 'form-control', 'label' => 'Favorecido:', 'style' => 'width: 500px;')); ?>				
				</span>	   	
					
			</div>
			
			<div class="span6">
				<div class="form-group clear">
					<h3 >Tipo de Serviço Prestado:</h3>
					<span style="padding: 10px;"><?php echo $this->BForm->input('PropostaCredProduto.60', array('type'=>'checkbox', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'disabled' => 'disabled', 'class' => 'input-xlarge', 'value' => '1', 'checked' => (isset($tipos_produto['60']) && !empty($tipos_produto['60']) ? '"checked"' : ''))); ?> Serviços de Engenharia</span>
					<span style="padding: 10px;"><?php echo $this->BForm->input('PropostaCredProduto.59', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'disabled' => 'disabled', 'class' => 'input-xlarge', 'value' => '1', 'checked' => (isset($tipos_produto['59']) && !empty($tipos_produto['59']) ? '"checked"' : ''))); ?> Exames de Saúde</span>
				</div>
				<hr />
				<?php if(isset($tipos_produto[59])) : ?>
					<div class="form-group clear">
						<h3 >Corpo Clínico: <span style="font-size: 16px;">(Profissionais que realizam exames clínicos)</span></h3>
						
						<?php if(count($medicos)) : ?>
							<table>
								<?php foreach( $medicos as $key => $medico ): ?>
									<tr>
										<td>
											<?php echo $this->BForm->hidden('Medico.'.$key.'.codigo', array('value' => $medico['medico']['codigo'])); ?>
											
											<?php echo $this->BForm->input('Medico.' . $key . '.nome', array('value' => $medico['medico']['nome'], 'class' => 'form-control', 'label' => 'Nome:', 'style' => 'float: left; width: 250px;')); ?>
										</td>
										<td><?php echo $this->BForm->input('Medico.' . $key . '.codigo_conselho_profissional', array('value' => $medico['medico']['codigo_conselho_profissional'], 'class' => 'form-control', 'label' => 'Conselho:', 'style' => 'float: left; width: 100px;','empty' => false, 'options' => $list_conselhos)); ?></td>
						   				<td><?php echo $this->BForm->input('Medico.' . $key . '.numero_conselho', array('value' => $medico['medico']['numero_conselho'], 'class' => 'form-control', 'label' => 'Número:', 'style' => 'float: left; width: 100px;')); ?></td>
						   				<td><?php echo $this->BForm->input('Medico.' . $key . '.conselho_uf', array('value' => $medico['medico']['conselho_uf'], 'class' => 'form-control', 'label' => 'UF:', 'style' => 'float: left; width: 60px;', 'empty' => false, 'options' => $estados_medico)); ?></td>
						   			</tr>
								<?php endforeach; ?>
							</table>						
						<?php else : ?>
							<p class="alert alert-warning">- Ainda não foi definido...</p>
						<?php endif; ?>
					</div>
					<hr />
				<?php endif; ?>
				
				<?php if(isset($tipos_produto[59])) : ?>
					<h3 >Horário de Atendimento:</h3>
					<?php if(count($horarios)) : ?>
						<?php foreach( $horarios as $key => $horario ): ?>
							<table class="table table-striped">
							 	<thead class="thead-inverse">
									<tr>
										<td colspan="2">
											<?php echo $this->BForm->hidden('Horario.'.$key.'.codigo', array('value' => $horario['Horario']['codigo'])); ?>
											
											<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.seg', array('type'=>'checkbox', 'checked' => (strpos($horario['Horario']['dias_semana'], 'seg') !== false ? 'checked' : ''), 'label' => false, 'div' => false, 'multiple'=>'checkbox')); ?> Seg.
											<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.ter', array('type'=>'checkbox', 'checked' => (strpos($horario['Horario']['dias_semana'], 'ter') !== false ? 'checked' : ''), 'label' => false, 'div' => false, 'multiple'=>'checkbox')); ?> Ter.
											<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.qua', array('type'=>'checkbox', 'checked' => (strpos($horario['Horario']['dias_semana'], 'qua') !== false ? 'checked' : ''), 'label' => false, 'div' => false, 'multiple'=>'checkbox')); ?> Qua.
											<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.qui', array('type'=>'checkbox', 'checked' => (strpos($horario['Horario']['dias_semana'], 'qui') !== false ? 'checked' : ''), 'label' => false, 'div' => false, 'multiple'=>'checkbox')); ?> Qui.
											<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.sex', array('type'=>'checkbox', 'checked' => (strpos($horario['Horario']['dias_semana'], 'sex') !== false ? 'checked' : ''), 'label' => false, 'div' => false, 'multiple'=>'checkbox')); ?> Sex.
											<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.sab', array('type'=>'checkbox', 'checked' => (strpos($horario['Horario']['dias_semana'], 'sab') !== false ? 'checked' : ''), 'label' => false, 'div' => false, 'multiple'=>'checkbox')); ?> Sab.
											<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.dom', array('type'=>'checkbox', 'checked' => (strpos($horario['Horario']['dias_semana'], 'dom') !== false ? 'checked' : ''), 'label' => false, 'div' => false, 'multiple'=>'checkbox')); ?> Dom.						
										</td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>
							    			<label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> DE </label>
							    			<?php echo $this->BForm->input('Horario.de_hora][]', array('value' => sprintf("%04s", $horario['Horario']['de_hora']), 'label' => false, 'div' => false, 'class' => 'form-control hora', 'style' => 'width: 60px; font-size: 11px;', 'empty' => false, 'maxlenght' => '5')) ?>
							    		</td>
							    		<td>	
							    			<label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;">  ATÉ </label>
							    			<?php echo $this->BForm->input('Horario.ate_hora][]', array('value' => sprintf("%04s", $horario['Horario']['ate_hora']), 'label' => false, 'div' => false, 'class' => 'form-control  hora', 'style' => 'width: 60px; font-size: 11px;', 'empty' => false, 'maxlenght' => '5')) ?>
										</td>
									</tr>				
								</tbody>
							</table>
						<?php endforeach; ?>					
					<?php else : ?>
						<p class="alert alert-warning">- Ainda não foi definido...</p>
					<?php endif; ?>
					
					<hr />
					
					<div class="row-fluid inline">
						<?php echo $this->BForm->input('Horario.horario_atendimento_diferenciado', array('value' => empty($horarios[0]['Horario']['horario_atendimento_diferenciado']) ? 0 : $horarios[0]['Horario']['horario_atendimento_diferenciado'], 'legend' => false, 'options' => array('1' => 'Sim', '0' => 'Não'), 'type' => 'radio','before' => '<div class="fornecedor_radio_checkbox js-horario"><span style="width:350px;">Algum exame possui horário de atendimento diferenciado?</span>','after' => '</div>', 'hiddenField' => false));?>
					</div><br />
					<?php if(isset($horario_diferenciado['HorarioDiferenciado'])) : ?>
						<?php //echo $this->BForm->hidden('hd_auxiliar', array('value' => '1')); ?>
						<h5 id="tituloHorario_edit" >(Dias e Horários) de Atendimento diferenciado: </h5>
						<?php foreach($horario_diferenciado['HorarioDiferenciado'] as $key => $horario_dif) : ?>
							<div id="periodos_horario_diferenciado_edit" >
								<table id="horarioDif_<?php echo $key; ?>" class="table table-striped periodo">
									<thead class="thead-inverse">
										<tr>
											<td id="dias_semana" colspan="4">
												<?php echo $this->BForm->hidden('HorarioDiferenciado.'.$key.'.codigo_proposta_credenciamento', array('value' => $this->data['PropostaCredenciamento']['codigo'])); ?>
												<?php echo $this->BForm->hidden('HorarioDiferenciado.'.$key.'.codigo_horario', array('value' => $horario['Horario']['codigo'])); ?>
												<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.codigo_servico', 
		                                        array(
		                                            'options' => $exames_credenciado_combo, 
		                                            'empty' => 'Selecione o Exame',
		                                            'value' => $horario_dif['codigo_servico'],
		                                            'label' => 'Exames', 
		                                            'class' => 'js-uni', 
		                                            'style' => 'width: 77%; margin-bottom: 0; margin-top: -6px', 
		                                            'div' => 'control-group input text width-full padding-left-10', 
		                                            'required' => false)); 
		                                    	?>
											</td>
											<td>
												<?php if($key > 0) : ?>
													<?php echo $this->Html->link('<i class="icon-minus icon-minus"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-danger', 'title' =>'Incluir', 'onclick' => "$(this).parents('.periodo').remove();")); ?>
												<?php else : ?>
													<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-warning', 'title' =>'Incluir', 'onclick' => "proposta.addHorarioEdit(); jQuery('.hora').mask('99:99');")); ?>
												<?php endif; ?>										
											</td>
										</tr>
										<tr>
											<td colspan="4">
												<span>Dias da semana:</span>
												<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.dias_semana.seg', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge','checked' => (strpos($horario_dif['dias_semana'], 'seg') !== false ? 'checked' : ''))); ?> Seg.
												<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.dias_semana.ter', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge','checked' => (strpos($horario_dif['dias_semana'], 'ter') !== false ? 'checked' : ''))); ?> Ter.
												<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.dias_semana.qua', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge','checked' => (strpos($horario_dif['dias_semana'], 'qua') !== false ? 'checked' : ''))); ?> Qua.
												<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.dias_semana.qui', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge','checked' => (strpos($horario_dif['dias_semana'], 'qui') !== false ? 'checked' : ''))); ?> Qui.
												<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.dias_semana.sex', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge','checked' => (strpos($horario_dif['dias_semana'], 'sex') !== false ? 'checked' : ''))); ?> Sex.
												<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.dias_semana.sab', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge','checked' => (strpos($horario_dif['dias_semana'], 'sab') !== false ? 'checked' : ''))); ?> Sab.
												<?php echo $this->BForm->input('HorarioDiferenciado.'.$key.'.dias_semana.dom', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge','checked' => (strpos($horario_dif['dias_semana'], 'dom') !== false ? 'checked' : ''))); ?> Dom.
											</td>
											<td></td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>
												<label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> DE </label>
									    		<?php echo $this->BForm->input('HorarioDiferenciado.'.($key).'.de_hora', array('class' => 'form-control hora', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'value' => sprintf("%04s", $horario_dif['de_hora']))); ?>
									    	</td>
									    	<td>
								    			<label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> ATÉ </label>
								    			<?php echo $this->BForm->input('HorarioDiferenciado.'.($key).'.ate_hora', array('class' => 'form-control hora', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple', 'value' => sprintf("%04s", $horario_dif['ate_hora']))); ?>
									    	</td>
									    	<td></td>
							    			<td></td>
							    			<td></td>								   
										</tr>
									</tbody>
								</table>
							</div>				
						<?php endforeach; ?>
					<?php else : ?>
						<?php //echo $this->BForm->hidden('hd_auxiliar', array('value' => '0')); ?>
						<h5 id="tituloHorario">(Dias e Horários) de Atendimento diferenciado: </h5>
						<div id="periodos_horario_diferenciado">
							<table id="horarioDif_0" class="table table-striped periodo">
								<thead class="thead-inverse">
									<tr>
										<td id="dias_semana" colspan="4">
											<?php echo $this->BForm->hidden('HorarioDiferenciado.0.codigo_proposta_credenciamento', array('value' => $this->data['PropostaCredenciamento']['codigo'])); ?>
												<?php echo $this->BForm->hidden('HorarioDiferenciado.0.codigo_horario', array('value' => $horario['Horario']['codigo'])); ?>
											<?php echo $this->BForm->input('HorarioDiferenciado.0.codigo_servico', 
		                                        array(
		                                            'options' => $exames_credenciado_combo, 
		                                            'empty' => 'Selecione o Exame',
		                                            'label' => 'Exames', 
		                                            'class' => 'js-uni', 
		                                            'style' => 'width: 100%; margin-bottom: 0; margin-top: -6px', 
		                                            'div' => 'control-group input text width-full padding-left-10', 
		                                            'required' => false)); 
		                                    	?>
										</td>
										<td>
											<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-warning', 'title' =>'Incluir', 'onclick' => "proposta.addHorario(); jQuery('.hora').mask('99:99');")); ?>
										</td>
									</tr>
									<tr>
										<td colspan="4">
											<span>Dias da semana: </span>
											<?php echo $this->BForm->input('HorarioDiferenciado.0.dias_semana.seg', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Seg.
											<?php echo $this->BForm->input('HorarioDiferenciado.0.dias_semana.ter', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Ter.
											<?php echo $this->BForm->input('HorarioDiferenciado.0.dias_semana.qua', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qua.
											<?php echo $this->BForm->input('HorarioDiferenciado.0.dias_semana.qui', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qui.
											<?php echo $this->BForm->input('HorarioDiferenciado.0.dias_semana.sex', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sex.
											<?php echo $this->BForm->input('HorarioDiferenciado.0.dias_semana.sab', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sab.
											<?php echo $this->BForm->input('HorarioDiferenciado.0.dias_semana.dom', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Dom.
										</td>
										<td></td>
									</tr>
								</thead>
								<tbody>
									<tr>
					    				<td>
					    					<label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> DE </label>
						    				<?php echo $this->BForm->input('HorarioDiferenciado.0.de_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
						    			</td>
						    			<td>
						    				<label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> ATÉ </label>
						    				<?php echo $this->BForm->input('HorarioDiferenciado.0.ate_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
						    			</td>
						    			<td></td>
						    			<td></td>
						    			<td></td>
									</tr>
								</tbody>
							</table>
						</div>
					<?php endif; ?>
					<hr />
				<?php endif; ?>
				<h3 >Contatos de Funcionamento:</h3>
				<?php if(isset($tipos_produto[59])) : ?>
				    <?php echo $this->BForm->input('PropostaCredenciamento.responsavel_tecnico_nome', array('class' => 'form-control', 'label' => 'Responsável Técnico:', 'style' => 'width: 500px;')); ?>
				    <?php echo $this->BForm->input('PropostaCredenciamento.codigo_conselho_profissional', array('class' => 'form-control', 'label' => 'Conselho Profissional:', 'style' => 'width: 100px;', 'empty' => false, 'options' => $list_conselhos)); ?>
				    <?php echo $this->BForm->input('PropostaCredenciamento.responsavel_tecnico_numero_conselho', array('class' => 'form-control', 'label' => 'Número do Conselho:', 'style' => 'width: 300px;')); ?>
				    <?php echo $this->BForm->input('PropostaCredenciamento.responsavel_tecnico_conselho_uf', array('class' => 'form-control', 'label' => 'UF:', 'style' => 'width: 100px;', 'empty' => false, 'options' => $estados_medico)); ?>
				<?php endif; ?>
			    
				<?php echo $this->BForm->input('PropostaCredenciamento.responsavel_administrativo', array('class' => 'form-control', 'label' => 'Responsável Administrativo:', 'style' => 'width: 500px;')); ?>
			    <?php echo $this->BForm->input('PropostaCredenciamento.telefone', array('class' => 'form-control telefone', 'maxLength' => 14,'label' => 'Telefone:', 'style' => 'width: 500px;')); ?>
			    <?php echo $this->BForm->input('PropostaCredenciamento.fax', array('class' => 'form-control telefone', 'maxLength' => 14, 'label' => 'Fax:', 'style' => 'width: 500px;')); ?>
			    <?php echo $this->BForm->input('PropostaCredenciamento.celular', array('class' => 'form-control telefone', 'maxLength' => 15, 'label' => 'Celular:', 'style' => 'width: 500px;')); ?>
			    <?php echo $this->BForm->input('PropostaCredenciamento.email', array('class' => 'form-control', 'label' => 'E-mail:', 'style' => 'width: 500px;', 'disabled' => 'disabled')); ?>
			    
			    <?php if(isset($tipos_produto[59])) : ?>
				   	<?php echo $this->BForm->input('PropostaCredenciamento.tipo_atendimento', array('div' => true, 'legend' => 'Tipo de Atendimento:', 'options' => array('1' => 'Hora Marcada', '0' => 'Ordem de Chegada'), 'type' => 'radio')) ?>
				   	<?php echo $this->BForm->input('PropostaCredenciamento.exames_local_unico', array('div' => true, 'legend' => 'Todos os Exames são feitos em um único local ?', 'options' => array('1' => 'Sim', '0' => 'Não'), 'type' => 'radio')) ?>				    
			    <?php endif; ?>
			    
			   	<?php echo $this->BForm->input('PropostaCredenciamento.acesso_portal', array('div' => true, 'legend' => 'Possui disponibilidade para utilização do Portal RHHealth (acesso via web):', 'options' => array('1' => 'Sim', '0' => 'Não'), 'type' => 'radio')) ?>
			</div>
		</div>
		<div id="modelos">
			<div id="horario_periodo" style="display:none;">
				<div id="periodos_horario_diferenciado">
					<table id="horarioDif_X" class="table table-striped periodo">
						<thead class="thead-inverse">
							<tr>
								<td id="dias_semana" colspan="4">
									<?php echo $this->BForm->hidden('HorarioDiferenciado.X.codigo_proposta_credenciamento', array('value' => $this->data['PropostaCredenciamento']['codigo'])); ?>
									<?php echo $this->BForm->hidden('HorarioDiferenciado.X.codigo_horario', array('value' => $horario['Horario']['codigo'])); ?>
									<?php echo $this->BForm->input('HorarioDiferenciado.X.codigo_servico', 
	                                    array(
	                                        'options' => $exames_credenciado_combo, 
	                                        'empty' => 'Selecione o Exame',
	                                        'label' => 'Exames', 
	                                        'class' => 'js-uni', 
	                                        'style' => 'width: 100%; margin-bottom: 0; margin-top: -6px', 
	                                        'div' => 'control-group input text width-full padding-left-10', 
	                                        'required' => false)); 
	                                	?>
								</td>
								<td>
									<?php echo $this->Html->link('<i class="icon-minus icon-minus"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-danger', 'title' =>'Remover', 'onclick' => "$(this).parents('.periodo').remove();")); ?>
								</td>
							</tr>
							<tr>
								<td colspan="4">
									<span>Dias da semana: </span>
									<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.seg', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Seg.
									<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.ter', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Ter.
									<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.qua', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qua.
									<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.qui', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qui.
									<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.sex', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sex.
									<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.sab', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sab.
									<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.dom', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Dom.
								</td>
								<td></td>
							</tr>
						</thead>
						<tbody>
							<tr>
			    				<td>
			    					<label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> DE </label>
				    				<?php echo $this->BForm->input('HorarioDiferenciado.X.de_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
				    			</td>
				    			<td>
				    				<label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> ATÉ </label>
				    				<?php echo $this->BForm->input('HorarioDiferenciado.X.ate_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
				    			</td>
				    			<td></td>
				    			<td></td>
				    			<td></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div id="horario_periodo_edit" style="display:none;">
				<div id="periodos_horario_diferenciado_edit">
					<table id="horarioDif_X" class="table table-striped periodo">
						<thead class="thead-inverse">
							<tr>
								<td id="dias_semana" colspan="4">
									<?php echo $this->BForm->hidden('HorarioDiferenciado.X.codigo_proposta_credenciamento', array('value' => $this->data['PropostaCredenciamento']['codigo'])); ?>
									<?php echo $this->BForm->hidden('HorarioDiferenciado.X.codigo_horario', array('value' => $horario['Horario']['codigo'])); ?>
									<?php echo $this->BForm->input('HorarioDiferenciado.X.codigo_servico', 
	                                    array(
	                                        'options' => $exames_credenciado_combo, 
	                                        'empty' => 'Selecione o Exame',
	                                        'label' => 'Exames', 
	                                        'class' => 'js-uni', 
	                                        'style' => 'width: 100%; margin-bottom: 0; margin-top: -6px', 
	                                        'div' => 'control-group input text width-full padding-left-10', 
	                                        'required' => false)); 
	                                	?>
								</td>
								<td>
									<?php echo $this->Html->link('<i class="icon-minus icon-minus"></i>', 'javascript:void(0)', array('escape' => false, 'class' => 'btn btn-danger', 'title' =>'Remover', 'onclick' => "$(this).parents('.periodo').remove();")); ?>
								</td>
							</tr>
							<tr>
								<td colspan="4">
									<span>Dias da semana: </span>
									<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.seg', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Seg.
									<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.ter', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Ter.
									<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.qua', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qua.
									<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.qui', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Qui.
									<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.sex', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sex.
									<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.sab', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Sab.
									<?php echo $this->BForm->input('HorarioDiferenciado.X.dias_semana.dom', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'class' => 'input-xlarge')); ?> Dom.
								</td>
								<td></td>
							</tr>
						</thead>
						<tbody>
							<tr>
			    				<td>
			    					<label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> DE </label>
				    				<?php echo $this->BForm->input('HorarioDiferenciado.X.de_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
				    			</td>
				    			<td>
				    				<label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> ATÉ </label>
				    				<?php echo $this->BForm->input('HorarioDiferenciado.X.ate_hora', array('class' => 'hora form-control', 'label' => false, 'style' => 'float: left; width: 80px;', 'multiple')); ?>
				    			</td>
				    			<td></td>
				    			<td></td>
				    			<td></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		
		<div class="row" style="padding: 0 25px 25px 0;">
			<div class="span11 form-actions right">
				<a href="/portal/propostas_credenciamento/" class="btn btn-default btn-lg"><i class="glyphicon glyphicon-fast-backward"></i> Voltar</a>
				<button type=submit class="btn btn-success btn-lg"><i class="glyphicon glyphicon-share"></i> Salvar</button>
			</div>
		</div>		
		
	<?php echo $this->BForm->end(); ?>
    </div>
    
	<div class="tab-pane <?php echo ($aba == 'dados_documentacao') ? 'active' : '' ?>" id="dados_documentacao">
		<?php echo $this->requestAction('/tipos_documentos/listagem/' . $this->data['PropostaCredenciamento']['codigo'], array('return')); ?>	
	</div>
	
	<div class="tab-pane <?php echo ($aba == 'dados_fotos') ? 'active' : '' ?>" id="dados_fotos">
		<h3>Fotos do Estabelecimento</h3>
		
		
			<?php if(count($fotos)) : ?>
				<div class="row">
					<?php foreach($fotos as $key => $foto) : ?>
						<div class="span3">
							<h4><?php echo $foto['PropostaCredFoto']['descricao']; ?></h4>
							<a href="/portal/files/fotos/<?php echo $codigo; ?>/<?php echo $foto['PropostaCredFoto']['caminho_arquivo']; ?>" target="_blank"><img src="/portal/files/fotos/<?php echo $codigo; ?>/<?php echo $foto['PropostaCredFoto']['caminho_arquivo']; ?>" class="thumbnail" alt="<?php echo $foto['PropostaCredFoto']['descricao']; ?>" style="width: 250px;"></a>
						</div>
					<?php endforeach; ?>
				</div>				
			<?php else : ?>
				<p class="alert alert-warning">Ainda nenhuma foto.</p>
			<?php endif; ?>
		
		
	</div>
	
	<div class="tab-pane <?php echo ($aba == 'dados_historico') ? 'active' : '' ?>" id="dados_historico">
		<h3>Histórico de Atualização de Status:</h3>
		
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Data / Hora:</th>
					<th>Status da Proposta:</th>
					<th>Responsável pela Alteração:</th>				
				</tr>
			</thead>
			<tbody>
				<?php foreach($dados_historico as $key => $info_historico) : ?>
					<tr>
						<th><?php echo $info_historico['PropostaCredHistorico']['data_inclusao']; ?></th>
						<th><?php echo $info_historico['StatusPropostaCred']['descricao']; ?></th>
						<th>(<?php echo !empty($info_historico['Usuario']['nome']) ? $info_historico['Usuario']['nome'] : 'USUARIO SEM LOGIN'; ?>)</th>				
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>	
    <div class="tab-pane <?php echo ($aba == 'dados_exames') ? 'active' : '' ?>" id="dados_exames">
		<?php echo $this->BForm->create('PropostaCredExame', array('type' => 'post', 'url' => array('controller' => 'propostas_credenciamento', 'action' => 'editar', $this->passedArgs[0]))); ?>
			<?php echo $this->BForm->hidden('acao', array('value' => 'contra_proposta')); ?>
			<h3 >Relação de Exames:</h3>
			
			<?php if(($status == StatusPropostaCred::PRECADASTRO) || ($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES)) : ?>
				<a href="javascript:void(0);" class="btn btn-info" onclick="$('#modal_tabela_padrao').modal('show'); $('.modal').css('z-index', '1050');">
					<span class="icon-white icon-align-justify"></span>
					Adicionar Novo Exame
				</a>
			<?php endif; ?>
			
			<table>
				<?php $flag_envio_form = false; ?>
				<?php foreach( $exames as $key => $exame ) : ?>
					<tr>
		    			<td><?php echo $this->BForm->input('PropostaCredExame.'.$key.'.codigo_exame', array('value' => $exame['PropostaCredExame']['codigo_exame'], 'class' => 'exames', 'style' => 'display:none', 'label' => false, 'div' => false)); ?>
		    				<?php echo $this->BForm->input('Servico.' . $exame['PropostaCredExame']['codigo'] . '.descricao', array('value' => $exame['Servico']['descricao'], 'class' => 'form-control', 'label' => 'Exame:', 'style' => 'float: left; width: 385px;', 'disabled' => 'disabled')); ?></td>
		    			<?php if($exame['PropostaCredExame']['valor_base']) : ?>
		    				<td><?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor_base', array('value' => $exame['PropostaCredExame']['valor_base'], 'class' => 'form-control moeda', 'label' => 'Máximo: (R$)', 'style' => "float: left; width: 100px; text-align: right; border: 2px solid #000;", 'disabled' => 'disabled')); ?></td>
		    			<?php else : ?>
		    				<td><?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor_base', array('value' => 'SEM VALOR', 'class' => 'form-control', 'label' => 'Máximo: (R$)', 'style' => "border: 2px solid #000;text-align: center; width: 100px;", 'disabled' => 'disabled', 'title' => 'Este produto não esta cadastrado na tabela de preço padrão!')); ?></td>
		    			<?php endif; ?>
		    			<td><?php echo $this->BForm->input('media_' . $key, array('value' => isset($media[$exame['PropostaCredExame']['codigo_exame']]) ? $media[$exame['PropostaCredExame']['codigo_exame']] : '-', 'class' => 'form-control moeda', 'label' => 'Média Cidade:', 'style' => "float: left; width: 100px; text-align: right; border: 2px solid #000;", 'disabled' => 'disabled')); ?></td>
		    			<td><?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor', array('value' => trim($exame['PropostaCredExame']['valor']) ? $exame['PropostaCredExame']['valor'] : '0,00', 'class' => 'form-control moeda', 'label' => 'Valor Proposto:', 'style' => "float: left; width: 100px; text-align: right; {$exame['Style']['valor_1']}", 'disabled' => 'disabled')); ?></td>
		    			
		    			<td>
		    				<?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor_contra_proposta', array('value' => ($exame['PropostaCredExame']['valor_contra_proposta'] ? $exame['PropostaCredExame']['valor_contra_proposta'] : ''), 'class' => 'form-control moeda contra_proposta', 'label' => 'Contra Proposta:', 'style' => "float: left; width: 100px; text-align: right; {$exame['Style']['valor_2']}", 'onblur' => 'verificaContra(this, "'.$exame['PropostaCredExame']['codigo'].'", "'.$exame['PropostaCredExame']['valor'].'")', 'disabled' => is_null($exame['PropostaCredExame']['aceito']) && is_null($exame['PropostaCredExame']['valor_minimo']) && ($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES) ? 'false' : 'true')); ?>
		    			</td>
		    			
		    			<td>
		    				<?php echo $this->BForm->input('PropostaCredExame.' . $exame['PropostaCredExame']['codigo'] . '.valor_minimo', array('value' => (($exame['PropostaCredExame']['valor_minimo'] && $status != StatusPropostaCred::AGUARDANDO_AVALIACAO_CONTRA_PROPOSTA) ? $exame['PropostaCredExame']['valor_minimo'] : '-'), 'class' => 'form-control moeda valor_minimo', 'label' => 'Valor Mínimo:', 'style' => "float: left; width: 100px; text-align: right; {$exame['Style']['valor_3']}", 'disabled' => ((($exame['PropostaCredExame']['aceito'] == '1') || ($status != StatusPropostaCred::RENEGOCIAR_VALOR_MINIMO)) ? 'disabled' : ''))); ?>
		    			</td>
		    			
		    			<td id="exame_<?php echo $exame['PropostaCredExame']['codigo']; ?>" style="text-align: center; padding: 15px 5px 0;">
		    				<?php if($exame['PropostaCredExame']['aceito'] == '1') : ?>
		    				
		    					<?php if(is_null($exame['PropostaCredExame']['valor_contra_proposta'])) : ?>
		    						<a href="javascript:void(0);" class="label label-success"><i class="icon-white icon-ok-sign"></i> Aprovado: <?php echo $exame['Usuario']['nome']; ?></a>
		    						<?php if(($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES)) : ?>
		    							<a href="javascript:void(0);" onclick="volta_status_exame(<?php echo $exame['PropostaCredExame']['codigo']; ?>, <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);">Reverter</a>
		    						<?php endif; ?>
		    					<?php elseif(!is_null($exame['PropostaCredExame']['valor_minimo'])) : ?>
		    						<a href="javascript:void(0);" class="label label-success"><i class="icon-white icon-ok-sign"></i> Aprovado Mínimo: <?php echo $exame['PropostaCredExame']['valor_minimo']; ?></a>
		    						<?php if(($status == StatusPropostaCred::AGUARDANDO_ANALISE_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES) || ($status == StatusPropostaCred::VALOR_MINIMO_NEGOCIADO) ) : ?>
		    							<br />
		    							<a href="javascript:void(0);" onclick="voltar_valida_valor_minimo(<?php echo $exame['PropostaCredExame']['codigo']; ?>, '<?php echo $exame['PropostaCredExame']['valor_minimo']; ?>', <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>)">Reverter</a>
		    						<?php endif; ?>
		    					<?php else : ?>
		    						<a href="javascript:void(0);" class="label label-success"><i class="icon-white icon-ok-sign"></i> Contra Proposta Aprovada: Cliente</a>
		    					<?php endif; ?>
		    					
		    				<?php elseif($exame['PropostaCredExame']['aceito'] == '0') : ?>
		    				
		    					<?php if(is_null($exame['PropostaCredExame']['valor_minimo'])) : ?>
		    						<a href="javascript:void(0);" class="label label-inverse" style="cursor: default;"><i class="icon-white icon-thumbs-down"></i> Reprovado!</a>
		    						<br />
		    						<a href="javascript:void(0);" onclick="volta_status_exame(<?php echo $exame['PropostaCredExame']['codigo']; ?>, <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);">Reverter</a>
		    					<?php else : ?>
		    						<a href="javascript:void(0);" class="label label-inverse" style="cursor: default;"><i class="icon-white icon-remove-sign"></i> Reprovado Mínimo: R$ <?php echo $exame['PropostaCredExame']['valor_minimo']; ?></a>
		    						<br />
		    						<a href="javascript:void(0);" onclick="voltar_valida_valor_minimo(<?php echo $exame['PropostaCredExame']['codigo']; ?>, '<?php echo str_replace(",", ".", $exame['PropostaCredExame']['valor_minimo']); ?>', <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);">Reverter</a>
		    					<?php endif; ?>
		    					
		    				<?php elseif((is_null($exame['PropostaCredExame']['aceito']) && is_null($exame['PropostaCredExame']['valor_contra_proposta'])) && (($status == StatusPropostaCred::AGUARDANDO_ANALISE_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES))) : ?>
		    				
		    					<a href="javascript:void(0);" onclick="aprovar_exame(<?php echo $exame['PropostaCredExame']['codigo']; ?>, '1', <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>)" class="label label-info"><i class="icon-white icon-thumbs-up"></i> Aceitar!</a>
		    					<a href="javascript:void(0);" onclick="aprovar_exame(<?php echo $exame['PropostaCredExame']['codigo']; ?>, '0', <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>)" class="label label-inverse"><i class="icon-white icon-thumbs-up"></i> Reprovar!</a>
		    					
							<?php elseif((isset($exame['PropostaCredExame']['valor_minimo']) && $exame['PropostaCredExame']['valor_minimo'] != "") && (($status == StatusPropostaCred::AGUARDANDO_ANALISE_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES) || ($status == StatusPropostaCred::RENEGOCIAR_VALOR_MINIMO) || ($status == StatusPropostaCred::VALOR_MINIMO_NEGOCIADO))) : ?>
								<?php if(is_null($exame['PropostaCredExame']['aceito']) && ($status != StatusPropostaCred::RENEGOCIAR_VALOR_MINIMO)) : ?>
					    			<a href="javascript:void(0);" class="label label-success" onclick="valida_valor_minimo('<?php echo $exame['PropostaCredExame']['codigo']; ?>', '1', '<?php echo str_replace(",", ".", $exame['PropostaCredExame']['valor_minimo']); ?>', <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);"><i class="icon-white icon-thumbs-up"></i> APROVAR!</a>
					    			<a href="javascript:void(0);" class="label label-important" onclick="valida_valor_minimo('<?php echo $exame['PropostaCredExame']['codigo']; ?>', '0', '<?php echo str_replace(",", ".", $exame['PropostaCredExame']['valor_minimo']); ?>', <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);"><i class="icon-white icon-thumbs-down"></i> REPROVAR!</a>
		    					<?php elseif($exame['PropostaCredExame']['aceito'] == "1") : ?>
		    						<a id="resultado_<?php echo $key; ?>" href="javascript:void(0);" class="label label-success">APROVADO: <?php echo $exame['PropostaCredExame']['valor_minimo']; ?></a>
		    					<?php elseif($exame['PropostaCredExame']['aceito'] == "0") : ?>
		    						<a id="resultado_<?php echo $key; ?>" href="javascript:void(0);" class="label label-inverse">REPROVADO: <?php echo $exame['PropostaCredExame']['valor_minimo']; ?></a>
		    					<?php elseif($exame['PropostaCredExame']['aceito'] == "2") : ?>
					    			<a href="javascript:void(0);" class="label label-danger" onclick="valida_valor_minimo('<?php echo $exame['PropostaCredExame']['codigo']; ?>', '0', $('#PropostaCredExame<?php echo $exame['PropostaCredExame']['codigo']; ?>ValorMinimo').val(), <?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);">X</a>		    						
		    					<?php endif; ?>
		    					
		    				<?php elseif((is_null($exame['PropostaCredExame']['aceito']) && !is_null($exame['PropostaCredExame']['valor_contra_proposta'])) || (($status == StatusPropostaCred::AGUARDANDO_ANALISE_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES))) : ?>
		    					<a href="javascript:void(0);" class="label" style="border: 1px solid #666; padding: 2px; cursor: default; font-size: 12px; font-weight: normal;"><i class="icon-white icon-remove-sign"></i> Não foi Avaliado!</a>								
			    			<?php endif; ?>
			    			
			    			<?php if(is_null($exame['PropostaCredExame']['aceito']) && (($status == StatusPropostaCred::AGUARDANDO_ANALISE_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_RETORNO_CONTRA_PROPOSTA) || ($status == StatusPropostaCred::AGUARDANDO_ANALISE_VALORES))) : ?>
 								<a href="javascript:void(0);" class="label label-alert" title="Remover este exame" onclick="remove_exame(this, '<?php echo $exame['PropostaCredExame']['codigo_exame']; ?>', '<?php echo $this->data['PropostaCredenciamento']['codigo']; ?>');">X</a>
							<?php endif; ?>
										    			
						</td>
		    			<td id="carregando_<?php echo $exame['PropostaCredExame']['codigo']; ?>" style="display: none; text-align: center;">
		    				<img src="/portal/img/hourglass.gif">
		    			</td>
					</tr>
					
					<?php if(!is_null($exame['PropostaCredExame']['valor_minimo'])) : ?>
						<?php $flag_tem_valor_minimo = true; ?>
					<?php endif; ?>
					
					<?php if(!isset($flag_envio_form) || !$flag_envio_form) : ?>
						<?php $flag_envio_form = ((is_null($exame['PropostaCredExame']['valor_contra_proposta']) || ($exame['PropostaCredExame']['valor'] == 0) && is_null($exame['PropostaCredExame']['valor_minimo'])) && is_null($exame['PropostaCredExame']['aceito'])); ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</table>
			<?php if(array_key_exists($status, $array_libera_solicita_renegociacao_valor_minimo)) : ?>
				<div class='form-actions' id="form-actions-solicita-negociacao-minimo" style="display: <?php echo (isset($flag_tem_valor_minimo) && $flag_tem_valor_minimo) ? 'block' : 'none'; ?>;">
					<a href="javascript:void(0);" class="btn btn-inverse" onclick="solicita_atualiza_status_renegocia_valor_minimo(<?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);">Solicitar Negociação dos Valores Mínimos não Aprovados!</a>
					(Solicita que seja renegociados todos os valores mínimos que ainda não estão aprovados!)
				</div>
			<?php endif; ?>			
			<div class='form-actions' id="form-actions-enviar_minimos" style="display: <?php echo isset($status) && $status == StatusPropostaCred::RENEGOCIAR_VALOR_MINIMO ? 'block' : 'none'; ?>;">
				<a href="javascript:void(0);" class="btn btn-success" onclick="enviar_renegociacoes_de_valor_minimo(<?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);" title="Preencher todos os campos de valor mínimo renegociados com o Credenciando!">Enviar Renegociações de Valor Mínimo!</a>
				<a href="javascript:void(0);" class="btn btn-warning" onclick="voltar_status_contra_proposta(<?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);">Voltar para Contra Proposta!</a>
			</div>
			
			<div class='form-actions' id="form-actions" style="display: <?php echo isset($flag_envio_form) && $flag_envio_form ? 'block' : 'none'; ?>;">
				<a href="javascript:void(0);" class="btn btn-success" onclick="verifica_preenchimento_contraproposta(<?php echo $this->data['PropostaCredenciamento']['codigo']; ?>);">Enviar Contra Proposta</a>
				(Preencher os campos de Contra Proposta para cada valor fora da política da RHHealth e Enviar a Contra Proposta!)
			</div>
		<?php echo $this->BForm->end(); ?>
    </div>
    
    <div class="tab-pane <?php echo ($aba == 'dados_engenharias') ? 'active' : '' ?>" id="dados_engenharias">
    	<h3 >Relação de Serviços de Engenharia:</h3>
		<table>
			<?php foreach( $engenharias as $key => $engenharia ) : ?>
				<tr>
					<td>
						<label>Serviço: </label>
						<input type="text" name="Engenharia.<?php echo $key; ?>.descricao" value="<?php echo $engenharia['Servico']['descricao']; ?>" disabled="disabled" class="form-control" style="float: left; width: 585px;">
					</td>
					<td id="engenharia_<?php echo $engenharia['PropostaCredExame']['codigo']; ?>" style="text-align: center; padding: 15px 10px 0;">
					
						<?php if($engenharia['PropostaCredExame']['aceito'] == '1') : ?>
							<a href="javascript:void(0);" class="label label-success" style="cursor: default;"><i class="icon-white icon-ok-sign"></i> Aprovado: <?php echo $engenharia['Usuario']['nome']; ?></a><br />
							<?php if(($status < 5) || $status == 12) : ?>
								<a href="javascript:void(0);" onclick="voltar_status_engenharia('<?php echo $engenharia['PropostaCredExame']['codigo']; ?>', '<?php echo $engenharia['PropostaCredExame']['codigo_proposta_credenciamento']; ?>');" style="cursor: default;">Reverter</a>
							<?php endif; ?>
						<?php elseif($engenharia['PropostaCredExame']['aceito'] == '0') : ?>
							<a href="javascript:void(0);" class="label label-inverse" style="cursor: default;"><i class="icon-white icon-remove-sign"></i> Reprovado: <?php echo $engenharia['Usuario']['nome']; ?></a>
							<?php if(($status < 5) || $status == 12) : ?>
								<br />
								<a href="javascript:void(0);" onclick="voltar_status_engenharia('<?php echo $engenharia['PropostaCredExame']['codigo']; ?>', '<?php echo $engenharia['PropostaCredExame']['codigo_proposta_credenciamento']; ?>');" style="cursor: default;">Reverter</a>
							<?php endif; ?>							
						<?php else : ?>
							<?php if($status != StatusPropostaCred::PRECADASTRO) : ?>
			    				<a href="javascript:void(0);" id="engenharia_<?php echo $engenharia['PropostaCredExame']['codigo']; ?>" onclick="aprovar_engenharia('<?php echo $engenharia['PropostaCredExame']['codigo']; ?>', '1', <?php echo $engenharia['PropostaCredExame']['codigo_proposta_credenciamento']; ?>);" class="label label-info"><i class="icon-white icon-thumbs-up"></i> Aprova!</a>
			    				<a href="javascript:void(0);" id="engenharia_<?php echo $engenharia['PropostaCredExame']['codigo']; ?>" onclick="aprovar_engenharia('<?php echo $engenharia['PropostaCredExame']['codigo']; ?>', '0', <?php echo $engenharia['PropostaCredExame']['codigo_proposta_credenciamento']; ?>);" class="label label-danger"><i class="icon-white icon-thumbs-up"></i> Reprovar!</a>							
							<?php endif; ?>
						<?php endif; ?>
					</td>
	    			<td id="carregando_<?php echo $engenharia['PropostaCredExame']['codigo']; ?>" style="display: none; text-align: center;">
	    				<img src="/portal/img/hourglass.gif">
	    			</td>						
				</tr>
			<?php endforeach; ?>
		</table>
    </div>
</div>
    
<div id="aprovado" style="display:none;">
	<a href="javascript:void(0);" class="label label-success" style="cursor: default; display: block;"><i class="icon-white icon-ok-sign"></i> Aprovado</a>
</div>
<div id="reprovado" style="display:none;">
	<a href="javascript:void(0);" class="label label-inverse" style="cursor: default; display: block;"><i class="icon-white icon-remove-sign"></i> Reprovado</a>
</div>
<div class="modal fade" id="modal_tabela_padrao">
	<div class="modal-dialog modal-md" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Tabela de Preços Padrão:</h4>
				<label><a href="javascript:void(0);" onclick="adicionaExames(<?php echo $this->passedArgs[0]; ?>);" class="btn btn-success btn-sm right" title="Incluir">Incluir na Proposta!</a></label>
				<div class="clear"></div>
			</div>
	    	<div class="modal-body" style="height: 600px; overflow: scroll;">
				<table style="width: 100%" class="table-striped">
					<tr>
						<td class="center" style="width: 110px;"></td>
						<td>Exame</td>
						<td style="text-align: right;">Valor Base</td>
					</tr>			
					<?php foreach($tabela_padrao as $key => $campo) : ?>
						<tr>
							<td style="width: 110px; text-align: center;"><input class="checkbox_exames" type="checkbox" value="<?php echo $campo['codigo']; ?>" name="tabela.<?php echo $key; ?>.exame"></td>
							<td><?php echo utf8_encode(strtoupper($campo['nome'])); ?></td>
							<td style="text-align: right;">R$ <?php echo $campo['valor']; ?></td>
						</tr>				
					<?php endforeach; ?>
				</table>
	    	</div>
	    </div>
	</div>
</div>
<div class="modal fade" id="modal_carregando">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">Aguarde, carregando informações...</h4>
			</div>
	    	<div class="modal-body">
	    		<img src="/portal/img/ajax-loader.gif" style="padding: 10px;">
	    	</div>
	    </div>
	</div>
</div>
<div class="modal fade" id="modal_inativar">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">INATIVAÇÃO DE PROPOSTA</h4>
			</div>
	    	<div class="modal-body">
	    		<p>Para inativar a Proposta de Credenciamento confirme utilizando o botão abaixo.</p>
				<?php echo $this->BForm->create('PropostaCredenciamento', array('type' => 'post', 'url' => array('controller' => 'propostas_credenciamento', 'action' => 'inativar_proposta', $this->passedArgs[0]))); ?>
					<?php echo $this->BForm->hidden('codigo', array('value' => $this->passedArgs[0])); ?>
					<?php echo $this->BForm->hidden('acao', array('value' => 'inativar_proposta')); ?>
					<?php echo $this->BForm->submit('Confirma Inativação da Proposta', array('div' => false, 'class' => 'btn btn-danger')); ?>
				<?php echo $this->BForm->end(); ?>
	    	</div>
	    </div>
	</div>
</div>
<div class="modal fade" id="modal_ativar">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">ATIVAÇÃO DE PROPOSTA</h4>
			</div>
	    	<div class="modal-body">
	    		<p>Para ativar a Proposta de Credenciamento confirme utilizando o botão abaixo.</p>
				<?php echo $this->BForm->create('PropostaCredenciamento', array('type' => 'post', 'url' => array('controller' => 'propostas_credenciamento', 'action' => 'ativar_proposta', $this->passedArgs[0]))); ?>
					<?php echo $this->BForm->hidden('codigo', array('value' => $this->passedArgs[0])); ?>
					<?php echo $this->BForm->hidden('acao', array('value' => 'ativar_proposta')); ?>
					<?php echo $this->BForm->submit('CONFIRMA LIBERAÇÃO', array('div' => false, 'class' => 'btn btn-success')); ?>
				<?php echo $this->BForm->end(); ?>
	    	</div>
	    </div>
	</div>
</div>
<div class="modal fade" id="modal_senha">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">REENVIAR SENHA E/OU ATUALIZAR E-MAIL</h4>
			</div>
	    	<div class="modal-body">
	    		<p>Para enviar uma nova senha para o usuário cadastrado e/ou atualizar o e-mail de cadastro e enviar uma senha. Utilize o formulário abaixo.</p>
				<?php echo $this->BForm->create('PropostaCredenciamento', array('type' => 'post', 'url' => array('controller' => 'propostas_credenciamento', 'action' => 'editar', $this->passedArgs[0]))); ?>
					<?php echo $this->BForm->hidden('acao', array('value' => 'reenviar_senha')); ?>			
					<?php echo $this->BForm->input('PropostaCredenciamento.email_confirmacao', array('value' => $this->data['PropostaCredenciamento']['email'], 'class' => 'form-control', 'label' => false, 'style' => 'width: 350px;')); ?>
					<?php echo $this->BForm->submit('Reenviar Senha', array('div' => false, 'class' => 'btn btn-success')); ?>
				<?php echo $this->BForm->end(); ?>
	    	</div>
	    </div>
	</div>
</div>
<div class="modal fade" id="modal_pre_cadastro">
	<div class="modal-dialog modal-sm" style="position: static;">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="gridSystemModalLabel">REENVIAR E-MAIL DE PRÉ CADASTRO.</h4>
			</div>
	    	<div class="modal-body">
	    		<p>Para reenviar o e-mail com os dados de acesso para completar o cadastramento. <br />Utilize o formulário abaixo.</p>
				<?php echo $this->BForm->create('PropostaCredenciamento', array('type' => 'post', 'url' => array('controller' => 'propostas_credenciamento', 'action' => 'reenviar_proposta', $this->passedArgs[0]))); ?>
					<?php echo $this->BForm->hidden('acao', array('value' => 'reenviar_proposta')); ?>
					<?php echo $this->BForm->input('PropostaCredenciamento.email', array('value' => $this->data['PropostaCredenciamento']['email'], 'class' => 'form-control', 'label' => false, 'style' => 'width: 350px;')); ?>
					<?php echo $this->BForm->submit('Reenviar E-mail', array('div' => false, 'class' => 'btn btn-success')); ?>
				<?php echo $this->BForm->end(); ?>
	    	</div>
	    </div>
	</div>
</div>
<div style="clear:both;"></div>
<?php echo $this->Javascript->codeBlock('
	jQuery(document).ready(function() {
		verifica_STATUS_exames(\''.$this->passedArgs[0].'\', '.$status.');
		setup_mascaras(); setup_time(); $("a[rel=\"popover\"]").popover({ trigger: "hover" });
		
		$(".modal").css("z-index", "-1");
		//js da table exames
		if( $("#HorarioHorarioAtendimentoDiferenciado0").is(\':checked\') ){
			$("#tituloHorario_edit").hide();
			$("#periodos_horario_diferenciado_edit").hide();
			$("#tituloHorario").hide();
			$("#periodos_horario_diferenciado").hide();
			$("#modelos").hide();
			$("#horario_periodo").hide();
		}
		if( $("#HorarioHorarioAtendimentoDiferenciado1").is(\':checked\') ){
			$("#tituloHorario_edit").show();
			$("#periodos_horario_diferenciado_edit").show();
		}
		$("input:radio[id=\'HorarioHorarioAtendimentoDiferenciado1\']").click(function() {
			$("#tituloHorario_edit").show();
			$("#periodos_horario_diferenciado_edit").show();
			$("#tituloHorario").show();
			$("#periodos_horario_diferenciado").show();
    	});
	    $("input:radio[id=\'HorarioHorarioAtendimentoDiferenciado0\']").click(function() {
	        $("#periodos_horario_diferenciado_edit").hide();
			$("#tituloHorario_edit").hide();
			$("#periodos_horario_diferenciado").hide();
			$("#tituloHorario").hide();
			$("#modelos").hide();
	    });
		// if($("#hd_auxiliar").val() == 1) {
		// 	$("#periodos_horario_diferenciado_edit").show();
		// 	$("#tituloHorario_edit").show();
		// }
		// else {
		// 	$("#periodos_horario_diferenciado").hide();
		// 	$("#tituloHorario").hide();
		// }		
	});
		
	function adicionaExames(codigo) {
		
		var exames = "";
		var adiciona = true;
		var exame_localizado = [];
		$(".checkbox_exames").each(function(i, element_exames_disponiveis) {			
			if(element_exames_disponiveis.checked) {
				$(".exames").each(function(f, exame_cadastrado) {
					if($(exame_cadastrado).val() == $(element_exames_disponiveis).val()){
						$(element_exames_disponiveis).parent().next().find("div").remove();
						$(element_exames_disponiveis).parent().parent().css("border", "2px solid #b94a48");
						$(element_exames_disponiveis).parent().next().append("<div style=\'color:#b94a48\; font-weight:bold; margin:5px 0px;\'>Exame já cadastrado!</div>");
						
						exame_localizado.push($(element_exames_disponiveis).val());
						adiciona = false;
					}
					else{
						adiciona = true;
					}
				});
				$("#exames select").each(function(i, element_exames_selecionados) {
					adiciona = false;
				});
				if( $.inArray($(element_exames_disponiveis).val(), exame_localizado) !== -1 ){
				}else{
					if((adiciona))
						exames = exames + $(element_exames_disponiveis).val() + ",";
				};				
			}
		});
		
		if((exames.length) && exame_localizado.length == 0) {
			codigos = exames.substring(0,(exames.length - 1));
			$.ajax({
		        type: "POST",
		        url: "/portal/propostas_credenciamento/add_exames",
		        dataType: "json",
		        data: "exames=" + codigos + "&codigo_proposta_credenciamento=" + codigo,
				beforeSend: function() {
					$(".modal").css("z-index", "1050");
					$("#modal_carregando").modal("show");
 					$("#modal_tabela_padrao").modal("hide");
				},
		        success: function(json) {
 					if(json == "1") {
						window.location.href = document.URL.replaceAll("/dados_exames", "") + "/dados_exames";
					}
					else{
 						$("#modal_tabela_padrao").modal("show");
 					}
		        },
				complete: function() { }
		    });			
			
		}
	}
	
	function verificaContra(element, codigo, valor) {
		if(($(element).val().trim() == valor.replace(".", ",").trim()) || ($(element).val().trim() == "")) {
			$("#exame_" + codigo).show();
		} else {
			$("#exame_" + codigo).hide();
		}
	}
		
	function solicita_atualiza_status_renegocia_valor_minimo(proposta) {
		$(".modal").css("z-index", "1050");
		$("#modal_carregando").modal("show");
		atualiza_status_renegocia_valor_minimo(proposta);
	}
	
	function atualiza_status_renegocia_valor_minimo(proposta) {
	    $.ajax({
	        type: "POST",
	        url: "/portal/propostas_credenciamento/atualiza_status_renegocia_valor_minimo",
	        dataType: "json",
	        data: "codigo=" + proposta,
			success: function(json) {
				
			},
			complete: function() {
				window.location.href = document.URL.replaceAll("/dados_exames", "") + "/dados_exames";
			}
	    });
	}
		
	function atualiza_status_valor_minimo_renegociado(proposta) {
	    $.ajax({
	        type: "POST",
	        url: "/portal/propostas_credenciamento/atualiza_status_valor_minimo_renegociado",
	        dataType: "json",
	        data: "codigo=" + proposta,
	        beforeSend: function() {
				$(".modal").css("z-index", "1050");
				$("#modal_carregando").modal("show");			
			},
	        success: function(json) {
				window.location.href = document.URL.replaceAll("/dados_exames", "") + "/dados_exames";
	        },
	        complete: function() {
				
			}
	    });
	} 
		
	function verifica_VALORES_MINIMOS_NEGOCIADOS(proposta) {
		$.ajax({
	    	type: "POST",
	    	url: "/portal/propostas_credenciamento/verifica_valores_minimos_nao_negociados",
	    	dataType: "json",
	    	data: "proposta=" + proposta,
	    	beforeSend: function() {},
	    	success: function(json) {
				if(json == "0") {
					atualiza_status_valor_minimo_renegociado(proposta);
				}
				
	    	},
	    	complete: function() {}
	    });	
	}
	
	function enviar_renegociacoes_de_valor_minimo(proposta) {
		$(".modal").css("z-index", "1050");
		$("#modal_carregando").modal("show");			
		
		$("input.valor_minimo").each(function(i, element) {
			if($(element).attr("disabled") != "disabled") {
			    $.ajax({
			        type: "POST",
			        url: "/portal/propostas_credenciamento/atualiza_valor_minimo",
			        dataType: "json",
			        data: "codigo=" + $(element).attr("id").replace("PropostaCredExame", "").replace("ValorMinimo", "") + "&valor=" + $(element).val(),
			        beforeSend: function() { },
			        success: function(json) { },
			        complete: function() { }
			    });
			}
    	});
		
	    $.ajax({
	        type: "POST",
	        url: "/portal/propostas_credenciamento/atualiza_status_valor_minimo_renegociado",
	        dataType: "json",
	        data: "codigo=" + proposta,
	        beforeSend: function() { },
	        success: function(json) {
		
				if(json == "1") {
					atualiza_status_valor_minimo_renegociado(proposta);
				}
				else{
					window.location.href = document.URL.replaceAll("/dados_exames", "") + "/dados_exames";
				}
		
			},
	        complete: function() { }
	    });		
		
	}
		
	function valida_valor_minimo(codigo, status, valor, proposta) {
		
		if(status == "2") {
			atualiza_status_renegocia_valor_minimo('.$this->passedArgs[0].');
		}
		
		$.ajax({
	        type: "POST",
	        url: "/portal/propostas_credenciamento/valida_valor_minimo",
	        dataType: "json",
	        data: "codigo=" + codigo + "&status=" + status + "&valor=" + valor,
	        beforeSend: function() { 
				$(".modal").css("z-index", "1050");
				$("#modal_carregando").modal("show");
			},
	        success: function(json) {
		
		        if(json) {
		
		        	if(status == "1") {
		
		        		$("input[name=\'data[PropostaCredExame][" + codigo + "][valor_minimo]\']").attr("style", "float: left; width: 100px; text-align: right; border: 2px solid green;");
		        		$("#exame_" + codigo).html("<a id=\"resultado_" + codigo + "\" href=\"javascript:void(0);\" class=\"label label-success\">Aprovado Mínimo: R$" + valor + "</a> <br /><a href=\"javascript:void(0);\" onclick=\"voltar_valida_valor_minimo("+codigo+", \'"+valor+"\', "+proposta+")\">Reverter</a>").show();
		
		        	} else if(status == "0") {
		
		        		$("input[name=\'data[PropostaCredExame][" + codigo + "][valor_minimo]\']").attr("style", "float: left; width: 100px; text-align: right; border: 2px solid red;");
		        		$("#exame_" + codigo).html("<a id=\"resultado_" + codigo + "\" href=\"javascript:void(0);\" class=\"label label-inverse\">Reprovado Mínimo: R$" + valor + "</a> <br /><a href=\"javascript:void(0);\" onclick=\"voltar_valida_valor_minimo("+codigo+", \'"+valor+"\', "+proposta+")\">Reverter</a>").show();
		
					} else if(status == "null") {
		
						$("input[name=\'data[PropostaCredExame][" + codigo + "][valor_minimo]\']").attr("disabled", "disabled");
						$("#exame_" + codigo).html("<a id=\"resultado_" + codigo + "\" href=\"javascript:void(0);\" class=\"btn\">Valor Mínimo Renegociado</a>").show();
		
						verifica_VALORES_MINIMOS_NEGOCIADOS(proposta);
					} 
		
					if(status == "2") {
						window.location.href = document.URL.replaceAll("/dados_exames", "") + "/dados_exames";
					 } else {
						verifica_STATUS_exames(proposta, '.$status.');
					}
		
		        }
	        },
	        complete: function() { 
				if(status != "2") {
					$("#modal_carregando").modal("hide");
					$(".modal").css("z-index", "-1");		
				}
		
			}
	    });
	}
	
	function voltar_valida_valor_minimo(codigo, valor, proposta) {
	    $.ajax({
	        type: "POST",
	        url: "/portal/propostas_credenciamento/voltar_valida_valor_minimo",
	        dataType: "json",
	        data: "codigo=" + codigo,
	        beforeSend: function() { $("#exame_" + codigo).hide(); $("#carregando_" + codigo).fadeIn(); },
	        success: function(json) {
		        if(json) {
		        	$("input[name=\'data[PropostaCredExame][" + codigo + "][valor_minimo]\']").attr("style", "float: left; width: 100px; text-align: right; border: 1px solid #ccc;");
	        		$("#exame_" + codigo).html("<a href=\'javascript:void(0);\' class=\'label label-success\' onclick=\'valida_valor_minimo("+codigo+", 1, "+valor+", "+proposta+");\'><i class=\'icon-white icon-thumbs-up\'></i> APROVAR!</a> <a href=\'javascript:void(0);\' class=\'label label-important\' onclick=\'valida_valor_minimo("+codigo+", 0, "+valor+", "+proposta+");\'><i class=\'icon-white icon-thumbs-down\'></i> REPROVAR!</a>").show();
		        	verifica_STATUS_exames(proposta, '.$status.');
		        }
		        $(".modal-backdrop").remove();
	        },
	        complete: function() { $("#carregando_" + codigo).hide(); $(".modal-backdrop").remove(); }
	    });
	}	
	
	function aprovar_engenharia(codigo, status, proposta) {
		var nome = "' . $authUsuario['Usuario']['nome'] . '";
		
	    $.ajax({
	        type: "POST",
	        url: "/portal/propostas_credenciamento/status_exame",
	        dataType: "json",
	        data: "codigo=" + codigo + "&status=" + status,
	        beforeSend: function() { $("#engenharia_" + codigo).hide(); $("#carregando_" + codigo).fadeIn(); },
	        success: function(json) {
		        if(json) {
		        	if(status == 1) {
		        		var botao = $("#aprovado").clone().find("a").html($("#aprovado").find("a").html() + ": " + nome);
		        	} else {
		        		var botao = $("#reprovado").clone().find("a").html($("#reprovado").find("a").html() + ": " + nome);
		        	}
		        	$("#engenharia_" + codigo).html(botao.show()).append("<a href=\'javascript:void(0);\' onclick=\'voltar_status_engenharia(" + codigo + ", " + proposta + ");\' style=\'cursor: default;\'>Reverter</a>");
		        }
	        },
	        complete: function() { $("#carregando_" + codigo).hide();  $("#engenharia_" + codigo).fadeIn(); verifica_STATUS_exames(proposta, '.$status.'); }
	    });		
	}
	
	function voltar_status_engenharia(codigo, proposta) {
		var nome = "' . $authUsuario['Usuario']['nome'] . '";
		
	    $.ajax({
	        type: "POST",
	        url: "/portal/propostas_credenciamento/volta_status_exame",
	        dataType: "json",
	        data: "codigo=" + codigo + "&codigo_proposta=" + proposta,
	        beforeSend: function() { $("#engenharia_" + codigo).hide(); $("#carregando_" + codigo).fadeIn(); },
	        success: function(json) {
		        if(json) {
		        	$("#engenharia_" + codigo).html("<a class=\'label label-info\' onclick=\'aprovar_engenharia("+codigo+", 1, "+proposta+");\' id=\'engenharia_"+codigo+"\' href=\'javascript:void(0);\'><i class=\'icon-white icon-thumbs-up\'></i> Aprova!</a><a class=\'label label-danger\' onclick=\'aprovar_engenharia("+codigo+", 0, "+proposta+");\' id=\'engenharia_"+codigo+"\' href=\'javascript:void(0);\'><i class=\'icon-white icon-thumbs-up\'></i> Reprovar!</a>");
		        	$("#carregando_" + codigo).hide();  
		        	$("#engenharia_" + codigo).fadeIn();
		        }
	        },
	        complete: function() { $("#carregando_" + codigo).hide();  $("#engenharia_" + codigo).fadeIn(); verifica_STATUS_exames(proposta, '.$status.'); }
	    });		
	}	
	
	function aprovar_exame(codigo, status, proposta) {
		var nome = "' . $authUsuario['Usuario']['nome'] . '";
		
	    $.ajax({
	        type: "POST",
	        url: "/portal/propostas_credenciamento/status_exame",
	        dataType: "json",
	        data: "codigo=" + codigo + "&status=" + status,
	        beforeSend: function() { $("#exame_" + codigo).hide(); $("#carregando_" + codigo).fadeIn(); },
	        success: function(json) {
		        if(json) {
		        	if(status == "1") {
		        		$("input[name=\'data[PropostaCredExame][" + codigo + "][valor]\']").attr("style", "float: left; width: 100px; text-align: right; border: 2px solid green;");
		        		$("input[name=\'data[PropostaCredExame][" + codigo + "][valor_contra_proposta]\']").attr("disabled", "disabled");
		        		$("input[name=\'data[PropostaCredExame][" + codigo + "][valor_contra_proposta]\']").attr("style", "float: left; width: 100px; text-align: right; border: 1px solid #ccc;");
		
		        		var botao = $("#aprovado").clone().find("a").html($("#aprovado").find("a").html() + ": " + nome);
		        		$("#exame_" + codigo).html(botao.show()).append("<a href=\'javascript:void(0);\' onclick=\'volta_status_exame(" + codigo + ", " + proposta + ");\'>Reverter</a>");
		
		        	} else {
		        		$("input[name=\'data[PropostaCredExame][" + codigo + "][valor]\']").attr("style", "float: left; width: 100px; text-align: right; border: 2px solid red;");
		        		$("input[name=\'data[PropostaCredExame][" + codigo + "][valor_contra_proposta]\']").attr("disabled", "disabled");
		        		$("input[name=\'data[PropostaCredExame][" + codigo + "][valor_contra_proposta]\']").attr("style", "float: left; width: 100px; text-align: right; border: 1px solid #ccc;");
		
		        		var botao = $("#reprovado").clone().find("a").html($("#reprovado").find("a").html());
		        		$("#exame_" + codigo).html(botao.show()).append("<a href=\'javascript:void(0);\' onclick=\'volta_status_exame(" + codigo + ", " + proposta + ");\'>Reverter</a>");		
					}
		        	
		        	$("#carregando_" + codigo).hide();  $("#exame_" + codigo).fadeIn();
		        }
	        },
	        complete: function() { verifica_STATUS_exames(proposta, '.$status.'); }
	    });	
	}
	function volta_status_exame(codigo, proposta) {
	    $.ajax({
	        type: "POST",
	        url: "/portal/propostas_credenciamento/volta_status_exame",
	        dataType: "json",
	        data: "codigo=" + codigo + "&codigo_proposta=" + proposta,
	        beforeSend: function() { $("#exame_" + codigo).hide(); $("#carregando_" + codigo).fadeIn(); },
	        success: function(json) {
		        if(json == "1") {
		        	$("input[name=\'data[PropostaCredExame][" + codigo + "][valor_contra_proposta]\']").attr("disabled", "true");
	        		$("input[name=\'data[PropostaCredExame][" + codigo + "][valor_contra_proposta]\']").removeAttr("disabled");
	        		$("input[name=\'data[PropostaCredExame][" + codigo + "][valor]\']").attr("style", "float: left; width: 100px; text-align: right; border: 1px solid #ccc;");
	        		$("input[name=\'data[PropostaCredExame][" + codigo + "][valor_contra_proposta]\']").attr("style", "float: left; width: 100px; text-align: right; border: 1px solid #ccc;");
		        		
	        		$("#exame_" + codigo).html("<a class=\'label label-info\' onclick=\'aprovar_exame("+codigo+", 1, "+proposta+")\' href=\'javascript:void(0);\'><i class=\'icon-white icon-thumbs-up\'></i> Aceitar!</a> <a class=\'label label-inverse\' onclick=\'aprovar_exame("+codigo+", 0, "+proposta+")\' href=\'javascript:void(0);\'><i class=\'icon-white icon-thumbs-down\'></i> Reprovar!</a>").show();
		
		        	$("#carregando_" + codigo).hide();
		        	$("#form-actions").show();
		        }
	        },
	        complete: function() { verifica_STATUS_exames(proposta, '.$status.') }
	    });	
	}
	
	function verifica_STATUS_exames(proposta, status) {
	
		$.ajax({
	    	type: "POST",
	    	url: "/portal/propostas_credenciamento/verifica_exames_proposta",
	    	dataType: "json",
	    	data: "proposta=" + proposta,
	    	beforeSend: function() {},
	    	success: function(json) {
	    		if((json == "1") || (json == "2")) {
		
	    			if((status < 5) || status == "12" || status == "16" || status == "17") {
						$("#botao-status").show();
		
						$("#msg-exames-sem-retorno").hide();
						$("#msg-analisar-valores-servicos").hide();
						$("#form-actions-solicita-negociacao-minimo").hide();
	    			}
	    		} else {
	    		
	    			$("#botao-status").hide();
	    			$("#msg-exames-sem-retorno").show();
	    			$("#msg-analisar-valores-servicos").show();
		
					$("#form-actions-solicita-negociacao-minimo").show();
	    			
	    			return false;
	    		}
	    	},
	    	complete: function() {}
	    });	
	}
	
	function verifica_preenchimento_contraproposta(proposta) {
    	var sem_contra_proposta = 0;
    	
    	$("input.contra_proposta").each(function(i, element) {
    		if(($(element).attr("disabled") != "disabled") && $(element).val().trim() == "") {
    			sem_contra_proposta++; 
    			$(element).css({"border":"2px solid red"});
    		} else {
    			$(element).attr("style", "float: left; width: 100px; text-align: right; border: 1px solid #ccc;");
    		}
    	});
    	if(sem_contra_proposta != 0) {
    		alert("Você não pode enviar o formulário de contra proposta com valores em branco. \nAprovar os exames primeiro, e digitar todos os valores de contra proposta.");
		
    		return false;
    	}
    	
		$.ajax({
	    	type: "POST",
	    	url: "/portal/propostas_credenciamento/verifica_engenharias_proposta",
	    	dataType: "json",
	    	data: "proposta=" + proposta,
	    	beforeSend: function() { 					
				$(".modal").css("z-index", "1050");
				$("#modal_carregando").modal("show");
			},
	    	success: function(json) {
	    		if((json != "1")) {
					manipula_modal("modal_carregando", 0);
		    		alert("Não é possível enviar contra-proposta. Você ainda tem serviços de engenharia pendente de avaliação!");
		    		
		    		$("#dados_exames").removeClass("active");
		    		$("#aba-dados_exames").removeClass("active");
		    		
		    		$("#dados_engenharias").addClass("active");
		    		$("#aba-dados_engenharias").addClass("active");	 
		
	    			return false;
	    			
	    		} else {
	    			$("#PropostaCredExameEditarForm").submit();
	    		}
	    	},
	    	complete: function() {}
	    });
	}
		
	function remove_exame(element, codigo, codigo_proposta) {
		
		if(confirm("Deseja realmente excluir este exame?")) {
			$.ajax({
		    	type: "POST",
		    	url: "/portal/propostas_credenciamento/remove_exame",
		    	dataType: "json",
		    	data: "codigo_proposta=" + codigo_proposta + "&codigo=" + codigo,
		    	beforeSend: function() { 					
					$(".modal").css("z-index", "1050");
					$("#modal_carregando").modal("show");
				},
		    	success: function(json) {
					if(json) {
						$(element).parents("tr").remove();
					}
		    	},
		    	complete: function() {
					$("#modal_carregando").modal("hide");
					$(".modal").css("z-index", "-1");
		
					verifica_STATUS_exames(codigo_proposta, '.$status.');
				}
		    });		
		}
		
	}
		
	function manipula_modal(id, mostra) {
		if(mostra) {
			$("#" + id).css("z-index", "1050");
			$("#" + id).modal("show");
		} else {
			$(".modal").css("z-index", "-1");
			$("#" + id).modal("hide");
		}
	}
	function voltar_status_contra_proposta(codigo_proposta){
		$(".modal").css("z-index", "1050");
		$("#modal_carregando").modal("show");			
	
		$("input.valor_minimo").each(function(i, element) {
			if($(element).attr("disabled") != "disabled") {
			    $.ajax({
			        type: "POST",
			        url: "/portal/propostas_credenciamento/limpa_valor_minimo",
			        dataType: "json",
			        data: "codigo=" + $(element).attr("id").replace("PropostaCredExame", "").replace("ValorMinimo", ""),
			        beforeSend: function() { },
			        success: function(json) { },
			        complete: function() { }
			    });
			}
    	});
	    $.ajax({
	        type: "POST",
	        url: "/portal/propostas_credenciamento/atualiza_status_contra_proposta",
	        dataType: "json",
	        data: "codigo=" + codigo_proposta,
	        beforeSend: function() { },
	        success: function(json) {
				if(json == "1") {
					window.location.href = document.URL.replaceAll("/dados_exames", "") + "/dados_exames";
				}
			},
	        complete: function() { }
	    });		
	}
  function bloquear_div_post(){
   	var div = jQuery("#solicita_documento");
    bloquearDiv(div);
  }
		
'); ?>
	
<?php echo $this->Buonny->link_js('proposta_credenciamento'); ?>
