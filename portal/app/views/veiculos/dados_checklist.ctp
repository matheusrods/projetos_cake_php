<?php if(isset($this->data['Cliente']) && $this->data['Cliente']): ?>
	<div class="well">
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('Cliente.razao_social', array('class' => 'input-xxlarge', 'label' => 'Cliente', 'readonly' => TRUE)) ?>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->Buonny->placa($this->data['TVeicVeiculo']['veic_placa'], date('d/m/Y 00:00:00'), date('d/m/Y 23:59:59'),$this->data['Cliente']['codigo']) ?>
			
			<?php echo $this->BForm->input('TTecnTecnologia.tecn_descricao', array('class' => 'input-large', 'label' => 'Tecnologia', 'readonly' => TRUE)) ?>
			<?php echo $this->BForm->input('TTermTerminal.term_numero_terminal', array('class' => 'input-large', 'label' => 'Nº Terminal', 'readonly' => TRUE)) ?>
			<div class="control-group input text">
			<label>&nbsp;</label>
			<?php if($posicionando): ?>
				<span class="badge-empty badge badge-success" title="Veículo posicionando"></span>
			<?php else: ?>
				<span class="badge-empty badge" title="Sem posicionamento"></span>
			<?php endif; ?>
			</div>
		</div>
		<div class='row-fluid inline'>
			<?php echo $this->BForm->input('TCveiChecklistVeiculo.cvei_data_cadastro', array('class' => 'input-small', 'label' => 'Ultimo Checklist', 'readonly' => TRUE, 'type' => 'text')) ?>
			<?php echo $this->BForm->input('TCveiChecklistVeiculo.cvei_data_vencimento', array('class' => 'input-small', 'label' => 'Vencimento', 'readonly' => TRUE, 'type' => 'text')) ?>
			<div class="control-group input text">
			<label>&nbsp;</label>
			<?php if($vencimento == 1): ?>
				<span class="badge-empty badge badge-success" title="Checklist Válido"></span>
			<?php elseif($vencimento == 2): ?>
				<span class="badge-empty badge badge-importante" title="Checklist inválido"></span>
			<?php else: ?>
				<span class="badge-empty badge" title="Sem checklist"></span>
			<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="table">
		<table class='table table-striped' id="problemas">
			<thead>
				<th class='input-small'>SM</th>
				<th class='input-small'>Nº Pedido</th>
				<th class='input-medium'>Previsão Inicio</th>
				<th class='input-xlarge'>Transportador</th>
				<th class='input-xlarge'>Embarcador</th>
				<th class='input-small'>CPF</th>
				<th class='input-large'>Motorista</th>
			</thead>
			<tbody>
				<?php if($viagens): ?>
					<?php foreach ($viagens as $viag): ?>
						<tr>
							<td><?php echo $this->Buonny->codigo_sm($viag['TViagViagem']['viag_codigo_sm']) ?></td>
							<td><?php echo $viag['TViagViagem']['viag_pedido_cliente'] ?></td>
							<td><?php echo $viag['TViagViagem']['viag_previsao_inicio'] ?></td>
							<td><?php echo $viag['Transportador']['pess_nome'] ?></td>
							<td><?php echo $viag['Embarcador']['pess_nome'] ?></td>	
							<td><?php echo $viag['MotoristaCpf']['pfis_cpf'] ?></td>
							<td><?php echo $viag['Motorista']['pess_nome'] ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>