<?php if (isset($this->passedArgs[0]) && $this->passedArgs[0] == 'export'): ?>
    <div class='well'>
        <span class="pull-right">
            <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>   
        </span>
    </div>   
    
<?php else: ?>
	<?php if (isset($dados)): ?>
		<div class="well">
			<span class="pull-right">
				<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
			</span>
		</div>
		<?php echo $this->Paginator->options(array('update' => 'div.lista')); ?>
		<table class='table table-striped table-bordered' style='width:1600px;max-width:none'>
			<thead>
				<tr>
					<td class="input-xsmall"><?php echo $this->Paginator->sort('Rma','TOrmaOcorrenciaRma.orma_codigo')?></td>
					<td class="input-xsmall"><?php echo $this->Paginator->sort('SM','TViagViagem.viag_codigo_sm')?></td>
					<td class="input-xsmall"><?php echo $this->Paginator->sort('Embarcador','Embarcador.embarcador_pjur_razao_social')?></td>
					<td class="input-xsmall"><?php echo $this->Paginator->sort('Transportador','Transportador.transportador_pjur_razao_social')?></td>
					<td class="input-xsmall"><?php echo $this->Paginator->sort('Motorista','TPessPessoa.pess_nome')?></td>
					<td class="input-xsmall"><?php echo $this->Paginator->sort('CPF','TPfisPessoaFisica.pfis_cpf')?></td>
					<td class="input-xsmall"><?php echo $this->Paginator->sort('Gerador','TGrmaGeradorRma.grma_descricao')?></td>
					<td class="input-xsmall"><?php echo $this->Paginator->sort('Tipo','TTrmaTipoRma.trma_descricao')?></td>
					<td class="input-xsmall"><?php echo $this->Paginator->sort('Tecnologia','TVtecVersaoTecnologia.vtec_descricao')?></td>
					<td class="input-xsmall"><?php echo $this->Paginator->sort('Placa','TVeicVeiculo.veic_placa')?></td>
					<td class="input-xsmall"><?php echo $this->Paginator->sort('Data Ocorrencia','TOrmaOcorrenciaRma.orma_data_cadastro')?></td>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($dados as $key => $value): ?>
					<?php $relatorioMonitoramento = array('controller' => 'rma', 'action' => 'relatorio_monitoramento','Rma'=>$value[0]['orma_codigo'], 'SM' => $value[0]['viag_codigo']); ?>
					<tr> 
						<td class='input-xsmall' title="<?= $value[0]['orma_codigo'] ?>">						<?php echo $this->Html->link($value[0]['orma_codigo'],$relatorioMonitoramento,array('onclick'=>"return open_popup(this);",'title'=>'Relatório de Monitoramento'));?></td>
						<td class='input-xsmall' title="<?= $value[0]['viag_codigo_sm'] ?>">					<?= $this->Buonny->codigo_sm($value[0]['viag_codigo_sm']) ?></td>
						<td class='input-medium ellipsis' title="<?= $value[0]['embarcador_pjur_razao_social'] ?>">		<?= $value[0]['embarcador_pjur_razao_social'] ?></td>
						<td class='input-medium ellipsis' title="<?= $value[0]['transportador_pjur_razao_social'] ?>">	<?= $value[0]['transportador_pjur_razao_social'] ?></td>
						<td class='input-medium ellipsis' title="<?= $value[0]['pess_nome'] ?>">							<?= $value[0]['pess_nome'] ?></td>
						<td class='input-medium ellipsis' title="<?= $value[0]['pfis_cpf'] ?>">							<?= $value[0]['pfis_cpf'] ?></td>
						<td class='input-medium ellipsis' title="<?= $value[0]['grma_descricao'] ?>">					<?= $value[0]['grma_descricao'] ?></td>
						<td class='input-medium ellipsis' title="<?= $value[0]['trma_descricao'] ?>">					<?= $value[0]['trma_descricao'] ?></td>
						<td class='input-medium ellipsis' title="<?= $value[0]['vtec_descricao'] ?>">					<?= $value[0]['vtec_descricao'] ?></td>
						<td class='input-small'  title="<?= $value[0]['veic_placa'] ?>">						<?= $this->Buonny->placa($value[0]['veic_placa'],Date('d/m/Y 00:00:00'), Date('d/m/Y 23:59:59')) ?></td>
						<td class='input-medium ellipsis' title="<?= $value[0]['orma_data_cadastro'] ?>">				<?= $value[0]['orma_data_cadastro'] ?></td>
					</tr>
				<?php endforeach ?>
			</tbody>
			<tfoot>
	        	<tr>
	            	<td><strong>Total: </strong><?php echo $this->params['paging']['TOrmaOcorrenciaRma']['count']; ?></td>
	            	<td colspan='10'></td>
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
		 <?php echo $this->Buonny->link_js('estatisticas') ?>
	<?php endif ?>
<?php endif ?>