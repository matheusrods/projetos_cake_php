<div class="row">
				<div class="span6">
					<h3 >Dados da Empresa:</h3>
				   	<?php echo $this->BForm->input('PropostaCredenciamento.razao_social', array('class' => 'form-control', 'label' => 'Razão Social:', 'style' => 'width: 500px;', 'data-required' => true, 'readonly' => 'readonly')); ?>
				    <?php echo $this->BForm->input('PropostaCredenciamento.nome_fantasia', array('class' => 'form-control', 'label' => 'Nome Fantasia:', 'style' => 'width: 500px;', 'readonly' => 'readonly')); ?>
				    <?php echo $this->BForm->input('PropostaCredenciamento.codigo_documento', array('class' => 'form-control cnpj', 'label' => 'CNPJ:', 'style' => 'width: 200px;', 'readonly' => 'readonly')); ?>
					<hr />
					
					<?php foreach($this->data['PropostaCredEndereco'] as $key => $endereco) : ?>
						<?php if($endereco['matriz']) : ?>
							<h3 >Endereço Matriz:</h3>
						<?php else : ?>
							<h3 >Endereço Filial:</h3>
						<?php endif; ?>
						
					    <?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.cep', array('value' => $endereco['cep'], 'class' => 'form-control', 'label' => 'Cep:', 'style' => 'width: 200px;', 'readonly' => 'readonly')); ?>
					    <table>
					    	<tr>
					    		<td><?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.logradouro', array('value' => $endereco['logradouro'], 'class' => 'form-control', 'label' => 'Logradouro:', 'style' => 'width: 340px;', 'readonly' => 'readonly')); ?></td>
					    		<td><?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.numero', array('value' => $endereco['numero'], 'class' => 'form-control', 'label' => 'Número:', 'style' => 'width: 148px;', 'readonly' => 'readonly')); ?></td>
					    	</tr>
					    </table>
					    <?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.complemento', array('value' => $endereco['complemento'], 'class' => 'form-control', 'label' => 'Complemento:', 'style' => 'width: 500px;', 'readonly' => 'readonly')); ?>
					    <table>
					    	<tr>
					    		<td><?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.bairro', array('value' => $endereco['bairro'], 'class' => 'form-control', 'label' => 'Bairro:', 'style' => 'width: 200px;', 'readonly' => 'readonly')); ?></td>
					    		<td><?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.cidade', array('value' => $endereco['cidade'], 'class' => 'form-control', 'label' => 'Cidade:', 'style' => 'width: 225px;', 'readonly' => 'readonly')); ?></td>
					    		<td><?php echo $this->BForm->input('PropostaCredEndereco.'.$key.'.estado', array('value' => $endereco['estado'], 'class' => 'form-control', 'label' => 'Estado:', 'style' => 'width: 53px;', 'readonly' => 'readonly')); ?></td>
					    	</tr>
					    </table>
					<?php endforeach; ?>
					
					<?php if($status != StatusPropostaCred::PRECADASTRO) : ?>
						<hr />
						<h3 >Informações Bancárias:</h3>
							<?php echo $this->BForm->input('PropostaCredenciamento.melhor_dia_pagto', array('div' => false, 'label' => 'Dia de recebimento:', 'class' => 'form-control input-mini', 'style' => 'width: 100px;', 'readonly' => 'readonly')) ?>

							<?php echo $this->BForm->input('PropostaCredenciamento.cobranca_boleto', array('div' => true, 'legend' => 'Como prefere receber?', 'options' => array('0' => 'Depósito em Conta', '1' => 'Vou gerar Boleto'), 'type' => 'radio', 'readonly' => 'readonly', 'disabled' => true)) ?>

							<?php if(isset($this->data['PropostaCredenciamento']['cobranca_boleto']) && $this->data['PropostaCredenciamento']['cobranca_boleto'] == 0):?>			    			
							   	<?php echo $this->BForm->input('PropostaCredenciamento.numero_banco', array('class' => 'form-control', 'label' => 'Banco:', 'style' => 'width: 500px; text-transform: uppercase;', 'readonly' => 'readonly', 'value' => $banco['RhBanco']['codigo_banco'] . "  " . $banco['RhBanco']['descricao'])); ?>
		    					<?php echo $this->BForm->input('PropostaCredenciamento.tipo_conta', array('div' => true, 'legend' => 'Tipo de Conta', 'options' => array('1' => 'Conta Corrente', '0' => 'Conta Poupança'), 'type' => 'radio', 'readonly' => 'readonly', 'disabled' => true)) ?>
						    	<?php echo $this->BForm->input('PropostaCredenciamento.agencia', array('class' => 'form-control', 'label' => 'Agência:', 'style' => 'width: 500px;', 'readonly' => 'readonly')); ?>
						    	<?php echo $this->BForm->input('PropostaCredenciamento.numero_conta', array('class' => 'form-control', 'label' => 'Número de Conta:', 'style' => 'width: 500px;', 'readonly' => 'readonly')); ?>
						    	<?php echo $this->BForm->input('PropostaCredenciamento.favorecido', array('class' => 'form-control', 'label' => 'Favorecido:', 'style' => 'width: 500px;', 'readonly' => 'readonly')); ?>					
							<?php endif; ?>
					<?php endif; ?>
				</div>
				
				<?php if($status != StatusPropostaCred::PRECADASTRO) : ?>
					<div class="span6">
						<div class="form-group clear">
							<h3 >Tipo de Serviço Prestado:</h3>
							<span style="padding: 10px;"><?php echo $this->BForm->input('PropostaCredProduto.60', array('type'=>'checkbox', 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'disabled' => 'disabled', 'class' => 'input-xlarge', 'value' => '1', 'checked' => (isset($tipos_produto['60']) && !empty($tipos_produto['60']) ? '"checked"' : ''))); ?> Engenharia</span>
							<span style="padding: 10px;"><?php echo $this->BForm->input('PropostaCredProduto.59', array('type'=>'checkbox','label' => false, 'div' => false, 'multiple'=>'checkbox', 'disabled' => 'disabled', 'class' => 'input-xlarge', 'value' => '1', 'checked' => (isset($tipos_produto['59']) && !empty($tipos_produto['59']) ? '"checked"' : ''))); ?> Exames Complementares</span>
						</div>
						<hr />
						
						<?php if(isset($tipos_produto[59])) : ?>
							<div class="form-group clear">
								<h3 >Corpo Clínico: <span style="font-size: 16px;">(Médicos que realizam exames clínicos)</span></h3>
								<table>
									<?php foreach( $medicos as $key => $medico ): ?>
										<tr>
											<td><?php echo $this->BForm->input('Medico.' . $key . '.nome', array('value' => $medico['medico']['nome'], 'class' => 'form-control', 'label' => 'Nome:', 'style' => 'float: left; width: 250px;', 'readonly' => 'readonly')); ?></td>
											<td><?php echo $this->BForm->input('Medico.' . $key . '.codigo_conselho_profissional', array('value' => $list_conselhos[$medico['medico']['codigo_conselho_profissional']], 'class' => 'form-control', 'label' => 'Conselho:', 'style' => 'float: left; width: 100px;', 'readonly' => 'readonly')); ?></td>
							   				<td><?php echo $this->BForm->input('Medico.' . $key . '.numero_conselho', array('value' => $medico['medico']['numero_conselho'], 'class' => 'form-control', 'label' => 'Número:', 'style' => 'float: left; width: 100px;', 'readonly' => 'readonly')); ?></td>
							   				<td><?php echo $this->BForm->input('Medico.' . $key . '.conselho_uf', array('value' => $medico['medico']['conselho_uf'], 'class' => 'form-control', 'label' => 'UF:', 'style' => 'float: left; width: 60px;', 'readonly' => 'readonly')); ?></td>
							   			</tr>
									<?php endforeach; ?>
								</table>
							</div>
							<hr />
						<?php endif; ?>
						
						<?php if(isset($tipos_produto[59])) : ?>
							<h3 >Horário de Atendimento:</h3>
							<?php foreach( $horarios as $key => $horario ): ?>
								<table class="table table-striped">
								 	<thead class="thead-inverse">
										<tr>
											<td colspan="2">
												<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.seg', array('type'=>'checkbox', 'checked' => (strpos($horario['Horario']['dias_semana'], 'seg') !== false ? 'checked' : ''), 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'disabled' => 'disabled')); ?> Seg.
												<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.ter', array('type'=>'checkbox', 'checked' => (strpos($horario['Horario']['dias_semana'], 'ter') !== false ? 'checked' : ''), 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'disabled' => 'disabled')); ?> Ter.
												<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.qua', array('type'=>'checkbox', 'checked' => (strpos($horario['Horario']['dias_semana'], 'qua') !== false ? 'checked' : ''), 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'disabled' => 'disabled')); ?> Qua.
												<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.qui', array('type'=>'checkbox', 'checked' => (strpos($horario['Horario']['dias_semana'], 'qui') !== false ? 'checked' : ''), 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'disabled' => 'disabled')); ?> Qui.
												<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.sex', array('type'=>'checkbox', 'checked' => (strpos($horario['Horario']['dias_semana'], 'sex') !== false ? 'checked' : ''), 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'disabled' => 'disabled')); ?> Sex.
												<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.sab', array('type'=>'checkbox', 'checked' => (strpos($horario['Horario']['dias_semana'], 'sab') !== false ? 'checked' : ''), 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'disabled' => 'disabled')); ?> Sab.
												<?php echo $this->BForm->input('Horario.'.$key.'.dias_semana.dom', array('type'=>'checkbox', 'checked' => (strpos($horario['Horario']['dias_semana'], 'dom') !== false ? 'checked' : ''), 'label' => false, 'div' => false, 'multiple'=>'checkbox', 'disabled' => 'disabled')); ?> Dom.						
											</td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>
								    			<label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;"> DE </label>
								    			<?php echo $this->BForm->input('Horario.de_hora][]', array('value' => sprintf("%04s", $horario['Horario']['de_hora']), 'label' => false, 'div' => false, 'class' => 'form-control hora', 'style' => 'width: 60px; font-size: 11px;', 'empty' => false, 'readonly' => 'readonly')) ?>
								    		</td>
								    		<td>	
								    			<label style="float: left; padding: 1px 5px; font-size: 10px; text-align: center;">  ATÉ </label>
								    			<?php echo $this->BForm->input('Horario.ate_hora][]', array('value' => sprintf("%04s", $horario['Horario']['ate_hora']), 'label' => false, 'div' => false, 'class' => 'form-control  hora', 'style' => 'width: 60px; font-size: 11px;', 'empty' => false, 'readonly' => 'readonly')) ?>
											</td>
										</tr>				
									</tbody>
								</table>
							<?php endforeach; ?>
							<hr />
						<?php endif; ?>				
	
						<h3 >Contatos de Funcionamento:</h3>
						
						<?php if(isset($tipos_produto[59])) : ?>
						<table>
							<tr>
								<td><?php echo $this->BForm->input('PropostaCredenciamento.responsavel_tecnico_nome', array('class' => 'form-control', 'label' => 'Responsável Técnico:', 'style' => 'float: left; width: 250px;', 'readonly' => 'readonly')); ?></td>
								<td><?php echo $this->BForm->input('PropostaCredenciamento.codigo_conselho_profissional', array('class' => 'form-control', 'label' => 'Conselho:', 'style' => 'float: left; width: 90px;', 'readonly' => 'readonly', 'value' => $list_conselhos[$this->data['PropostaCredenciamento']['codigo_conselho_profissional']])); ?></td>
								<td><?php echo $this->BForm->input('PropostaCredenciamento.responsavel_tecnico_numero_conselho', array('class' => 'form-control', 'label' => 'Número:', 'style' => 'float: left; width: 90px;', 'readonly' => 'readonly')); ?></td>
								<td><?php echo $this->BForm->input('PropostaCredenciamento.responsavel_tecnico_conselho_uf', array('class' => 'form-control', 'label' => 'UF:', 'style' => 'float: left; width: 40px;', 'readonly' => 'readonly')); ?></td>
							</tr>
						</table>
						<?php endif; ?>
					    
						<?php echo $this->BForm->input('PropostaCredenciamento.responsavel_administrativo', array('class' => 'form-control', 'label' => 'Responsável Administrativo:', 'style' => 'width: 500px;', 'readonly' => 'readonly')); ?>
						<table>
							<tr>
								<td><?php echo $this->BForm->input('PropostaCredenciamento.telefone', array('class' => 'form-control telefone', 'label' => 'Telefone:', 'style' => 'width: 160px;', 'readonly' => 'readonly')); ?></td>
								<td><?php echo $this->BForm->input('PropostaCredenciamento.fax', array('class' => 'form-control telefone', 'label' => 'Fax:', 'style' => 'width: 160px;', 'readonly' => 'readonly')); ?></td>
								<td><?php echo $this->BForm->input('PropostaCredenciamento.celular', array('class' => 'form-control telefone', 'label' => 'Celular:', 'style' => 'width: 160px;', 'readonly' => 'readonly')); ?></td>
							</tr>
						</table>						
					    <?php echo $this->BForm->input('PropostaCredenciamento.email', array('class' => 'form-control', 'label' => 'E-mail:', 'style' => 'width: 500px;', 'readonly' => 'readonly')); ?>
					    
					    <?php if(isset($tipos_produto[59])) : ?>
						   	<?php echo $this->BForm->input('PropostaCredenciamento.tipo_atendimento', array('div' => true, 'legend' => 'Tipo de Atendimento:', 'options' => array('1' => 'Hora Marcada', '0' => 'Ordem de Chegada'), 'type' => 'radio', 'readonly' => 'readonly', 'disabled' => true)) ?>
						   	<?php echo $this->BForm->input('PropostaCredenciamento.exames_local_unico', array('div' => true, 'legend' => 'Todos os Exames são feitos em um único local ?', 'options' => array('1' => 'Sim', '0' => 'Não'), 'type' => 'radio', 'readonly' => 'readonly', 'disabled' => true)) ?>				    
					    <?php endif; ?>
					    
					   	<?php echo $this->BForm->input('PropostaCredenciamento.acesso_portal', array('div' => true, 'legend' => 'Possui disponibilidade para utilização do portal RHhealth (acesso via web):', 'options' => array('1' => 'Sim', '0' => 'Não'), 'type' => 'radio', 'readonly' => 'readonly', 'disabled' => true)) ?>
					</div>				
				<?php endif; ?>
			</div>
			<div class="row" style="height: 80px;"></div>