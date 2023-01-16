<div class='well'>
	<?php echo $this->BForm->create('Recebsm', array('autocomplete' => 'off', 'url' => array('controller' => 'solicitacoes_monitoramento', 'action' => 'motoristas'))) ?>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_periodo($this) ?>
			<?php if (!isset($authUsuario['Usuario']['tipo_empresa']) || $authUsuario['Usuario']['tipo_empresa'] == Cliente::SUBTIPO_EMBARCADOR): ?>
				<?php echo $this->Buonny->input_cliente_tipo($this, 1, $clientes_embarcadores) ?>
			<?php endif ?>
			<?php if (!isset($authUsuario['Usuario']['tipo_empresa']) || $authUsuario['Usuario']['tipo_empresa'] == Cliente::SUBTIPO_TRANSPORTADOR): ?>
				<?php echo $this->Buonny->input_cliente_tipo($this, 4, $clientes_transportadores) ?>
			<?php endif ?>
		</div>
		<?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end() ?>
</div>
<?php if (isset($motoristas)): ?>
	<div class='well'>
		<strong>Data Inicial: </strong><?php echo $this->data['Recebsm']['data_inicial']; ?>
		<strong>Data Final: </strong><?php echo $this->data['Recebsm']['data_final']; ?>
		<?php if (isset($cliente_embarcador) && !empty($cliente_embarcador)): ?>
			<strong>Embarcador: </strong><?php echo $cliente_embarcador['Cliente']['codigo'] . ' - '.$cliente_embarcador['Cliente']['razao_social']; ?>
			<?php if ($this->data['Recebsm']['cliente_embarcador']): ?>
				<?php echo ' - '.$clientes_embarcadores[$this->data['Recebsm']['cliente_embarcador']]; ?>
			<?php endif ?>
		<?php endif ?>
		<?php if (isset($cliente_transportador) && !empty($cliente_transportador)): ?>
			<strong>Transportador: </strong><?php echo $cliente_transportador['Cliente']['codigo'] . ' - '.$cliente_transportador['Cliente']['razao_social']; ?>
			<?php if ($this->data['Recebsm']['cliente_transportador']): ?>
				<?php echo ' - '.$clientes_embarcadores[$this->data['Recebsm']['cliente_transportador']]; ?>
			<?php endif ?>
		<?php endif ?>
	</div>
	<table class='table table-striped'>
		<thead>
			<th><?php echo $this->Html->link('Motorista', 'javascript:void(0)') ?></th>
			<th><?php echo $this->Html->link('CPF', 'javascript:void(0)') ?></th>
			<th class='numeric'><?php echo $this->Html->link('Viagens', 'javascript:void(0)') ?></th>
			<th class='action-icon'></th>
			<th class='action-icon'></th>
		</thead>
		<?php $total_viagens = 0 ?>
		<?php if ($motoristas): ?>
			<?php foreach ($motoristas as $motorista): ?>
				<?php $total_viagens += $motorista['0']['qtd_sm'] ?>
				<tr>
					<td><?php echo $motorista['Motorista']['nome'] ?></td>
					<td><?php echo $motorista['Motorista']['cpf'] ?></td>
					<td class='numeric'><?php echo $this->Buonny->moeda($motorista['0']['qtd_sm'], array('places' => 0)) ?></td>
					<td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-list-alt', 'title' => 'Jornada Motorista Sintético', 'onclick' => "jornada_motorista('{$motorista['Motorista']['codigo']}', '{$this->data['Recebsm']['data_inicial']}', '{$this->data['Recebsm']['data_final']}', 0)")) ?></td>
					<td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-list-alt', 'title' => 'Jornada Motorista', 'onclick' => "jornada_motorista('{$motorista['Motorista']['codigo']}', '{$this->data['Recebsm']['data_inicial']}', '{$this->data['Recebsm']['data_final']}', 1)")) ?></td>
					<!--<td class='action-icon'><?php //echo $this->Html->link('', 'javascript:void(0)', array('class' => 'icon-list-alt', 'title' => 'Ponto Motorista', 'onclick' => "ponto_motorista('{$motorista['Motorista']['codigo']}', '{$this->data['Recebsm']['data_inicial']}', '{$this->data['Recebsm']['data_final']}')")) ?></td>-->
				</tr>
			<?php endforeach ?>
		<?php endif ?>
		<tfoot>
			<tr>
				<td colspan="2"><strong>Total: </strong><?php echo count($motoristas) ?></td>
				<td class='numeric'><?php echo $this->Buonny->moeda($total_viagens, array('places' => 0)) ?></td>
				<td></td>
				<td></td>
			</tr>
		</tfoot>
	</table>
<?php endif ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){setup_datepicker();});', false) ?>