
<style>
	.chart_content .span3 {
		width: 290px !important;
	}
</style>

<?php if (!$dados): ?>
	<div class="alert">
		Defina os critérios de filtros.
	</div>
<?php else: ?>
	
	<?php if ($filtro_resultado == true):?>

		<div class="row-fluid margin-top-30 chart_content" style="margin-bottom: 20px; align-items: center;display: flex;flex-direction: row;flex-wrap: wrap;justify-content: center">
	
			<?php 

			$total_normal_alterado = 0;
			$qtd_normal = 0;
			$qtd_alterado = 0;
			$total_positivo_negativo = 0;
			$qtd_positivo = 0;
			$qtd_negativo = 0;
			$total_detectado_n_detectado = 0;
			$qtd_detectado = 0;
			$qtd_n_detectado = 0;

			foreach($dados as $key => $value) {

				if ($value[0]['codigo'] == 1 || $value[0]['codigo'] == 2) {
					$total_normal_alterado += $value[0]['quantidade'];	

					if ($value[0]['codigo'] == 1) {
						$qtd_normal =  $value[0]['quantidade'];
					}
					if ($value[0]['codigo'] == 2) {
						$qtd_alterado =  $value[0]['quantidade'];
					}
				}

				if ($value[0]['codigo'] == 3 || $value[0]['codigo'] == 4) {
					$total_positivo_negativo += $value[0]['quantidade'];	

					if ($value[0]['codigo'] == 3) {
						$qtd_positivo =  $value[0]['quantidade'];
					}
					if ($value[0]['codigo'] == 4) {
						$qtd_negativo =  $value[0]['quantidade'];
					}
				}	
				
				if ($value[0]['codigo'] == 5 || $value[0]['codigo'] == 6) {
					$total_detectado_n_detectado += $value[0]['quantidade'];	

					if ($value[0]['codigo'] == 5) {
						$qtd_detectado =  $value[0]['quantidade'];
					}
					if ($value[0]['codigo'] == 6) {
						$qtd_n_detectado =  $value[0]['quantidade'];
					}
				}
			}
			
			//Porcentagem nomal/alterado
			$per_normal = $qtd_normal > 0 ? ($qtd_normal * 100) / $total_normal_alterado : 0;
			$per_alterado = $qtd_alterado > 0 ? ($qtd_alterado * 100) / $total_normal_alterado : 0;
			//Porcentagem positivo/negativo
			$per_positivo = $qtd_positivo > 0 ? ($qtd_positivo * 100) / $total_positivo_negativo : 0;
			$per_negativo = $qtd_negativo > 0 ? ($qtd_negativo * 100) / $total_positivo_negativo : 0;
			//Porcentagem detectado/não detectado
			$per_detectado = $qtd_detectado > 0 ? ($qtd_detectado * 100) / $total_detectado_n_detectado : 0;
			$per_n_detectado = $qtd_n_detectado > 0 ? ($qtd_n_detectado * 100) / $total_detectado_n_detectado : 0;

			?>	
			
			<?php if ($per_normal > 0 || $per_alterado > 0) :?>
				<div class="span3">
					<div class="font-size-18 text-center">
						<strong>NORMAL / ALTERADO</strong>
					</div>

					<div class="margin-top-10">

						<?php
							if ($qtd_normal == $qtd_alterado) {
								?>
									<div class="ponteiro-background" style="background-image: url('/portal/img/todosbem/dashboard/centro.png'); background-repeat: no-repeat;height: 156px;background-size: 290px 156px;"></div>
								<?php
							} elseif ($qtd_normal > $qtd_alterado) {
								?>
									<div class="ponteiro-background" style="background-image: url('/portal/img/todosbem/dashboard/verde.png'); background-repeat: no-repeat;height: 156px;background-size: 290px 156px;"></div>
								<?php
							} else {
								?>
									<div class="ponteiro-background" style="background-image: url('/portal/img/todosbem/dashboard/vermelho.png'); background-repeat: no-repeat;height: 156px;background-size: 290px 156px;"></div>
								<?php
							}
						
						?>
						
						<div class="blocked color-gray text-center font-size-18 margin-bottom-10">Total: <?= $total_normal_alterado;?></div>
						
						<div class="blocked" data-toggle="tooltip" data-html="true" title="" data-original-title="<span class='font-size-16'>Quantidade de colaboradores<br>1</span>">
							<div class="pull-left font-size-16 background-color-green color-white padding-10 text-center" style="width: 15%"><strong><?= round($per_normal); ?>%</strong></div>
							<div class="pull-left background-leafgreen color-white padding-10" style="width: 70%"><strong>NORMAL</strong></div>
						</div>   
						
						<div class="blocked" data-toggle="tooltip" data-html="true" title="" data-original-title="<span class='font-size-16'>Quantidade de colaboradores<br>7</span>">
							<div class="pull-left font-size-16 background-color-red color-white padding-10 text-center" style="width: 15%"><strong><?= round($per_alterado);?>%</strong></div>
							<div class="pull-left background-color-indianred color-white padding-10" style="width: 70%"><strong>ALTERADO</strong></div>
						</div>		
					</div>
				</div> 
			<?php endif;?>

			<?php if ($qtd_positivo > 0 || $qtd_negativo > 0) :?>
				<div class="span3">
					<div class="font-size-18 text-center">
						<strong>POSITIVO / NEGATIVO</strong>
					</div>

					<div class="margin-top-10">
						<?php
							if ($qtd_positivo == $qtd_negativo) {
								?>
									<div class="ponteiro-background" style="background-image: url('/portal/img/todosbem/dashboard/centro.png'); background-repeat: no-repeat;height: 156px;background-size: 290px 156px;"></div>
								<?php
							} elseif ($qtd_positivo > $qtd_negativo) {
								?>
									<div class="ponteiro-background" style="background-image: url('/portal/img/todosbem/dashboard/verde.png'); background-repeat: no-repeat;height: 156px;background-size: 290px 156px;"></div>
								<?php
							} else {
								?>
									<div class="ponteiro-background" style="background-image: url('/portal/img/todosbem/dashboard/vermelho.png'); background-repeat: no-repeat;height: 156px;background-size: 290px 156px;"></div>
								<?php
							}			
						?>
						
						<div class="blocked color-gray text-center font-size-18 margin-bottom-10">Total: <?= $total_positivo_negativo;?></div>
						
						<div class="blocked" data-toggle="tooltip" data-html="true" title="" data-original-title="<span class='font-size-16'>Quantidade de colaboradores<br>1</span>">
							<div class="pull-left font-size-16 background-color-green color-white padding-10 text-center" style="width: 15%"><strong><?= round($per_positivo); ?>%</strong></div>
							<div class="pull-left background-leafgreen color-white padding-10" style="width: 70%"><strong>POSITIVO</strong></div>
						</div>   
						
						<div class="blocked" data-toggle="tooltip" data-html="true" title="" data-original-title="<span class='font-size-16'>Quantidade de colaboradores<br>7</span>">
							<div class="pull-left font-size-16 background-color-red color-white padding-10 text-center" style="width: 15%"><strong><?= round($per_negativo);?>%</strong></div>
							<div class="pull-left background-color-indianred color-white padding-10" style="width: 70%"><strong>NEGATIVO</strong></div>
						</div>		
					</div>
				</div> 
			<?php endif;?>

			<?php if ($qtd_detectado > 0 || $qtd_n_detectado > 0) :?>
				<div class="span3">
					<div class="font-size-18 text-center">
						<strong>DETECTADO / NÃO DETECTADO</strong>
					</div>

					<div class="margin-top-10">
						<?php
							if ($qtd_detectado == $qtd_n_detectado) {
								?>
									<div class="ponteiro-background" style="background-image: url('/portal/img/todosbem/dashboard/centro.png'); background-repeat: no-repeat;height: 156px;background-size: 290px 156px;"></div>
								<?php
							} elseif ($qtd_detectado > $qtd_n_detectado) {
								?>
									<div class="ponteiro-background" style="background-image: url('/portal/img/todosbem/dashboard/verde.png'); background-repeat: no-repeat;height: 156px;background-size: 290px 156px;"></div>
								<?php
							} else {
								?>
									<div class="ponteiro-background" style="background-image: url('/portal/img/todosbem/dashboard/vermelho.png'); background-repeat: no-repeat;height: 156px;background-size: 290px 156px;"></div>
								<?php
							}			
						?>
						
						<div class="blocked color-gray text-center font-size-18 margin-bottom-10">Total: <?= $total_detectado_n_detectado;?></div>
						
						<div class="blocked" data-toggle="tooltip" data-html="true" title="" data-original-title="<span class='font-size-16'>Quantidade de colaboradores<br>1</span>">
							<div class="pull-left font-size-16 background-color-green color-white padding-10 text-center" style="width: 15%"><strong><?= round($per_detectado); ?>%</strong></div>
							<div class="pull-left background-leafgreen color-white padding-10" style="width: 70%"><strong>DETECTADO</strong></div>
						</div>   
						
						<div class="blocked" data-toggle="tooltip" data-html="true" title="" data-original-title="<span class='font-size-16'>Quantidade de colaboradores<br>7</span>">
							<div class="pull-left font-size-16 background-color-red color-white padding-10 text-center" style="width: 15%"><strong><?= round($per_n_detectado);?>%</strong></div>
							<div class="pull-left background-color-indianred color-white padding-10" style="width: 70%"><strong>NÃO DETECTADO</strong></div>
						</div>		
					</div>
				</div>
			<?php endif;?>

		</div>

	<?php endif; ?>	

	<div class="row" style="<?php echo $filtro_resultado == true ? 'display: none;' : 'display: block;' ?>">
		<div id="grafico_baixa_exame" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
	</div>

	<table class="table table-striped">
	    <thead>
	        <tr>
				<td class='input-small'>Código</td>
				<td>Descrição</td>
				<td class='numeric input-small'>Quantidade</td>
			</tr>
		</thead>
		<tbody>
			<?php $total = 0 ?>
			<?php $series = array() ?>
			<?php foreach($dados as $key => $value) : ?>
				<?php $total += $value[0]['quantidade'] ?>
				<?php $series[] = array('name' => '"'.str_replace('"', "'", $value[0]['descricao']).'"', 'values' => $value[0]['quantidade']) ?>
				<tr>
					<td class='input-small'><?php echo $value['0']['codigo']; ?></td>
					<td><?php echo $value['0']['descricao']; ?></td>
					<?php $codigo = empty($value['0']['codigo']) ? -1 : $value['0']['codigo'] ?>
					<td class='numeric input-small'><?= $this->Html->link($value[0]['quantidade'], "javascript:analitico('{$codigo}')") ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td>Total</td>
				<td></td>
				<td class='numeric'><?= $this->Html->link($total, "javascript:analitico('')") ?></td>
			</tr>
		</tfoot>
	</table>
	<?php echo $this->Javascript->codeBlock("
	    function analitico(codigo_selecionado) {
	        var agrupamento = {$agrupamento}; 
	    
	        var form = document.createElement('form');
	        var form_id = ('formresult' + Math.random()).replace('.','');
	        form.setAttribute('method', 'post');
	        form.setAttribute('target', form_id);
	        form.setAttribute('action', '/portal/consulta_pedidos_exames/resultado_exames/1/' + Math.random());

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[PedidoExame][codigo_cliente]');
	        field.setAttribute('value', '{$this->data['PedidoExame']['codigo_cliente']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[PedidoExame][data_inicio]');
	        field.setAttribute('value', '{$this->data['PedidoExame']['data_inicio']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[PedidoExame][data_fim]');
	        field.setAttribute('value', '{$this->data['PedidoExame']['data_fim']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[PedidoExame][tipo_periodo]');
	        field.setAttribute('value', '{$this->data['PedidoExame']['tipo_periodo']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);
	       
            field = document.createElement('input');
            field.setAttribute('name', 'data[PedidoExame][codigo_cliente_alocacao]');     
            field.setAttribute('value', codigo_selecionado);
            field.setAttribute('value', agrupamento == 1 ? codigo_selecionado : '{$this->data['PedidoExame']['codigo_cliente_alocacao']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[PedidoExame][codigo_setor]');     
            field.setAttribute('value', agrupamento == 2 ? codigo_selecionado : '{$this->data['PedidoExame']['codigo_setor']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[PedidoExame][codigo_cargo]');     
            field.setAttribute('value', agrupamento == 3 ? codigo_selecionado : '{$this->data['PedidoExame']['codigo_cargo']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[PedidoExame][tipo_exame]');     
            field.setAttribute('value', agrupamento == 4 ? codigo_selecionado : '');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[PedidoExame][codigo_funcionario]');     
            field.setAttribute('value', '{$this->data['PedidoExame']['codigo_funcionario']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);
	               
	        var janela = window_sizes();
	        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	        document.body.appendChild(form);
	        form.submit();

	    }"
	);?>
	<?php echo $this->Javascript->codeBlock($this->Highcharts->render(array(), $series, array(
	    'title' => '',
	    'renderTo' => 'grafico_baixa_exame',
	    'chart' => array('type' => 'pie'),
	    'legend' => array('labelFormatter' => 'function() { return this.name + " - " + this.y; }'),
	    'plotOptions' => array('pie' => array('showInLegend'=>true)),
	    'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'), 'printButton' => array('enabled'=> 'false')))
	))); ?>
<?php endif ?>