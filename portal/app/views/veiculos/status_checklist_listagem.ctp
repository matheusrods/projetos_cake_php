
<?php if($cliente): ?>
	<div id="cliente" class='well'>
		<strong>Código: </strong><?php echo $cliente['Cliente']['codigo'] ?>
		<strong>Cliente: </strong><?php echo $cliente['Cliente']['razao_social'] ?>
		<span class="pull-right">
            <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel')) ?>
        </span>
	</div>
<?php endif; ?>

<?php if(isset($paginator)): ?>

	<?php $data_upos = date('Y-m-d H:i:s',strtotime('-2 hour')); ?>
	<?php echo $paginator->options(array('update' => 'div.lista')); ?>

	<div class='row-fluid inline'>
		<table class='table table-striped' style='width:1700px;max-width:none;'>
			<thead>
				<tr style="border:none" >
					<th class='input-small' rowspan="2" style="vertical-align: middle;">Placa</th>
					<th rowspan="2" style="vertical-align: middle;">Transportador</th>
					<th class='input-small' rowspan="2" style="vertical-align: middle;">Veiculo</th>
					<th class='input-small' rowspan="2" style="vertical-align: middle;">Tipo</th>
					<th class='input-small' rowspan="2" style="vertical-align: middle;">Tecnologia</th>
					<th class='input-medium' rowspan="2" style="vertical-align: middle;">Origem</th>
					<th class='input-small' rowspan="2" style="vertical-align: middle;">Status</th>
					<th class='input-small' colspan="4" style="text-align: center;">Último Checklist</th>
					<th style="width:14px; vertical-align: middle" title='Posicionamento' rowspan="2">Pos</th>
					<th class='input-small' rowspan="2" style="vertical-align: middle;">Fabricante</th>
					<th rowspan="2" style="vertical-align: middle;">Modelo</th>
					<th class='input-small' rowspan="2" style="vertical-align: middle;">Ano</th>
					<th class='input-small' rowspan="2" style="vertical-align: middle;">Terminal</th>
					<th class='input-middle' rowspan="2" style="vertical-align: middle;">Posição Checklist</th>
				</tr>
				<tr  style="border:none;box-shadow:none">
					<th class='input-small' style="border-top: none">Data</th>
					<th class='input-small' style="border-top: none">Hora Checklist</th>
					<th class='input-small numeric' style="border-top: none">Dias Checklist</th>
					<th class='input-small' style="border-top: none">Operador</th>
				</tr>
			</thead>
			
			<tbody>
				
				<?php foreach ($veiculos as $veiculo):?>
					<?php $placa = $veiculo['TVeicVeiculo']['veic_placa']; ?>
					<tr>
						<td><?= $this->Buonny->placa($veiculo['TVeicVeiculo']['veic_placa'], date('d/m/Y 00:00:00'), date('d/m/Y 23:59:59'),$cliente['Cliente']['codigo']) ?></td>
						<td><?= $veiculo['TPjurPessoaJuridica']['pjur_razao_social']?></td>
						<td><?= isset($veiculo['TVtraVeiculoTransportador']) ? $veiculo['TVtraVeiculoTransportador']['vtra_tip_cliente'] : $veiculo['TVembVeiculoEmbarcador']['vemb_tip_cliente'] ?></td>
						<td><?= $veiculo['TTveiTipoVeiculo']['tvei_descricao']?></td>
						<td><?= $veiculo['TTecnTecnologia']['tecn_descricao'] ?></td>
						<td><?= $veiculo['TRefeReferencia']['refe_descricao'] ?></td>
						<td><?= $veiculo['TVeicVeiculo']['veic_status'] ?></td>
						<td><?=Comum::formataData($veiculo['TUcveUltimoChecklistVeiculo']['ucve_data_cadastro'],'dmyhms','dmy');?></td>
						<td><?=!empty($veiculo['TCveiChecklistVeiculo']['cvei_data_cadastro']) ? Comum::formataData($veiculo['TCveiChecklistVeiculo']['cvei_data_cadastro'],'dmyhms' ,'hm') : '' ?></td>
						<td class='numeric'><?= $this->Buonny->moeda($veiculo['0']['dias_checklist'], array('places' => 0, 'nozero' => true)) ?></td>
						<td><?=$veiculo['TCveiChecklistVeiculo']['cvei_usuario_adicionou'] ?></td>
						<td>
							<?php if(date('Y-m-d H:i:s',Comum::dateToTimestamp($veiculo['TUposUltimaPosicao']['upos_data_comp_bordo'])) >= $data_upos): ?>
								<span class="badge badge-empty badge-success" title="Posicionando Normal"></span>
							<?php else: ?>
								<span class="badge badge-empty" title="Sem Posicionamento"></span>
							<?php endif; ?>
						</td>
						<td><?= $veiculo['TMveiMarcaVeiculo']['mvei_descricao']?></td>
						<td><?= $veiculo['TMvecModeloVeiculo']['mvec_descricao']?></td>
						<td><?= $veiculo['TVeicVeiculo']['veic_ano_modelo']?></td>
						<td><?= $veiculo['TTermTerminal']['term_numero_terminal'] ?></td>
						<td>
	    					<?php if($veiculo['TCveiChecklistVeiculo']['cvei_mcch_codigo']): ?>
	    						<span class="badge-empty badge badge-warning" title="Checklist recusado"></span>
	    					<?php else: ?>
								<?php if($veiculo['TCveiChecklistVeiculo']['cvei_status']): ?>

									<?php if ($veiculo['0']['dias_checklist'] <= $veiculo['0']['dias_checklist']): ?>
										<span class="badge-empty badge badge-success" title="Checklist aprovado"></span>
									<?php else: ?>
										<span class="badge-empty badge " title="Checklist expirado"></span>
									<?php endif ?>
								<?php else: ?>					
									<span class="badge-empty badge badge-important" title="Checklist <?php echo $checklist_posicao[$veiculo['0']['posicao_checklist']];?>"></span>
								<?php endif; ?>
							<?php endif ?>
						</td>
					</tr>
				<?php endforeach;?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan = "19">
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
	</div>
<?php endif; ?>