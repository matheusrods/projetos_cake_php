<?php if(!empty($checklists)):?>
	<div id="grafico" style="min-width: 400px; height: 400px; margin: 0 auto 50px"></div>
	<table class='table table-striped'>
		<thead>
			<th>Descrição</th>
			<th class='numeric input-small'>Aprovados</th>
			<th class='numeric input-small'>Reprovados</th>
			<th class='numeric input-small'>Recusados</th>
			<th class='numeric input-small'>Total</th>
		</thead>
		<tbody>
			<?php $total_aprovados = 0 ?>
			<?php $total_reprovados = 0 ?>
			<?php $total_recusados = 0 ?>
			<?php if(count($checklists)): ?>
				<?php foreach ($checklists as $key => $checklist): ?>
					<?php $total_aprovados += $checklist['0']['qtd_aprovados'] ?>
					<?php $total_reprovados += $checklist['0']['qtd_reprovados'] ?>
					<?php $total_recusados += $checklist['0']['qtd_recusados'] ?>
					<?php $total = ($checklist['0']['qtd_aprovados'] + $checklist['0']['qtd_reprovados'] + $checklist['0']['qtd_recusados']) ?>
					<?php $codigo_selecionado = empty($checklist['0']['codigo']) ? 'null' : $checklist['0']['codigo'] ?>
					<tr>
						<td><?= $checklist['0']['descricao'] ?></td>
						<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($checklist['0']['qtd_aprovados'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}', '1')") ?></td>
						<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($checklist['0']['qtd_reprovados'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}', '2')") ?></td>
						<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($checklist['0']['qtd_recusados'], array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}', '3')") ?></td>
						<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($total, array('nozero' => true, 'places' => 0)), "javascript:analitico('{$codigo_selecionado}', '')") ?></td>
					</tr>
				<?php endforeach ?>
			<?php else: ?>
				<?php $key = 0 ?>
			<?php endif ?>
		</tbody>
		<tfoot>
			<tr>
				<?php $total = ($total_aprovados + $total_reprovados + $total_recusados) ?>
				<td>Total</td>
				<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($total_aprovados, array('nozero' => true, 'places' => 0)), "javascript:analitico('total', '1')") ?></td>
				<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($total_reprovados, array('nozero' => true, 'places' => 0)), "javascript:analitico('total', '2')") ?></td>
				<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($total_recusados, array('nozero' => true, 'places' => 0)), "javascript:analitico('total', '3')") ?></td>
				<td class='numeric input-small'><?= $this->Html->link($this->Buonny->moeda($total, array('nozero' => true, 'places' => 0)), "javascript:analitico('total', '')") ?></td>
			</tr>
		</tfoot>
	</table>
	<?php 
		if(!isset($this->data['TCveiChecklistVeiculo']['cvei_alvo_valido_refe_codigo'])):
			$this->data['TCveiChecklistVeiculo']['cvei_alvo_valido_refe_codigo'] = NULL;
		endif;
	?>	
	<?php echo $this->Javascript->codeBlock("
		function analitico(codigo_selecionado, codigo_status) {
			var field = null;
			var agrupamento = {$this->data['TCveiChecklistVeiculo']['agrupamento']};
			var form = document.createElement('form');
		    var form_id = ('formresult' + Math.random()).replace('.','');
			form.setAttribute('method', 'post');
			form.setAttribute('target', form_id);
		    form.setAttribute('action', '/portal/checklists/analitico/' + Math.random());
		    
		    if (agrupamento == 1){
		    	field = document.createElement('input');
				field.setAttribute('name', 'data[TCveiChecklistVeiculo][cvei_usuario_adicionou]');
			    field.setAttribute('value', codigo_selecionado);
				field.setAttribute('type', 'hidden');
				form.appendChild(field);
			}	

		    if (agrupamento == 2 && codigo_selecionado != 'total') {
		    	field = document.createElement('input');
				field.setAttribute('name', 'data[TCveiChecklistVeiculo][tran_pess_oras_codigo]');
			    field.setAttribute('value', codigo_selecionado);
				field.setAttribute('type', 'hidden');
				form.appendChild(field);
		    } else {
		    	field = document.createElement('input');
			    field.setAttribute('name', 'data[TCveiChecklistVeiculo][codigo_cliente_transportador]');
			    field.setAttribute('value', '{$this->data['TCveiChecklistVeiculo']['codigo_cliente_transportador']}');
				field.setAttribute('type', 'hidden');
				form.appendChild(field);
		    }
		    if (agrupamento == 3 && codigo_selecionado != 'total') {
		    	field = document.createElement('input');
			    field.setAttribute('name', 'data[TCveiChecklistVeiculo][data_inicial]');
			    field.setAttribute('value', codigo_selecionado);
			    field.setAttribute('type', 'hidden');
			    form.appendChild(field);
			    field = document.createElement('input');
			    field.setAttribute('name', 'data[TCveiChecklistVeiculo][data_final]');
			    field.setAttribute('value', codigo_selecionado);
			    field.setAttribute('type', 'hidden');
			    form.appendChild(field);
		    } else {
			    field = document.createElement('input');
			    field.setAttribute('name', 'data[TCveiChecklistVeiculo][data_inicial]');
			    field.setAttribute('value', '{$this->data['TCveiChecklistVeiculo']['data_inicial']}');
			    field.setAttribute('type', 'hidden');
			    form.appendChild(field);
			    field = document.createElement('input');
			    field.setAttribute('name', 'data[TCveiChecklistVeiculo][data_final]');
			    field.setAttribute('value', '{$this->data['TCveiChecklistVeiculo']['data_final']}');
			    field.setAttribute('type', 'hidden');
			    form.appendChild(field);
		    }
			if (agrupamento == 4 && codigo_selecionado != 'total') {
			    field = document.createElement('input');
			    field.setAttribute('name', 'data[TCveiChecklistVeiculo][refe_codigo]');
			    field.setAttribute('value', codigo_selecionado);
			    field.setAttribute('type', 'hidden');
			    form.appendChild(field);
			} else {
				field = document.createElement('input');
			    field.setAttribute('name', 'data[TCveiChecklistVeiculo][refe_codigo]');
			    field.setAttribute('value', '{$this->data['TCveiChecklistVeiculo']['refe_codigo']}');
			    field.setAttribute('type', 'hidden');
			    form.appendChild(field);
			    field = document.createElement('input');
			    field.setAttribute('name', 'data[TCveiChecklistVeiculo][refe_codigo_visual]');
			    field.setAttribute('value', '{$this->data['TCveiChecklistVeiculo']['refe_codigo_visual']}');
			    field.setAttribute('type', 'hidden');
			    form.appendChild(field);
			}
			if(agrupamento == 5){
				if(codigo_selecionado != 'total') {
				    field = document.createElement('input');
				    field.setAttribute('name', 'data[TCveiChecklistVeiculo][cvei_alvo_valido_refe_codigo]');
				    field.setAttribute('value', (codigo_selecionado) != '' ? codigo_selecionado : '');
				    field.setAttribute('type', 'hidden');
				    form.appendChild(field);
				} else {
					field = document.createElement('input');
				    field.setAttribute('name', 'data[TCveiChecklistVeiculo][cvei_alvo_valido_refe_codigo]');
				    field.setAttribute('value', '{$this->data['TCveiChecklistVeiculo']['cvei_alvo_valido_refe_codigo']}');
				    field.setAttribute('type', 'hidden');
				    form.appendChild(field);
				    field = document.createElement('input');
				    field.setAttribute('name', 'data[TCveiChecklistVeiculo][refe_codigo_visual]');
				    field.setAttribute('value', '{$this->data['TCveiChecklistVeiculo']['refe_codigo_visual']}');
				    field.setAttribute('type', 'hidden');
				    form.appendChild(field);
				}
			}
			

		    form.appendChild(field);
		    field = document.createElement('input');
		    field.setAttribute('name', 'data[TCveiChecklistVeiculo][cvei_usuario_adicionou]');
		    field.setAttribute('value', (agrupamento == 1 && codigo_selecionado != 'total' ? codigo_selecionado : '{$this->data['TCveiChecklistVeiculo']['cvei_usuario_adicionou']}'));
		    field.setAttribute('type', 'hidden');

			field = document.createElement('input');
		    field.setAttribute('name', 'data[TCveiChecklistVeiculo][codigo_cliente]');
		    field.setAttribute('value', '{$this->data['TCveiChecklistVeiculo']['codigo_cliente']}');
		    field.setAttribute('type', 'hidden');
		    
		    form.appendChild(field);
		    field = document.createElement('input');
		    field.setAttribute('name', 'data[TCveiChecklistVeiculo][veic_placa]');
		    field.setAttribute('value', '{$this->data['TCveiChecklistVeiculo']['veic_placa']}');
		    field.setAttribute('type', 'hidden');
		    
		    form.appendChild(field);
		    field = document.createElement('input');
		    field.setAttribute('name', 'data[TCveiChecklistVeiculo][cvei_status]');
		    field.setAttribute('value', codigo_status);
		    field.setAttribute('type', 'hidden');
		    form.appendChild(field);
		   
		    var janela = window_sizes();
		    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
		    document.body.appendChild(form);
		    form.submit();
		} ".
		$this->Highcharts->render(array(), $series, array(
		    'title' => '',
		    'renderTo' => 'grafico',
		    'chart' => array('type' => 'pie'),
			'legend' => array('labelFormatter' => 'function() { return this.name + " - " + this.y; }'),
			'plotOptions' => array('pie' => array('showInLegend'=>true, 'animation' => "false")),
			'exporting' => array('buttons' => array('exportButton' => array('enabled'=> 'false'), 'printButton' => array('enabled'=> 'false')))
	))); ?>
<?php else: ?>
	<div class="alert alert-warning">Nenhum registro encontrado</div>
<?php endif;?>	