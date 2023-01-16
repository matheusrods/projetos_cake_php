<div class='well'>
	<?php echo $this->BForm->create('ClienteProduto', array('autocomplete' => 'off', 'url' => array('controller' => 'clientes_produtos', 'action' => 'estatistica_cancelamento'))) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('mes', array('label' => false, 'class' => 'input-medium', 'options' => $meses, 'empty' => 'Selecione um mês')) ?>
		<?php echo $this->BForm->input('ano', array('label' => false, 'class' => 'input-medium', 'options' => $anos,  'empty' => 'Selecione um ano')) ?>
		<?php echo $this->BForm->input('codigo_produto', array('label' => false, 'class' => 'input-xlarge', 'options' => $produtos, 'empty' => 'Produto')) ?>
		<?php echo $this->BForm->input('codigo_motivo_cancelamento', array('label' => false, 'class' => 'input-xxlarge', 'options' => $motivos, 'empty' => 'Motivo')) ?>
	</div>
	<span class="label label-info">Agrupar por:</span>
	<div class="row-fluid inline">
		<div id='agrupamento'>
			<?php echo $this->BForm->input('agrupamento', array('type' => 'radio', 'options' => $agrupamento, 'default' => 1, 'legend' => false, 'label' => array('class' => 'radio inline input-small'))) ?>
		</div>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end() ?>
</div>
<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php if( isset( $dadosGrafico ) && !empty( $dadosGrafico ) ): ?>
    <div id="show-chart" style="min-width: 400px; height: 400px; margin: 0 auto 50px">
        <?php $this->addScript($this->Buonny->link_js('highcharts/highcharts')) ?>
        <?php $this->addScript($this->Buonny->link_js('highcharts/modules/exporting')) ?>
        <?php echo $this->Javascript->codeBlock($this->Highcharts->render($dadosGrafico['eixo_x'], $dadosGrafico['series'], array(
				'renderTo' => 'show-chart',
				'chart' => array('type' => 'column'),
				'plotOptions' => array(
					'series' => array(
						'stacking' => 'normal'
					)
				),
				'xAxis' => array(
					'labels' => array(
						'rotation' => -45,
						'y' => 15,
					)
				),
				'yAxis' => array(
		            'allowDecimals' => false,
			        'min' => 0,
	                'title' => 'Número de cancelamentos'
	            ),
			)
        )); ?>
    </div>
<?php endif; ?>
<?php if (isset($dados)): ?>
<?php $titulo = isset($dados[0]['MotivoCancelamento']) ? 'Motivo': 'Produto'; ?>
	<table class='table table-striped tablesorter'>
        <thead>
            <th><?php echo $this->Html->link($titulo, 'javascript:void(0)') ?></th>
            <th class='numeric'><?php echo $this->Html->link('Clientes de '.(!empty($mes) ? $mes.' de ': '').$ano[1], 'javascript:void(0)') ?></th>
        <th class='numeric'><?php echo $this->Html->link('Clientes de '.(!empty($mes) ? $mes.' de ': '').$ano[0], 'javascript:void(0)') ?></th>
			<th class='numeric'><?php echo $this->Html->link('Diferença percentual', 'javascript:void(0)') ?></th>
        </thead>
		<tbody>
			<?php
			$totalAnoAnterior	 = 0;
			$totalAnoSelecionado = 0;
			foreach ($dados as $dado):
				$diferenca		 = 0;
				$negative		 = 0;
			?>
				<?php if($dado[0]["{$ano[0]}"] != 0 && $dado[0]["{$ano[1]}"] != 0): ?>
								<?php if($dado[0]["{$ano[0]}"] > $dado[0]["{$ano[1]}"]): ?>
									<?php $diferenca = (100 * $dado[0]["{$ano[0]}"]) / $dado[0]["{$ano[1]}"]; ?>
									<?php $diferenca = 100 - $diferenca; ?>
								<?php elseif($dado[0]["{$ano[0]}"] < $dado[0]["{$ano[1]}"]): ?>
									<?php $diferenca = ($dado[0]["{$ano[1]}"] / $dado[0]["{$ano[0]}"]) * 100; ?>
									<?php if ($diferenca > 0): ?>
										<?php $diferenca = $diferenca - 100; ?>
									<?php endif ?>
								<?php endif ?>
				<?php endif ?>
				<?php $negative = ($dado[0]["{$ano[0]}"] > $dado[0]["{$ano[1]}"] && $diferenca != 0) ?>
				<tr>
					<td><?php echo $dado[$titulo == 'Produto' ? 'Produto': 'MotivoCancelamento']['descricao'] ?></td>
					<td class='numeric'><?php echo $dado[0]["{$ano[1]}"] == 0 ? '': $this->Html->link($dado[0]["{$ano[1]}"], 'javascript:void(0)', array('onclick' => "estatistica_analitico(".$ano[1]." , '".$dado[($titulo == 'Produto' ? 'Produto': 'MotivoCancelamento')]['descricao']."')"));?></td>
					<td class='numeric'><?php echo $dado[0]["{$ano[0]}"] == 0 ? '' : $this->Html->link($dado[0]["{$ano[0]}"], 'javascript:void(0)', array('onclick' => "estatistica_analitico(".$ano[0]." , '".$dado[($titulo == 'Produto' ? 'Produto': 'MotivoCancelamento')]['descricao']."')"));?></td>
					<td class="numeric<?= ($negative ? ' negative_value' : '') ?>"><?= $diferenca === 0 ? '': round($diferenca) ?></td>
				</tr>
			<?php
				$totalAnoAnterior	 += $dado[0]["{$ano[1]}"];
				$totalAnoSelecionado += $dado[0]["{$ano[0]}"];
				$ano_selecionado = $ano[0];
				$ano_anterior = $ano[1];
			endforeach;
			?>
		</tbody>
        <tfoot>
            <tr>
                <td><strong>Total:</strong></td>
                <td class = 'numeric'><?php echo $totalAnoAnterior == 0 ? '': $this->Html->link($totalAnoAnterior, 'javascript:void(0)', array('onclick' => "estatistica_analitico(".$ano_anterior.")")); ?></td>
                <td class = 'numeric'><?php echo $totalAnoSelecionado == 0 ? '': $this->Html->link($totalAnoSelecionado, 'javascript:void(0)', array('onclick' => "estatistica_analitico(".$ano_selecionado.")")); ?></td>
                <td></td>
            </tr>
        </tfoot>
	</table>
