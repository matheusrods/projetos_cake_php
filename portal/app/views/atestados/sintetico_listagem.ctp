<?php if (!$dados): ?>
	<div class="alert">
		Defina os critérios de filtros.
	</div>
<?php else: ?>
	<div class="row">
		<div id="grafico_absenteismo" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
	</div>
	<table class="table table-striped">
	    <thead>
	        <tr>
				<td class='input-small'>Código</td>
				<td>Descricao</td>
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
	        form.setAttribute('action', '/portal/atestados/analitico/1/' + Math.random());

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[Atestado][codigo_cliente]');
	        field.setAttribute('value', ".json_encode($this->data['Atestado']['codigo_cliente']).");
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[Atestado][data_inicio]');
	        field.setAttribute('value', '{$this->data['Atestado']['data_inicio']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[Atestado][data_fim]');
	        field.setAttribute('value', '{$this->data['Atestado']['data_fim']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[Atestado][tipo_periodo]');
	        field.setAttribute('value', '{$this->data['Atestado']['tipo_periodo']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);
	       
            field = document.createElement('input');
            field.setAttribute('name', 'data[Atestado][codigo_cliente_alocacao]');     
            field.setAttribute('value', codigo_selecionado);
            field.setAttribute('value', agrupamento == 1 ? codigo_selecionado : '{$this->data['Atestado']['codigo_cliente_alocacao']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[Atestado][codigo_setor]');     
            field.setAttribute('value', agrupamento == 2 ? codigo_selecionado : '{$this->data['Atestado']['codigo_setor']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[Atestado][codigo_cargo]');     
            field.setAttribute('value', agrupamento == 3 ? codigo_selecionado : '{$this->data['Atestado']['codigo_cargo']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[Atestado][codigo_funcionario]');     
            field.setAttribute('value', '{$this->data['Atestado']['codigo_funcionario']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[Atestado][codigo_cid]');     
            field.setAttribute('value', agrupamento == 4 ? codigo_selecionado : '{$this->data['Atestado']['codigo_cid']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[Atestado][codigo_medico]');     
            field.setAttribute('value', agrupamento == 5 ? codigo_selecionado : '{$this->data['Atestado']['codigo_medico']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[Atestado][nome_medico]');     
            field.setAttribute('value', agrupamento == 5 ? codigo_selecionado : '{$this->data['Atestado']['nome_medico']}');
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
	    'renderTo' => 'grafico_absenteismo',
	    'chart' => array('type' => 'pie'),
	    'legend' => array('labelFormatter' => 'function() { return this.name + " - " + this.y; }'),
	    'plotOptions' => array('pie' => array('showInLegend'=>true)),
	    'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'), 'printButton' => array('enabled'=> 'false')))
	))); ?>
<?php endif ?>