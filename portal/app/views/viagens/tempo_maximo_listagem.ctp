<?php if(!$filtrado): ?>
	<div class="alert">
		Defina os critérios de filtros.
	</div>
<?php elseif(empty($dados) && empty($dados_agrupamento_veiculo)): ?>
	<div class="alert">
		Nenhum registro encontrado.
	</div>
<?php else: ?>
	<div class='well'>
        <strong>Código: </strong><?= $cliente['Cliente']['codigo'] ?>
        <strong>Cliente: </strong><?= $cliente['Cliente']['razao_social'] ?>
        <span class="pull-right">
        <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export_cd'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
        </span>
    </div>
    <?php echo $this->Javascript->codeBlock("function filtros() {return ".json_encode($this->data['TViagViagem'])."}") ?>
	<table class='table table-striped table-bordered alvos'>
		<thead>
			<tr>
				<th colspan=2></th>
				<th colspan=2 style='text-align:center'>Alvos Acima do Tempo</th>
				<th colspan=2 style='text-align:center'>Total Alvos</th>
				<th></th>
			</tr>
			<tr>
				<th><?= $this->Html->link($agrupamento[$this->data['TViagViagem']['agrupamento']], 'javascript:void(0)') ?></th>
				<th class='numeric input-small'><?= $this->Html->link('SMs', 'javascript:void(0)') ?></th>
				<th class='numeric input-small'><?= $this->Html->link('Entregando', 'javascript:void(0)') ?></th>
				<th class='numeric input-small'><?= $this->Html->link('Entregue', 'javascript:void(0)') ?></th>
				<th class='numeric input-small'><?= $this->Html->link('Entregando', 'javascript:void(0)') ?></th>
				<th class='numeric input-small'><?= $this->Html->link('Entregue', 'javascript:void(0)') ?></th>
				<th class='numeric input-small'><?= $this->Html->link('Tempo Médio', 'javascript:void(0)') ?></th>
			</tr>
		</thead>
		<tbody>
			<?php $total_sm = 0 ?>
			<?php $total_maximo_entregando = 0 ?>
			<?php $total_maximo_entregue = 0 ?>
			<?php $total_entregando = 0 ?>
			<?php $total_entregue = 0 ?>
			<?php $total_tempo_total = 0 ?>
			<?php $total_quantidade_entregas = 0 ?>
			<?php foreach ($dados as $dado): ?>
				<?php if ($dado[0]['qtd_acima_maximo_entregando'] + $dado[0]['qtd_acima_maximo_entregue'] + $dado[0]['qtd_dentro_maximo_entregando'] + $dado[0]['qtd_dentro_maximo_entregue'] > 0): ?>
					<?php $total_sm += $dado[0]['qtd_sm'] ?>
					<?php $total_maximo_entregando += $dado[0]['qtd_acima_maximo_entregando'] ?>
					<?php $total_maximo_entregue += $dado[0]['qtd_acima_maximo_entregue'] ?>
					<?php $total_entregando += $dado[0]['qtd_acima_maximo_entregando'] + $dado[0]['qtd_dentro_maximo_entregando'] ?>
					<?php $total_entregue += $dado[0]['qtd_acima_maximo_entregue'] + $dado[0]['qtd_dentro_maximo_entregue'] ?>
					<?php $tempo_medio = ($dado[0]['quantidade_entregas'] > 0 ? $dado[0]['tempo_total'] / $dado[0]['quantidade_entregas'] : $dado[0]['quantidade_entregas']) ?>
					<?php $total_tempo_total += $dado[0]['tempo_total'] ?>
					<?php $total_quantidade_entregas += $dado[0]['quantidade_entregas'] ?>
					<?php $qtd_entregando = $this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregando'], array('nozero' => true, 'places' => 0)) ?>
					<?php $qtd_entregue = $this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregue'], array('nozero' => true, 'places' => 0)) ?>
					<?php $qtd_total_entregando = $this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregando'] + $dado[0]['qtd_dentro_maximo_entregando'], array('nozero' => true, 'places' => 0)) ?>
					<?php $qtd_total_entregue = $this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregue'] + $dado[0]['qtd_dentro_maximo_entregue'], array('nozero' => true, 'places' => 0)) ?>
					<tr>
						<td><?= $dado[0]['descricao_agrupamento'] ?></td>
						<td class='numeric input-small'><?= $dado[0]['qtd_sm'] ?></td>
						<td class='numeric input-small'><?= empty($dado[0]['codigo_agrupamento']) ? $qtd_entregando : $this->Html->link($qtd_entregando, 'javascript:void(0)', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '{$dado[0]['codigo_agrupamento']}', '".TVlocViagemLocal::STATUS_ALVO_ENTREGANDO."','','".TVlocViagemLocal::STATUS_PERMANENCIA_ACIMA."')" )) ?></td>
						<td class='numeric input-small'><?= empty($dado[0]['codigo_agrupamento']) ? $qtd_entregue : $this->Html->link($qtd_entregue, 'javascript:void(0)', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '{$dado[0]['codigo_agrupamento']}', '".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."', '', '".TVlocViagemLocal::STATUS_PERMANENCIA_ACIMA."')" )) ?></td>
						<td class='numeric input-small'><?= empty($dado[0]['codigo_agrupamento']) ? $qtd_total_entregando : $this->Html->link($qtd_total_entregando, 'javascript:void(0)', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '{$dado[0]['codigo_agrupamento']}', '".TVlocViagemLocal::STATUS_ALVO_ENTREGANDO."')" )) ?></td>
						<td class='numeric input-small'><?= empty($dado[0]['codigo_agrupamento']) ? $qtd_total_entregue : $this->Html->link($qtd_total_entregue, 'javascript:void(0)', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '{$dado[0]['codigo_agrupamento']}', '".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."')" )) ?></td>
						<td class='numeric input-small'><?= Comum::convertToHoursMins($tempo_medio) ?></td>
					</tr>
				<?php endif ?>
			<?php endforeach ?>
			<tfoot>
				<tr>
					<td>Total</td>
					<td class='numeric input-small'>
						<?php if ($this->data['TViagViagem']['agrupamento'] == 1): ?> 
							<?= $total_sm ?>
						<?php else: ?>
							<label title='Não somado pois uma viagem pode ter multiplas bandeiras, regiões e/ou lojas'>--</label>
						<?php endif ?>
					</td>
					<td class='numeric input-small'><?= $this->Html->link($total_maximo_entregando, 'javascript:void(0)', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '', '".TVlocViagemLocal::STATUS_ALVO_ENTREGANDO."', '', '".TVlocViagemLocal::STATUS_PERMANENCIA_ACIMA."')" )) ?></td>
					<td class='numeric input-small'><?= $this->Html->link($total_maximo_entregue, 'javascript:void(0)', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '', '".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."', '', '".TVlocViagemLocal::STATUS_PERMANENCIA_ACIMA."')" )) ?></td>
					<td class='numeric input-small'><?= $this->Html->link($total_entregando, 'javascript:void(0)', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '', '".TVlocViagemLocal::STATUS_ALVO_ENTREGANDO."')" )) ?></td>
					<td class='numeric input-small'><?= $this->Html->link($total_entregue, 'javascript:void(0)', array( 'onclick' => "alvo_sintetico_analitico2(filtros(), '', '".TVlocViagemLocal::STATUS_ALVO_ENTREGUE."')" )) ?></td>
					<td class='numeric input-small'><?= Comum::convertToHoursMins($total_quantidade_entregas > 0 ? ($total_tempo_total / $total_quantidade_entregas) : 0) ?></td>
				</tr>
			</tfoot>
		</tbody>
	</table>
	<div id='veiculos'></div>
	<?= $this->addScript($this->Javascript->codeBlock("
		jQuery(document).ready(function(){
			var div = jQuery('div#veiculos');
			$.ajax({
				type: 'POST',
				url: '/portal/viagens/tempo_maximo_sintetico_veiculos',
				beforeSend : function(){
					bloquearDiv(div);
				},
				success : function(data){
					div.html(data);
					div.unblock();
				},
				error : function(){
					div.unblock();
				}
			});
		});
	")) ?>
<?php endif ?>
