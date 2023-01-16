<?php if(isset($this->passedArgs[0]) && $this->passedArgs[0] == 'export_cd'):
	header('Content-type: application/vnd.ms-excel');
	header(sprintf('Content-Disposition: attachment; filename="%s"', basename('alvos_parados_em_alvos.csv')));
	header('Pragma: no-cache');
	echo iconv('UTF-8', 'ISO-8859-1', '"CD";"SMs";"Alvos Acima do Tempo (Entregando)";"Alvos Acima do Tempo (Entregue)";"Total alvos (Entregando)";"Total alvos (Entregue)";"Tempo Médio"')."\n";
    $total_sm = 0;
	$total_maximo_entregando = 0;
	$total_maximo_entregue = 0;
	$total_entregando = 0;
	$total_entregue = 0;
	$total_tempo_total = 0;
	$total_quantidade_entregas = 0;
	foreach ($dados as $dado){
		if ($dado[0]['qtd_acima_maximo_entregando'] + $dado[0]['qtd_acima_maximo_entregue'] + $dado[0]['qtd_dentro_maximo_entregando'] + $dado[0]['qtd_dentro_maximo_entregue'] > 0){
			$total_sm += $dado[0]['qtd_sm'];
			$total_maximo_entregando += $dado[0]['qtd_acima_maximo_entregando'];
			$total_maximo_entregue += $dado[0]['qtd_acima_maximo_entregue'];
			$total_entregando += $dado[0]['qtd_acima_maximo_entregando'] + $dado[0]['qtd_dentro_maximo_entregando'];
			$total_entregue += $dado[0]['qtd_acima_maximo_entregue'] + $dado[0]['qtd_dentro_maximo_entregue'];
			$tempo_medio = ($dado[0]['quantidade_entregas'] > 0 ? $dado[0]['tempo_total'] / $dado[0]['quantidade_entregas'] : $dado[0]['quantidade_entregas']);
			$total_tempo_total += $dado[0]['tempo_total'];
			$total_quantidade_entregas += $dado[0]['quantidade_entregas'];
			$qtd_entregando = $this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregando'], array('nozero' => true, 'places' => 0));
			$qtd_entregue = $this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregue'], array('nozero' => true, 'places' => 0));
			$qtd_total_entregando = $this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregando'] + $dado[0]['qtd_dentro_maximo_entregando'], array('nozero' => true, 'places' => 0));
			$qtd_total_entregue = $this->Buonny->moeda($dado[0]['qtd_acima_maximo_entregue'] + $dado[0]['qtd_dentro_maximo_entregue'], array('nozero' => true, 'places' => 0));

	    	$linha  = '"'.$dado[0]['descricao_agrupamento'].'";';
	    	$linha .= '"'.$dado[0]['qtd_sm'].'";';
	    	$linha .= '"'.$qtd_entregando.'";';
			$linha .= '"'.$qtd_entregue.'";';
			$linha .= '"'.$qtd_total_entregando.'";';
			$linha .= '"'.$qtd_total_entregue.'";';
			$linha .= '"'.Comum::convertToHoursMins($tempo_medio).'";';
			$linha .= "\n";
		    echo iconv("UTF-8", "ISO-8859-1", utf8_encode($linha));
 			}
 		}
 		die;
endif;
 ?>
<?php
 	$filtrado = (isset($this->data['TViagViagem']['codigo_cliente']) && $this->data['TViagViagem']['codigo_cliente'] != null
	&& isset($this->data['TViagViagem']['maximo_minutos']) && $this->data['TViagViagem']['maximo_minutos'] != null
	&& isset($this->data['TViagViagem']['data_inicial']) && $this->data['TViagViagem']['data_inicial'] != null
	&& isset($this->data['TViagViagem']['data_final']) && $this->data['TViagViagem']['data_final'] != null);
	?>
<div class='form-procurar'>
	<div class='well'>
	    <h5><?= $this->Html->link(($filtrado ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    	<div id='filtros'>
		    <?php echo $this->BForm->create('TViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'tempo_maximo_sintetico'))) ?>
		    <div class="row-fluid inline">
	            <?php echo $this->Buonny->input_codigo_cliente_base($this, 'codigo_cliente', 'Cliente', false, 'TViagViagem') ?>
		    </div>
		    <div class="row-fluid inline">
		    	<?php echo $this->Buonny->input_periodo($this) ?>
		        <?php echo $this->BForm->input('maximo_minutos', array('label' => false, 'class' => 'input-mini numeric tempo', 'placeholder' => 'Minutos', 'title' => 'Minutos Máximo no Local')) ?>
		        <?php echo $this->BForm->input('status_viagem', array('label' => false, 'class' => 'input-medium', 'options' => $status_viagem, 'empty' => 'Status Viagem')) ?>
		        <?php echo $this->BForm->input('UFOrigem', array('label' => false,'class' => 'input-mini','empty'=>'UF','title'=>'UF Origem', 'options' => $UFOrigem)) ?>
		    </div>
		    <div class="row-fluid inline" id="div-tipo-alvo">
    			<?= $this->Buonny->input_alvos_bandeiras_regioes($this, array_merge($alvos_bandeiras_regioes, array('div' => '#div-tipo-alvo', 'force_model' => 'TViagViagem', 'input_codigo_cliente' => 'codigo_cliente')))?>
    		</div>
		    <div class="row-fluid inline">
				<span class="label label-info">Agrupar por:</span>
	            <div id='agrupamento'>
					<?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
				</div>
			</div>
		    <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		    <?php echo $this->BForm->end();?>
		</div>
	</div>
</div>
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
							<label title='NÃ£o somado pois uma viagem pode ter multiplas bandeiras, regiÃµes e/ou lojas'>--</label>
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
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_js('alvos')) ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
<?php $this->addScript($this->Buonny->link_js('bootstrap-multiselect')); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
    	$.placeholder.shim();
		setup_mascaras();
		jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
		jQuery("table.alvos").tablesorter();
		$(".multiselect-classe-alvo").multiselect({
			maxHeight: 300,
			nonSelectedText: "Classe Alvos",
			numberDisplayed: 1,
			includeSelectAllOption: true
		});
    });', false);
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
<?php endif; ?>