<?php if ($this->passedArgs[0] != 'export'): ?>
    <div class='well'>
        <span class="pull-right">
            <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
        </span>
    </div>
<?php echo $this->Paginator->options(array('update' => 'div.lista'));?>
<table class='table table-striped table-bordered' style="max-width:none; white-space:nowrap">
	<thead>
		<th colspan="5">&nbsp;</th>	
		<th style="text-align:center" colspan="2">Alvo</th>
		<th style="text-align:center" colspan="2">Planilha</th>
		<th style="text-align:center" colspan="2">Informações da Viagem</th>
		<th style="text-align:center" colspan="3">Expedição</th>
		<th style="text-align:center" colspan="3">Janela</th> 
		<th style="text-align:center" colspan="3">Detalhes do alvo</th>
		<th style="text-align:center" colspan="2">Baú</th>
	</thead>
	<thead>
		<th><?php echo $this->Paginator->sort('SM', 'viag_codigo_sm') ?></th>
		<th><?php echo $this->Paginator->sort('Rota', 'ipcp_rota') ?></th>
		<th><?php echo $this->Paginator->sort('Placa', 'veic_placa') ?></th>
		<th><?php echo $this->Paginator->sort('Status', 'stem_descricao') ?></th>
		<th><?php echo $this->Paginator->sort('Motivo', 'matr_descricao') ?></th>
		<th><?php echo $this->Paginator->sort('Origem', 'referencia_cd') ?></th>
		<th><?php echo $this->Paginator->sort('Loja', 'refe_loja_descricao') ?></th>
		<th><?php echo $this->Paginator->sort('Origem', 'referencia_cd') ?></th>
		<th><?php echo $this->Paginator->sort('Loja', 'refe_loja_descricao') ?></th>
		<th><?php echo $this->Paginator->sort('Data Cadastro Viagem', 'viag_data_cadastro') ?></th>
		<th><?php echo $this->Paginator->sort('Data Inicio Viagem', 'viag_data_inicio') ?></th>
		<th><?php echo $this->Paginator->sort('Inicial', 'ipcp_limite_expedicao_inicial') ?></th>
		<th><?php echo $this->Paginator->sort('Intermediária', 'ipcp_limite_expedicao_intermediario') ?></th>
		<th><?php echo $this->Paginator->sort('Final', 'ipcp_limite_expedicao_final') ?></th>
		<th><?php echo $this->Paginator->sort('Inicial', 'ipcp_janela_inicial') ?></th>
		<th><?php echo $this->Paginator->sort('Intervalo', 'ipcp_janela_intervalo') ?></th>
		<th><?php echo $this->Paginator->sort('Final', 'ipcp_janela_final') ?></th>
		<th><?php echo $this->Paginator->sort('Data Previsão Chegada', 'data_previsao') ?></th>
		<th><?php echo $this->Paginator->sort('Entrada', 'vlev_data_entrada') ?></th>
		<th><?php echo $this->Paginator->sort('Saída', 'vlev_data_saida') ?></th>
		<th><?php echo $this->Paginator->sort('Abertura', 'vloc_data_abertura_bau') ?></th>
		<th><?php echo $this->Paginator->sort('Fechamento', 'vloc_data_fechamento_bau') ?></th>
	</thead>
	<tbody>
		<?php if(isset($dadosPcp)): ?>
			<?php foreach ($dadosPcp as $key => $pcp):?>
				<?php $style_data_cadastro = in_array($pcp[0]['ipcp_matr_codigo'], array(TMatrMotivoAtraso::FATURAMENTO)) ? 'background:yellow' : '' ?>
				<?php $style_data_inicio = in_array($pcp[0]['ipcp_matr_codigo'], array(TMatrMotivoAtraso::EXPEDICAO)) ? 'background:yellow' : '' ?>
				<?php $style_data_previsao = in_array($pcp[0]['ipcp_matr_codigo'], array(TMatrMotivoAtraso::PERCURSO)) && $pcp[0]['ipcp_stem_codigo'] == TStemStatusTempo::PROVAVEL_ATRASO ? 'background:yellow' : '' ?>
				<?php $style_data_entrada = in_array($pcp[0]['ipcp_matr_codigo'], array(TMatrMotivoAtraso::PERCURSO)) && $pcp[0]['ipcp_stem_codigo'] == TStemStatusTempo::ATRASO ? 'background:yellow' : '' ?>
				<?php $style_data_saida = in_array($pcp[0]['ipcp_matr_codigo'], array(TMatrMotivoAtraso::RETENCAO)) ? 'background:yellow' : '' ?>
				<?php $style_limite_expedicao = in_array($pcp[0]['ipcp_matr_codigo'], array(TMatrMotivoAtraso::FATURAMENTO, TMatrMotivoAtraso::EXPEDICAO)) ? 'background:yellow' : '' ?>
				<?php $style_limite_janela = in_array($pcp[0]['ipcp_matr_codigo'], array(TMatrMotivoAtraso::PERCURSO, TMatrMotivoAtraso::RETENCAO)) ? 'background:yellow' : '' ?>
				<tr>
					<td class="numeric"><?= $this->Buonny->codigo_sm($pcp[0]['viag_codigo_sm'])?></td>
					<td class="numeric"><?= $pcp[0]['ipcp_rota'] ?></td>
					<td class="numeric"><?= $pcp[0]['veic_placa'] ?></td>
					<td> <?= $pcp[0]['stem_descricao'] ?></td>
					<td><?= $pcp[0]['matr_descricao'] ?></td>
					<td><?= $pcp[0]['referencia_cd']?></td>
					<td><?= $pcp[0]['refe_loja_descricao']?></td>
					<td><?= $pcp[0]['ipcp_cd']?></td>
					<td><?= $pcp[0]['ipcp_loja']?></td>
					<td style="<?= $style_data_cadastro ?>" ><?= AppModel::dbDateToDate($pcp[0]['viag_data_cadastro']) ?></td>
					<td style="<?= $style_data_inicio ?>" ><?= AppModel::dbDateToDate($pcp[0]['viag_data_inicio']) ?></td>
					<td><?= AppModel::dbDateToDate($pcp[0]['ipcp_limite_expedicao_inicial'])?></td>
					<td style="<?= $style_limite_expedicao ?>" ><?= AppModel::dbDateToDate($pcp[0]['ipcp_limite_expedicao_intermediario']) ?></td>
					<td><?= AppModel::dbDateToDate($pcp[0]['ipcp_limite_expedicao_final']) ?></td>
					<td><?= AppModel::dbDateToDate($pcp[0]['ipcp_janela_inicial']) ?></td>
					<td style="<?= $style_limite_janela ?>" ><?= AppModel::dbDateToDate($pcp[0]['ipcp_janela_intermediaria']) ?></td>
					<td style="<?= $style_limite_janela ?>" ><?= AppModel::dbDateToDate($pcp[0]['ipcp_janela_final']) ?></td>
					<td style="<?= $style_data_previsao ?>" ><?= substr(AppModel::dbDateToDate($pcp[0]['data_previsao']),0,19) ?></td>
					<td style="<?= $style_data_entrada ?>" ><?= AppModel::dbDateToDate($pcp[0]['vlev_data_entrada']) ?></td>
					<td style="<?= $style_data_saida ?>" ><?= AppModel::dbDateToDate($pcp[0]['vlev_data_saida']) ?></td>
					<td><?= (!empty($pcp[0]['vloc_data_abertura_bau']) ? AppModel::dbDateToDate(date('Y-m-d H:i:s',strtotime($pcp[0]['vloc_data_abertura_bau']))) : NULL) ?></td>
					<td><?= (!empty($pcp[0]['vloc_data_fechamento_bau']) ? AppModel::dbDateToDate(date('Y-m-d H:i:s',strtotime($pcp[0]['vloc_data_fechamento_bau']))) : NULL) ?></td>
				</tr>
			<?php endforeach ?>
		<?php else: ?>
			<?php $key = 0 ?>
		<?php endif ?>
	</tbody>
	<tfoot>
		<td><strong>Total: </strong><?php echo $this->Paginator->counter('{:count}'); ?></td>
		<td colspan="21"></td>
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
<?php endif; ?>