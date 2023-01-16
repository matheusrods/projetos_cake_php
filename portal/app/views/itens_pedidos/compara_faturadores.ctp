<div class='form-procurar'> 
	<div class='well'>
		<?php echo $this->BForm->create('Cliente', array('autocomplete' => 'off', 'url' => array('controller' => 'itens_pedidos', 'action' => 'compara_faturadores'))) ?>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_codigo_cliente($this); ?>
			<?php echo $this->Buonny->input_periodo($this) ?>
			<?php echo $this->BForm->input('produto', array('class' => 'input-medium', 'label' => false, 'options' => array(1 => 'Teleconsult', 2 => 'BuonnySat'), 'empty' => 'Todos')); ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $this->BForm->end();?>
	</div>
</div>
<?php if (isset($diferencas)): ?>
	<table class='table table-striped'>
		<thead>
			<th><?= $this->Html->link('Cód.', 'javascript:void(0)') ?></th>
			<th><?= $this->Html->link('Cliente', 'javascript:void(0)') ?></th>
			<th><?= $this->Html->link('Produto', 'javascript:void(0)') ?></th>
			<th class='numeric'><?= $this->Html->link('Vr.Antigo', 'javascript:void(0)') ?></th>
			<th class='numeric'><?= $this->Html->link('Vr.Novo', 'javascript:void(0)') ?></th>
			<th class='numeric'><?= $this->Html->link('Diferença', 'javascript:void(0)') ?></th>
		</thead>
		<tbody>
			<?php $total_clientes = 0 ?>
			<?php $total_valor_a_pagar_sa = 0 ?>
			<?php $total_valor_a_pagar_sn = 0 ?>
			<?php $total_diferenca = 0 ?>
			<?php if ($diferencas): ?>
				<?php foreach ($diferencas as $diferenca): ?>
					<?php $total_clientes++ ?>
					<?php $total_valor_a_pagar_sa += $diferenca[0]['valor_a_pagar_sa'] ?>
					<?php $total_valor_a_pagar_sn += $diferenca[0]['valor_a_pagar_sn'] ?>
					<?php $total_diferenca += $diferenca[0]['diferenca'] ?>
					<tr>
						<td><?= $this->Html->link($diferenca[0]['codigo'], "javascript:utilizacao_de_servicos('{$diferenca['0']['codigo']}', '{$this->data['Cliente']['data_inicial']}', '{$this->data['Cliente']['data_final']}')") ?></td>
						<td><?= $diferenca[0]['razao_social'] ?></td>
						<td><?= $diferenca[0]['produto'] ?></td>
						<td class='numeric'><?= $this->Buonny->moeda($diferenca[0]['valor_a_pagar_sa']) ?></td>
						<td class='numeric'><?= $this->Buonny->moeda($diferenca[0]['valor_a_pagar_sn']) ?></td>
						<td class='numeric'><?= $this->Buonny->moeda($diferenca[0]['diferenca']) ?></td>
					</tr>
				<?php endforeach ?>
			<?php endif ?>
		</tbody>
			<tr>
				<td class='numeric'><?= $total_clientes ?></td>
				<td></td>
				<td></td>
				<td class='numeric'><?= $this->Buonny->moeda($total_valor_a_pagar_sa) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($total_valor_a_pagar_sn) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($total_diferenca) ?></td>
			</tr>
		<tfoot>
		</tfoot>
	</table>
<?php endif ?>
<?php echo $this->Javascript->codeBlock("
    function utilizacao_de_servicos( codigo_cliente, data_inicial, data_final ) {   
        var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/portal/clientes/utilizacao_de_servicos/1/' + Math.random());
        form.setAttribute('target', form_id);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][codigo_cliente]');
        field.setAttribute('value', codigo_cliente);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][data_inicial]');
        field.setAttribute('value', data_inicial);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        field = document.createElement('input');
        field.setAttribute('name', 'data[Cliente][data_final]');
        field.setAttribute('value', data_final);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        document.body.appendChild(form);
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
        form.submit();
    }"
); ?>
<?php if (isset($diferencas) && count($diferencas) > 0): ?>
	<?php $this->addScript($this->Buonny->link_css('tablesorter')); ?>
    <?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')); ?>
	<?php echo $this->Javascript->codeBlock("jQuery(document).ready(function() { 
		$.tablesorter.addParser({
				// set a unique id
				id: 'currency-column',
				is: function(s) {
						// return false so this parser is not auto detected
						return false;
				},
				format: function(s) {
						 s = s.replace(/$/g,'');
						 s = s.replace(/\(/g,'-');
						 s = s.replace(/\)/g,'');
						 return $.tablesorter.formatFloat(s.replace(new RegExp(/[^0-9-]/g),''));
				},

				type: 'numeric'
		});
		jQuery('table.table').tablesorter({
			headers: {
				3: { sorter:'currency-column' },
				4: { sorter:'currency-column' },
				5: { sorter:'currency-column' },
			}
		}); 
	})") ?>
<?php endif ?>
