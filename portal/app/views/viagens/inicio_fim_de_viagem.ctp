
<?php echo $this->BForm->create('TViagViagem', array('action' => 'post', 'url' => array('controller' => 'Viagens','action' => 'inicio_fim_de_viagem', $this->data['TViagViagem']['viag_codigo'])));?>

<div class='row-fluid inline'>	
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('viag_codigo_sm', array('class' => 'input-small', 'label' => 'SM', 'readonly' => TRUE)) ?> 
		<?php echo $this->BForm->input('TTveiTipoVeiculo.tvei_descricao', array('class' => 'input-small', 'label' => 'Tipo Veículo', 'readonly' => TRUE)) ?>
		<div class="control-group input text">
			<label>&nbsp;</label>
			<?php echo $this->Buonny->placa( $this->data['TVeicVeiculo']['veic_placa'], $this->data['TViagViagem']['viag_data_cadastro'], (empty($this->data['TViagViagem']['viag_data_fim']) ? Date('d/m/Y H:i:s') : $this->data['TViagViagem']['viag_data_fim']) ); ?>
		</div>
	</div>
	<div class='row-fluid inline'>
		<h5>Tecnologia do Veículo</h5>
		<?php echo $this->BForm->hidden('viag_codigo') ?>
		<?php echo $this->BForm->hidden('viag_data_cadastro') ?>
		<?php echo $this->BForm->hidden('TVeicVeiculo.veic_placa') ?>
		<?php echo $this->BForm->hidden('TUposUltimaPosicao.upos_data_comp_bordo') ?>
		<?php echo $this->BForm->input('TTecnTecnologia.tecn_descricao', array('class' => 'input-large', 'label' => 'Tecnologia', 'readonly' => TRUE)) ?>
		<?php echo $this->BForm->input('TTermTerminal.term_numero_terminal', array('class' => 'input-small', 'label' => 'Numero', 'readonly' => TRUE)) ?>
		<div class="control-group input text">
			<label>&nbsp;</label>
			<?php if(Comum::dateToTimestamp($this->data['TUposUltimaPosicao']['upos_data_comp_bordo']) >= strtotime('- 2 HOUR')): ?>
				<span class="badge-empty badge badge-success" title="Posicionando Normal"></span>
			<?php else: ?>
				<span class="badge-empty badge" title="Sem Posicionamento"></span>
			<?php endif; ?>
		</div>
	</div>

	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('viag_data_inicio', array('class' => 'data input-small', 'label' => 'Data Inicio', 'type' => 'text')) ?>
		<?php echo $this->BForm->input('viag_hora_inicio', array('class' => 'hora input-mini', 'label' => 'Hora')) ?>
		
		<?php echo $this->BForm->input('viag_data_fim', array('class' => 'data input-small', 'label' => 'Data Final', 'type' => 'text')) ?>
		<?php echo $this->BForm->input('viag_hora_fim', array('class' => 'hora input-mini', 'label' => 'Hora')) ?> 
		
		<?php echo $this->BForm->input('viag_pedido_cliente', array('class' => 'input-small', 'label' => 'Pedido Cliente')) ?>
	</div>
</div>
<div class='row-fluid inline'>	
	<?php if(isset($alertas)): ?>
		<?php foreach ($alertas as $key => $value): ?>
			<?php echo $this->BForm->error_menssage($value) ?>
		<?php endforeach; ?>
	<?php endif; ?>
</div>

	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')) ?>
	<?php echo $this->Html->link('Outra SM', array('controller' => 'viagens', 'action' => 'inicio_fim_de_viagem_localiza'),array('div' => false, 'class' => 'btn')) ?>
<?php echo $this->BForm->end() ?>

<?php echo $this->Javascript->codeBlock('

	$(document).ready(function(){
		setup_datepicker();
		setup_time();
		setup_mascaras();
		
	});', false);
?>