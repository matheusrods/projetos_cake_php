<?php if ($titulos_a_receber): ?>
	<table class="table table-striped table-bordered">
		<thead>
			<tr>
				<th class='input-small'>Código</th>
				<th>Descrição</th>
				<th class='input-medium numeric'>Em Aberto</th>
				<th class='input-medium numeric'>Pago</th>
				<th class='input-medium numeric'>Total</th>
			</tr>
		</thead>
		<tbody>
			<?php $qtd = 0 ?>
			<?php $total_em_aberto = 0 ?> 
			<?php $total_pago = 0 ?> 
			<?php $total = 0 ?> 
			<?php foreach ($titulos_a_receber as $key => $titulo_a_receber): ?>
				<?php $qtd ++ ?>
				<?php $total_em_aberto += $titulo_a_receber[0]['valor_em_aberto'] ?> 
				<?php $total_pago += $titulo_a_receber[0]['valor_pago'] ?> 
				<?php $total += ($titulo_a_receber[0]['valor_em_aberto'] + $titulo_a_receber[0]['valor_pago']) ?> 
				<tr>
					<td class='input-small'><?= $titulo_a_receber[0]['codigo'] ?></td>
					<td><?= $titulo_a_receber[0]['descricao'] ?></td>
					<td class='input-medium numeric'><?= $this->Html->link($this->Buonny->moeda($titulo_a_receber[0]['valor_em_aberto'], array('nozero' => true)), "javascript:analitico('{$titulo_a_receber[0]['codigo']}', '1')") ?></td>
					<td class='input-medium numeric'><?= $this->Html->link($this->Buonny->moeda($titulo_a_receber[0]['valor_pago'], array('nozero' => true)), "javascript:analitico('{$titulo_a_receber[0]['codigo']}', '2')") ?></td>
					<td class='input-medium numeric'><?= $this->Html->link($this->Buonny->moeda($titulo_a_receber[0]['valor_em_aberto'] + $titulo_a_receber[0]['valor_pago'], array('nozero' => true)), "javascript:analitico('{$titulo_a_receber[0]['codigo']}', 'total')") ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td class='numeric'><?= $qtd ?></td>
				<td></td>
				<td class='input-medium numeric'><?= $this->Html->link($this->Buonny->moeda($total_em_aberto, array('nozero' => true)), "javascript:analitico('','1')") ?></td>
				<td class='input-medium numeric'><?= $this->Html->link($this->Buonny->moeda($total_pago, array('nozero' => true)), "javascript:analitico('','2')") ?></td>
				<td class='input-medium numeric'><?= $this->Html->link($this->Buonny->moeda($total, array('nozero' => true)), "javascript:analitico('','total')") ?></td>
			</tr>
		</tfoot>
	</table>
<?php endif ?>
<?php echo $this->Javascript->codeBlock("
	function analitico(codigo_selecionado, codigo_status) {
		var field = null;
		var agrupamento = {$this->data['Tranrec']['agrupamento']};
		var form = document.createElement('form');
	    var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('target', form_id);
	    form.setAttribute('action', '/portal/transacoes_de_recebimento/analitico/' + Math.random());
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[Tranrec][data_inicial]');
	    field.setAttribute('value', '{$this->data['Tranrec']['data_inicial']}');
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[Tranrec][data_final]');
	    field.setAttribute('value', '{$this->data['Tranrec']['data_final']}');
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[Tranrec][codigo_cliente]');
	    field.setAttribute('value', (agrupamento == 1 && codigo_selecionado != 'total' ? codigo_selecionado : '{$this->data['Tranrec']['codigo_cliente']}'));
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[Tranrec][codigo_corretora]');
	    field.setAttribute('value', (agrupamento == 2 && codigo_selecionado != 'total' ? codigo_selecionado : '{$this->data['Tranrec']['codigo_corretora']}'));
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[Tranrec][codigo_endereco_regiao]');
	    field.setAttribute('value', (agrupamento == 3 && codigo_selecionado != 'total' ? codigo_selecionado : '{$this->data['Tranrec']['codigo_endereco_regiao']}'));
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[Tranrec][codigo_seguradora]');
	    field.setAttribute('value', (agrupamento == 4 && codigo_selecionado != 'total' ? codigo_selecionado : '{$this->data['Tranrec']['codigo_seguradora']}'));
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    field = document.createElement('input');
	    field.setAttribute('name', 'data[Tranrec][status]');
	    field.setAttribute('value', codigo_status);
	    field.setAttribute('type', 'hidden');
	    form.appendChild(field);
	    var janela = window_sizes();
	    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
	    document.body.appendChild(form);
	    form.submit();
	} "); ?>