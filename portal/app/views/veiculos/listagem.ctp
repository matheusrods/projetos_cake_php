	<br />	
<?php if($cliente): ?>
		<div class='actionbar-right'>
			<?php echo $this->Html->Link('<span class="icon-download-alt"></span>&nbsp;Importar Veículos&nbsp;', array('action' => 'importar_veiculo', $cliente['Cliente']['codigo']), array('class'  => 'button', 'escape' => false,));?>
			<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'exportar'), array('escape' => false, 'title' =>'Exportar para Excel'));?>

			<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'localizar_veiculo', $cliente['Cliente']['codigo']), array('class' => 'btn btn-success', 'escape' => false )); ?>
		</div>

	<div id="cliente" class='well'>
		<strong>Código: </strong><?php echo $cliente['Cliente']['codigo'] ?>
		<strong>Cliente: </strong><?php echo $cliente['Cliente']['razao_social'] ?>
	</div>
	<?php endif; ?>

	<?php if(isset($paginator)): ?>

	<?php $data_upos = date('Y-m-d H:i:s',strtotime('-2 hour')); ?>
	<?php echo $paginator->options(array('update' => 'div.lista')); ?>
	
	<div class='row-fluid inline'>
		<table class='table table-striped' style="min-width:1152px">
			<thead>
				<th class='input-small'><?php echo $this->Paginator->sort('Placa', 'mvei_descricao') ?></th>
				<th class='input-small'><?php echo $this->Paginator->sort('Veiculo do Cliente', 'tipo') ?></th>
				<th class='input-small'><?php echo $this->Paginator->sort('Tipo', 'TTveiTipoVeiculo.tvei_descricao') ?></th>
				<th class='input-small'><?php echo $this->Paginator->sort('Fabricante', 'TMveiMarcaVeiculo.mvei_descricao') ?></th>
				<th class='input-small'><?php echo $this->Paginator->sort('Modelo', 'TMVecModeloVeiculo.mvec_descricao') ?></th>
				<th class='input-small'><?php echo $this->Paginator->sort('Ano', 'veic_ano_modelo') ?></th>
				<th class='input-small'><?php echo $this->Paginator->sort('Tecnologia', 'TTecnTecnologia.tecn_descricao') ?></th>
				<th class='input-small'><?php echo $this->Paginator->sort('Terminal', 'TTermTerminal.term_numero_terminal') ?></th>
				<th class='input-small'><?php echo $this->Paginator->sort('Status', 'veic_status') ?></th>
				<th class='input-small'><?php echo $this->Paginator->sort('Cobrança', 'TTvcoTipoVinculoContratual.tvco_descricao') ?></th>
				<th class='input-small'><?php echo $this->Paginator->sort('Dias Sem viagem', 'TVembVeiculoEmbarcador.vemb_dias_sem_viagem') ?></th>
				<th></th>
				<th></th>
				<?php if (!isset($authUsuario['Usuario']['admin']) || $authUsuario['Usuario']['admin'] == 0): ?>
					<th></th>
				<?php endif; ?>
				<th></th>
				<th></th>
				<th style="width:54px"></th>
				<th style="width:54px"></th>
			</thead>
			
			<tbody>
				
				<?php foreach ($veiculos as $veiculo):?>
	
					<?php $placa = $veiculo['TVeicVeiculo']['veic_placa']; ?>
					<tr>
						<td>
							<?php echo $this->Buonny->placa($veiculo['TVeicVeiculo']['veic_placa'], date('d/m/Y 00:00:00'), date('d/m/Y 23:59:59'),$cliente['Cliente']['codigo']) ?>
						</td>
						<td>
							<?= isset($veiculo['TVtraVeiculoTransportador']) ? $veiculo['TVtraVeiculoTransportador']['vtra_tip_cliente'] : $veiculo['TVembVeiculoEmbarcador']['vemb_tip_cliente'] ?>
						</td>
						<td>
							<?php echo $veiculo['TTveiTipoVeiculo']['tvei_descricao']?>
						</td>
						<td>
							<?php echo $veiculo['TMveiMarcaVeiculo']['mvei_descricao']?>
						</td>
						<td>
							<?php echo $veiculo['TMvecModeloVeiculo']['mvec_descricao']?>
						</td>
						<td>
							<?php echo $veiculo['TVeicVeiculo']['veic_ano_modelo']?>
						</td>
						<td>
							<?php echo $veiculo['TTecnTecnologia']['tecn_descricao'] ?>
						</td>
						<td>
							<?php echo $veiculo['TTermTerminal']['term_numero_terminal'] ?>
						</td>
						<td>
							<?php echo $veiculo['TVeicVeiculo']['veic_status'] ?>
						</td>
						<td>
							<?php echo $veiculo['TTvcoTipoVinculoContratual']['tvco_descricao'] ?>
						</td>	
						<td>
							<?php echo isset($veiculo['TVtraVeiculoTransportador']) ? $veiculo['TVtraVeiculoTransportador']['vtra_dias_sem_viagem'] : $veiculo['TVembVeiculoEmbarcador']['vemb_dias_sem_viagem'] ?>
						</td>				
						<td>
							<?php $placa = $veiculo["TVeicVeiculo"]["veic_placa"];?>
							<?= (!$permite) ? '' : $html->link('', array('controller' => 'Veiculos', 'action' => 'senha_veiculo', $placa), array('onclick' => 'return open_dialog(this, "Alterar senha", 560)','class' => 'icon-lock dialog', 'title' => 'Alterar senha')) ?>
                   		</td>                
						<td>
							<?php echo $this->Html->link('', array('controller' => 'Veiculos','action' => 'cliente_veiculo_log',$cliente['Cliente']['codigo'], $veiculo['TVeicVeiculo']['veic_placa'], rand()), array('onclick' => 'return open_dialog(this,"Logs do Veículo", 880)', 'title' => 'Visualizar Logs', 'class' => 'icon-eye-open')) ?>
						</td>
						<?php if ($authUsuario['Usuario']['codigo_uperfil'] === Uperfil::ADMIN || $authUsuario['Usuario']['admin'] === 1): ?>
							<td>
								<?php echo $this->BMenu->linkOnClick('',array('controller' => 'Veiculos', 'action' => 'excluir',$cliente['Cliente']['codigo'], $veiculo['TVeicVeiculo']['veic_placa'],rand()), array('class' => 'icon-remove-sign', 'title' => 'Excluir Veiculo'),'Confirma exclusão?'); ?>
							</td>
						<?php endif; ?>
						<td>
							<?php echo $html->link('', array('controller' => 'Veiculos', 'action' => 'alterar2', $cliente['Cliente']['codigo'],$veiculo['TVeicVeiculo']['veic_placa']), array('class' => 'icon-edit', 'title' => 'Alterar Veiculo')); ?>
						</td>
						<td>
							<?php $cancelado = isset($veiculo['TVtraVeiculoTransportador']['vtra_cancelado']) ? $veiculo['TVtraVeiculoTransportador']['vtra_cancelado'] : $veiculo['TVembVeiculoEmbarcador']['vemb_cancelado'] ?>
							<?php if($cancelado): ?>
								<?php echo $html->link('', array('controller' => 'Veiculos', 'action' => 'reverter_cancelamento',$cliente['Cliente']['codigo'],$veiculo['TVeicVeiculo']['veic_placa']), array('class' => 'icon-refresh', 'title' => 'Reverter Cancelamento'), 'Confirma reverter o processo?'); ?>
							<?php else: ?>
								<?php echo $html->link('', array('controller' => 'Veiculos', 'action' => 'cancelar',$cliente['Cliente']['codigo'],$veiculo['TVeicVeiculo']['veic_placa']), array('class' => 'icon-trash', 'title' => 'Excluir veículo da frota'), 'Confirma exclusão?'); ?>
							<?php endif; ?>

						</td>
						<td>
							<?php if($cancelado): ?>
								<span class="badge badge-empty badge-important" title="Agendado para Cancelamento"></span>
							<?php else: ?>
								<span class="badge badge-empty badge-success" title="Normal"></span>
							<?php endif; ?>
						</td>
						<td>
							<?php if(date('Y-m-d H:i:s',Comum::dateToTimestamp($veiculo['TUposUltimaPosicao']['upos_data_comp_bordo'])) >= $data_upos): ?>
								<span class="badge badge-empty badge-success" title="Posicionando Normal"></span>
							<?php else: ?>
								<span class="badge badge-empty" title="Sem Posicionamento"></span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach;?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan = "18">
						<strong>Total</strong> 
						<?php echo $this->Paginator->params['paging']['TVeicVeiculo']['count']; ?>
					</td>
				</tr>
			</tfoot>
		</table>
		<div class='row-fluid'>
			<div class='numbers span6'>
				<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
				<?php echo $this->Paginator->numbers(); ?>
				<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
			</div>
			<div class='counter span6'>
				<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
			</div>
		</div>
			<?php echo $this->Js->writeBuffer(); ?>
		<?php else: ?>
		<div class="alert">Cliente não localizado</div>	
	<?php endif; ?>
	</div>
	<?php echo $this->Buonny->link_js('estatisticas'); ?>	