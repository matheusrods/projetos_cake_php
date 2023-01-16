<?php if (isset($cliente)): ?>
	<?php echo $this->element('veiculos/cliente') ?>
<? endif; ?>
<div class='actionbar-right'>
	<?php echo $this->Html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir_checklist',$dados[0][0]['veic_placa'],0,1, $cliente['Cliente']['codigo'] ), array('class' => 'btn btn-success', 'escape' => false )); ?>
</div>
<br>
<?php echo $this->Paginator->options(array('update' => '.problemas')); ?>
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
			<?php if($viagens): ?>
				<?php foreach ($viagens as $key => $viagem): ?>
					<?php if(isset($viagem[$key]) && is_array($viagem[$key])): ?>
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
					<?php elseif(isset($viagem[$key])): ?>
						<tr>
							<td><?php echo $this->Buonny->codigo_sm($viagem[$key]['TViagViagem']['viag_codigo_sm']) ?></td>
							<td><?php echo $viagem[$key]['TViagViagem']['viag_pedido_cliente'] ?></td>
							<td><?php echo $viagem[$key]['TViagViagem']['viag_previsao_inicio'] ?></td>
							<td><?php echo $viagem[$key]['Transportador']['pess_nome'] ?></td>
							<td><?php echo $viagem[$key]['Embarcador']['pess_nome'] ?></td>	
							<td><?php echo $viagem[$key]['MotoristaCpf']['pfis_cpf'] ?></td>
							<td><?php echo $viagem[$key]['Motorista']['pess_nome'] ?></td>
							<td><?php echo $viagem[$key]['contatos'] ?></td>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
<div class='row-fluid inline'>
	<table class='table table-striped'>
		<thead>
			<th><?php echo $this->Paginator->sort('Inclusão','TCveiChecklistVeiculo.cvei_data_cadastro')?></th>
			<th><?php echo $this->Paginator->sort('Cliente','Cliente.codigo')?></th>
			<th><?php echo $this->Paginator->sort('Razão Social','Cliente.razao_social')?></th>
			<th style="width:13px"></th>
			<th style="width:13px"></th>
		</thead>
		<? if($listagem): ?>
		<tbody>
			<?php foreach ($listagem as $key => $cvei):?>
				<tr>
					<td><?php echo $cvei['TCveiChecklistVeiculo']['cvei_data_cadastro'] ?></td>
					<td><?php echo $cvei['Cliente']['codigo'] ?></td>
					<td><?php echo $cvei['Cliente']['razao_social'] ?></td>
    				<td>
    					<?php echo $this->BMenu->linkOnClick('', array('controller' => 'veiculos','action' => 'visualizar_checklist',$cvei['TCveiChecklistVeiculo']['cvei_codigo'],rand()), array('title' => 'Visualizar Checklist' ,'class' => 'icon-eye-open')); ?>
    				</td>
    				<td>
    					<?php if($cvei['TCveiChecklistVeiculo']['cvei_mcch_codigo']): ?>
    						<span class="badge-empty badge badge-warning" title="Checklist recusado"></span>
    					<?php else: ?>
							<?php if($cvei['TCveiChecklistVeiculo']['cvei_status']): ?>
								<?php if ($cvei['0']['dias_checklist'] <= $this->data['TVeicVeiculo']['racs_validade_checklist']): ?>
									<span class="badge-empty badge badge-success" title="Checklist aprovado"></span>
								<?php else: ?>
									<span class="badge-empty badge " title="Checklist expirado"></span>
								<?php endif ?>
							<?php else: ?>					
								<span class="badge-empty badge badge-important" title="Checklist <?php echo $posicao_checklist;?>"></span>
							<?php endif; ?>
						<?php endif ?>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
		<? endif; ?>
		<tfoot>
			<tr>
				<td colspan = "8">
					<strong>Total:&nbsp;&nbsp;&nbsp;</strong><?php echo $this->Paginator->params['paging']['TCveiChecklistVeiculo']['count']; ?>
				</td>
			</tr>
		</tfoot>
	</table>	
</div>
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
<?php if(isset($ovei_codigo) && !empty($ovei_codigo) ): ?>
	<?php echo $this->Javascript->codeBlock("
		jQuery(document).ready(function(){
			window.opener.atualizaListaVeiculosOcorrencias2();
			window.close();
		});
	"); ?>
<?php endif; ?>