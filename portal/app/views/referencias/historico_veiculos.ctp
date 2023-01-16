<?php echo $this->BForm->create('TRefeReferencia', array('autocomplete' => 'off', 'url' => array('controller' => 'referencias', 'action' => 'historico_veiculos'))) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('data_inicial', array('class' => 'input-small data', 'label' => false, 'placeholder' => 'Data', 'type' => 'text')) ?>
		<?php echo $this->Buonny->input_codigo_cliente_base($this) ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_referencia($this, '#TRefeReferenciaCodigoCliente', 'TRefeReferencia') ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
<?php echo $this->BForm->end();?>
<?php echo $this->Javascript->codeBlock("$(document).ready(function(){setup_datepicker()});") ?>
<?php if (isset($veiculos)): ?>
	<table class='table table-striped'>
		<thead>
			<tr>
				<td>Placa</td>
				<td>Transportadora</td>
				<td></td>
			</tr>
		</thead>
		<?php $total_veiculos = 0 ?>
		<?php foreach ($veiculos as $veiculo): ?>
			<?php $total_veiculos ++ ?>
			<tr>
				<td><?= $veiculo[0]['veic_placa'] ?></td>
				<td><?= $veiculo[0]['tran_pess_nome'] ?></td>
				<td><?= $this->Html->link('<i class="icon-eye-open"></i>', "javascript:historico_alvo_veiculo({$veiculo[0]['veic_oras_codigo']}, {$this->data['TRefeReferencia']['refe_codigo']}, '{$this->data['TRefeReferencia']['data_inicial']}', '{$this->data['TRefeReferencia']['data_final']}' )", array('escape' => false)); ?></td>
			</tr>
		<?php endforeach ?>
		<tfoot>
			<tr>
				<td>Total</td>
				<td><?=$total_veiculos ?></td>
				<td></td>
			</tr>
		</tfoot>
	</table>
	<?php echo $this->Javascript->codeBlock("
		function historico_alvo_veiculo(veic_oras_codigo, refe_codigo, data_inicial, data_final) {
			var form = document.createElement('form');
		    var form_id = ('formresult' + Math.random()).replace('.','');
			form.setAttribute('method', 'post');
			form.setAttribute('target', form_id);
			form.setAttribute('action', '/portal/referencias/historico_alvo_veiculo');
			field = document.createElement('input');
			field.setAttribute('name', 'data[TRefeReferencia][refe_codigo]');
			field.setAttribute('value', refe_codigo);
			field.setAttribute('type', 'hidden');
			form.appendChild(field);
			field = document.createElement('input');
			field.setAttribute('name', 'data[TRefeReferencia][veic_oras_codigo]');
			field.setAttribute('value', veic_oras_codigo);
			field.setAttribute('type', 'hidden');
			form.appendChild(field);
			field = document.createElement('input');
			field.setAttribute('name', 'data[TRefeReferencia][data_inicial]');
			field.setAttribute('value', data_inicial);
			field.setAttribute('type', 'hidden');
			form.appendChild(field);
			field = document.createElement('input');
			field.setAttribute('name', 'data[TRefeReferencia][data_final]');
			field.setAttribute('value', data_final);
			field.setAttribute('type', 'hidden');
			form.appendChild(field);
			document.body.appendChild(form);
		    var janela = window_sizes();
		    window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
		    form.submit();
		};"
	) ?>
<?php endif ?>