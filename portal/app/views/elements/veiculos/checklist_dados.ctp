<?php if($cliente): ?>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?> &nbsp; &nbsp;
		<strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?> &nbsp; &nbsp;
		<strong>Placa: </strong><?= $veic_placa ?> &nbsp; &nbsp;
	</div>
<?php endif; ?>
<br />
<div style="maxheight:200px">
	<table class='table table-striped' id="problemas">
		<thead>
			<th class='input-small'>SM</th>
			<th class='input-small'>Nº Pedido</th>
			<th class='input-medium'>Previsão Inicio</th>
			<th class='input-xlarge'>Transportador</th>
			<th class='input-xlarge'>Embarcador</th>
			<th class='input-small'>CPF</th>
			<th class='input-large'>Motorista</th>
			<th class='input-large'>Telefones</th>
		</thead>
		<tbody>
			<?php if( $viagens ): ?> 
				<?php foreach ($viagens as $key => $viagem): ?>
					<?php if(isset($viagem) && is_array($viagem)): ?>
						<?php foreach ($viagem as $viag): ?>
							<tr>
								<td><?php echo $this->Buonny->codigo_sm($viag['TViagViagem']['viag_codigo_sm']) ?></td>
								<td><?php echo $viag['TViagViagem']['viag_pedido_cliente'] ?></td>
								<td><?php echo $viag['TViagViagem']['viag_previsao_inicio'] ?></td>
								<td><?php echo $viag['Transportador']['pess_nome'] ?></td>
								<td><?php echo $viag['Embarcador']['pess_nome'] ?></td>	
								<td><?php echo $viag['MotoristaCpf']['pfis_cpf'] ?></td>
								<td><?php echo $viag['Motorista']['pess_nome'] ?></td>
								<td><?php echo $viag['contatos'] ?></td>
							</tr>
						<?php endforeach; ?>					
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
<?php if( !$viagens): ?>
<div class="alert alert">Nenhuma viagem encontrada para esse Veículo</div>
<?php endif; ?>
<br>
	<?php if(isset($incluir) && $incluir == true):?>
		<div class='actionbar-right'>
			<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir_checklist',0,$dados[0][0]['veic_placa'],0,1), array('class' => 'btn btn-success', 'escape' => false )); ?>
		</div>
	<?php endif; ?>
	<div id="cliente" class='well'>		
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('TTecnTecnologia.tecn_descricao', array('class' => 'input-large', 'label' => 'Tecnologia', 'readonly' => TRUE)) ?>
			<?php echo $this->BForm->input('TTermTerminal.term_numero_terminal', array('class' => 'input-large', 'label' => 'Nº Terminal', 'readonly' => TRUE)) ?>
			<div class="control-group input text">
				<label>&nbsp;</label>
				<?php echo $this->BForm->hidden("TVeicVeiculo.veic_placa", array('value' => $this->data['TVeicVeiculo']['veic_placa'])) ?>
				<?php echo $this->Buonny->placa($this->data['TVeicVeiculo']['veic_placa'], date('d/m/Y 00:00:00'), date('d/m/Y 23:59:59')) ?>
			</div>
			
			<div class="control-group input text">
			<label>&nbsp;</label>
			<?php if($posicionamento): ?>
				<span class="badge-empty badge badge-success" title="Veículo posicionando"></span>
			<?php else: ?>
				<span class="badge-empty badge" title="Sem posicionamento"></span>
			<?php endif; ?>
			</div>
			<div class="control-group input text" style="margin-left: 10px; max-width: 550px;">
					<label>Última Posição</label>
					<span><?=isset($posicionamento['TUposUltimaPosicao']['ultima_posicao']) ? $posicionamento['TUposUltimaPosicao']['ultima_posicao'] : NULL?></span>
			</div>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input('TCveiChecklistVeiculo.cvei_alvo_valido', array('label' => 'Alvo do Checklist','class'=> 'input-medium','readonly' => TRUE)) ?>			
				<?php echo $this->BForm->hidden('TCveiChecklistVeiculo.cvei_alvo_valido_refe_codigo') ?>
			</div>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('TCveiChecklistVeiculo.cvei_contato_nome', array('class' => 'input-large  contato','maxlength'=>50, 'label' => 'Contato', 'readonly' => (isset($this->data['visualizar_checklist']) && $this->data['visualizar_checklist'] == TRUE )?TRUE:FALSE)) ?>			
			<?php echo $this->BForm->input('TCveiChecklistVeiculo.cvei_contato_telefone', array('label' => 'Telefone','class'=> 'input-medium telefone','readonly' => (isset($this->data['visualizar_checklist']) && $this->data['visualizar_checklist'] == TRUE )?TRUE:FALSE)) ?>			
			<?php echo $this->BForm->input('TCveiChecklistVeiculo.codigo_cliente', array('value' => $cliente['Cliente']['codigo'], 'type'=>'hidden')) ?>
		</div>
		<?php if( empty($this->data['TCveiChecklistVeiculo']['cvei_status']) && !empty($this->data['TCveiChecklistVeiculo']['cvei_mcch_codigo']) )  :?>
			<?php echo $this->BForm->input('TCveiChecklistVeiculo.cvei_mcch_codigo', array(
			'value' => $motivos_cancelamento[$this->data['TCveiChecklistVeiculo']['cvei_mcch_codigo']], 
			'type'=>'text', 
			'class' => 'input-xlarge',
			'disabled'=>'disabled', 
			'label' => 'Motivo Recusa'
			)) ?>
		<?php endif;?>
		<div class='row-fluid inline'>	
			<div class="control-group input text">
				<label id="TCveiChecklistVeiculoCveiContatoTelefone-error" maxlenght= 50 style="display:none" class="error-ContatoNome" for="TCveiChecklistVeiculoCveiContatoTelefone">
						Por favor,informe um contato.
				</label>
			</div>	
			<div class="control-group input text">
				<label id="TCveiChecklistVeiculoCveiContatoTelefone-error" style="display:none" class="error-ContatoTelefone" for="TCveiChecklistVeiculoCveiContatoTelefone">
					Por favor, insira um telefone válido
				</label>
			</div>	
		</div>
		<?php if(isset($Operador) && $Operador == 1): ?>
			<div class = 'row-fluid inline'>
				<?php echo $this->BForm->input('TCveiUltimoChecklist.cvei_usuario_adicionou', array('class' => 'input-large', 'label' => 'Operador', 'readonly' => TRUE)) ?>
			</div>
		<?php endif; ?>	
		
	</div>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<br>
<br><br>