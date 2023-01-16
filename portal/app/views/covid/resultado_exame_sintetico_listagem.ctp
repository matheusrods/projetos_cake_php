<?php if (!$dados): ?>
	<div class="alert">
		Defina os critérios de filtros.
	</div>
<?php else: ?>
	<div class="row">
		<div id="grafico_resultado_exame" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
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
	        form.setAttribute('action', '/portal/covid/resultado_exame_analitico/1/' + Math.random());

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[UsuarioGca][codigo_cliente]');
	        field.setAttribute('value', '{$this->data['UsuarioGca']['codigo_cliente']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[UsuarioGca][data_inicio]');
	        field.setAttribute('value', '{$this->data['UsuarioGca']['data_inicio']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[UsuarioGca][data_fim]');
	        field.setAttribute('value', '{$this->data['UsuarioGca']['data_fim']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);

	        field = document.createElement('input');
	        field.setAttribute('name', 'data[UsuarioGca][tipo_periodo]');
	        field.setAttribute('value', '{$this->data['UsuarioGca']['tipo_periodo']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);
	       
            field = document.createElement('input');
            field.setAttribute('name', 'data[UsuarioGca][codigo_cliente_alocacao]');     
            field.setAttribute('value', codigo_selecionado);
            field.setAttribute('value', agrupamento == 1 ? codigo_selecionado : '{$this->data['UsuarioGca']['codigo_cliente_alocacao']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[UsuarioGca][codigo_setor]');     
            field.setAttribute('value', agrupamento == 2 ? codigo_selecionado : '{$this->data['UsuarioGca']['codigo_setor']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[UsuarioGca][codigo_cargo]');     
            field.setAttribute('value', agrupamento == 3 ? codigo_selecionado : '{$this->data['UsuarioGca']['codigo_cargo']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[UsuarioGca][tipo_periodo]');     
            field.setAttribute('value', codigo_selecionado);
            field.setAttribute('value', agrupamento == 4 ? codigo_selecionado : '1,2');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);

            field = document.createElement('input');
            field.setAttribute('name', 'data[UsuarioGca][codigo_funcionario]');     
            field.setAttribute('value', '{$this->data['UsuarioGca']['codigo_funcionario']}');
            field.setAttribute('type', 'hidden');
            form.appendChild(field);
	               
	        var janela = window_sizes();
	        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	        document.body.appendChild(form);
	        form.submit();

	    }"
	);?>
	<?php echo $this->Javascript->codeBlock(

	"Highcharts.setOptions({colors: [".$resultado."]});" .

	$this->Highcharts->render(array(), $series, array(
	    'title' => '',
	    'renderTo' => 'grafico_resultado_exame',
	    'chart' => array('type' => 'pie'),
	    'legend' => array('labelFormatter' => 'function() { return this.name + " - " + this.y; }'),
	    'plotOptions' => array(
	    	'pie' => array('showInLegend'=>true),
	    	'series' => array(
	    		'dataLabels' => array(
	    			'color' => array(
	    				'#00FF00', // verde
						'#FF0000' // vermelho
	    			)
	    		)
	    	)
	    ),
	    'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'), 'printButton' => array('enabled'=> 'false')))
	))

	); ?>
<?php endif ?>