<?php endif ?>
<?php $this->addScript($this->Buonny->link_js('solicitacoes_monitoramento')) ?>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        jQuery(\'table.table\').tablesorter({
            sortList: [0]
        });
		init_combo_events();
		setup_datepicker();

		


    });', false);
?>
<script type="text/javascript">
	function estatistica_analitico(ano, produto) {
			var field = null;
			var form = document.createElement("form");
			var form_id = ("formresult" + Math.random()).replace(".","");
			form.setAttribute("method", "post");
			form.setAttribute("target", form_id);
			form.setAttribute("action", "/portal/clientes_produtos/estatisca_analitico_cliente_cancelamento/" + Math.random());
			field = document.createElement("input");
			field.setAttribute("name", "data[ClienteProduto][ano]");
			field.setAttribute("value", ano);
			field.setAttribute("type", "hidden");
			form.appendChild(field);
			field = document.createElement("input");
			field.setAttribute("name", "data[ClienteProduto][codigo_motivo_cancelamento]");
			field.setAttribute("value", <?php echo (!empty($this->data['ClienteProduto']['codigo_motivo_cancelamento'])?$this->data['ClienteProduto']['codigo_motivo_cancelamento']:"''") ?>);
			field.setAttribute("type", "hidden");
			form.appendChild(field);
			field = document.createElement("input");
			field.setAttribute("name", "data[ClienteProduto][mes]");
			field.setAttribute("value", <?php echo (!empty($this->data['ClienteProduto']['mes'])?$this->data['ClienteProduto']['mes']:"''") ?>);
			field.setAttribute("type", "hidden");
			form.appendChild(field);
			field = document.createElement("input");
			field.setAttribute("name", "data[ClienteProduto][codigo_produto]");
			field.setAttribute("value", <?php echo (!empty($this->data['ClienteProduto']['codigo_produto'])?$this->data['ClienteProduto']['codigo_produto']:"''") ?>);
			field.setAttribute("type", "hidden");
			form.appendChild(field);
			field = document.createElement("input");
			field.setAttribute("name", "data[ClienteProduto][agrupamento]");
			field.setAttribute("value", <?php echo (!empty($this->data['ClienteProduto']['agrupamento'])?$this->data['ClienteProduto']['agrupamento']:"''") ?>);
			field.setAttribute("type", "hidden");
			form.appendChild(field);
			field = document.createElement("input");
			field.setAttribute("name", "data[ClienteProduto][descricao_produto]");
			field.setAttribute("value", produto);
			field.setAttribute("type", "hidden");
			form.appendChild(field);
			var janela = window_sizes();
			window.open("",form_id, "scrollbars=yes,menubar=no,height="+(janela.height-200)+",width="+(janela.width-20)+",resizable=yes,toolbar=no,status=no");
			document.body.appendChild(form);
			form.submit();
		}
</script>