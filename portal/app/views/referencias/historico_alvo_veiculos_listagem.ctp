<?php if (!empty($veiculos)): ?>
	<?php 
	    echo $paginator->options(array('update' => 'div.lista')); 
	    $total_paginas = $this->Paginator->numbers();
	?>
	<table class='table table-striped'>
		<thead>
			<tr>
				<td><?= $this->Paginator->sort('Placa', 'veic_placa') ?></td>
				<td><?= $this->Paginator->sort('Transportadora', 'tran_pess_nome') ?></td>
				<td><?= $this->Paginator->sort('Data Entrada', 'data_entrada') ?></td>
				<td><?= $this->Paginator->sort('Data Saída', 'data_saida') ?></td>
				<td class='numeric'><?= $this->Paginator->sort('Permanência (min)', 'minutos_permanencia') ?></td>
			</tr>
		</thead>
		<?php $total_veiculos = 0 ?>
		<?php foreach ($veiculos as $veiculo): ?>
			<?php $total_veiculos ++ ?>
			<tr>
				<td><?= $veiculo[0]['veic_placa'] ?></td>
				<td><?= $veiculo[0]['tran_pess_nome'] ?></td>
				<td><?= AppModel::dbDateToDate($veiculo[0]['data_entrada']) ?></td>
				<td><?= AppModel::dbDateToDate($veiculo[0]['data_saida']) ?></td>
				<td class='numeric'><?= Comum::convertToHoursMins($veiculo[0]['minutos_permanencia']) ?></td>
			</tr>
		<?php endforeach ?>
	</table>
	<div class='row-fluid'>
	    <div class='numbers span6'>
	    	<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
	        <?php echo $this->Paginator->numbers(); ?>
	    	<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	    </div>
	    <div class='counter span6'>
	        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
	    </div>
	</div>
	<?php echo $this->Js->writeBuffer(); ?>
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
<?php endif; ?>