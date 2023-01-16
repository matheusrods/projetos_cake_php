<?php if(isset($dados)): ?>
	<?php 
	    echo $paginator->options(array('update' => 'div.lista')); 
	    $total_paginas = $this->Paginator->numbers();
	?>
	<?php if ($this->data['Notaite']['agrupamento'] == 1): ?>
		<?php $label = 'Cliente' ?>
	<?php elseif ($this->data['Notaite']['agrupamento'] == 2): ?>
		<?php $label = 'Corretora' ?>
	<?php elseif ($this->data['Notaite']['agrupamento'] == 3): ?>
		<?php $label = 'Grupo Econômico' ?>
	<?php elseif ($this->data['Notaite']['agrupamento'] == 4): ?>
		<?php $label = 'Produto' ?>
	<?php elseif ($this->data['Notaite']['agrupamento'] == 5): ?>
		<?php $label = 'Seguradora' ?>
	<?php elseif ($this->data['Notaite']['agrupamento'] == 6): ?>
		<?php $label = 'Gestor' ?>
	<?php endif ?>
	<table class="table table-striped table-bordered tablesorter">
		<thead class="head_table">
			<tr>
				<th class="input-mini numeric">Código</th>
				<th class="cliente"><?= $label ?></th>
				<th class="input-small numeric">Valor(R$)</th>
				<th class="input-small numeric">Posição</th>
				<th class="input-small numeric">Participação(%)</th>
				<th class="input-small numeric">Acumulado(%)</th>
				<th class='action-icon'></th>
			</tr>
		</thead>
		<tbody>
			<?php $total = 0 ?>
			<?php $acumulado = 0 ?>
			<?php if ($dados !== false): ?>
				<?php foreach ($dados as $dado): ?>
					<tr>
						<td class="input-mini numeric"><?php echo $dado[0]['codigo']; ?></td>
						<td class="cliente"><?php echo $dado[0]['descricao']; ?></td>
						<td class="input-small numeric"><?php echo $this->Buonny->moeda($dado[0]['vlmerc']); ?></td>
						<td class="input-small numeric"><?php echo $dado[0]['registro']; ?></td>
						<td class="input-small numeric"><?php echo number_format($dado[0]['participacao'], 4, ',', '.'); ?></td>
						<td class="input-small numeric"><?php echo number_format($dado[0]['acumulado'], 4, ',', '.'); ?></td>
						<td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "abertura('{$dado[0]['codigo']}', 'comparativo')", 'class' => 'icon-list-alt', 'title' => 'Comparativo Anual')) ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2"><strong>Total do período</strong></td>
				<td class="numeric"><?= $this->Buonny->moeda($totalNotas) ?></td>
				<td colspan="3"></td>
				<td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "abertura('', 'comparativo')", 'class' => 'icon-list-alt', 'title' => 'Comparativo Anual')) ?></td>
			<tr>
		</tfoot>
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
	<?php echo $this->Javascript->codeBlock("
	function abertura(codigo, tipo) {
		var agrupamento = {$this->data['Notaite']['agrupamento']};
		var form = document.createElement('form');
		var form_id = ('formresult' + Math.random()).replace('.','');
		if (tipo == 'comparativo') {
			form.setAttribute('method', 'post');
			form.setAttribute('action', '/portal/itens_notas_fiscais/comparativo_anual_solen/1/' + Math.random());
			form.setAttribute('target', form_id);
		}
		field = document.createElement('input');
		field.setAttribute('name', 'data[Notaite][level]');
		field.setAttribute('value', {$this->data['Notaite']['level']}+1);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		field = document.createElement('input');
		field.setAttribute('name', 'data[Notaite][grupo_empresa]');
		field.setAttribute('value', '');
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		field = document.createElement('input');
		field.setAttribute('name', 'data[Notaite][empresa]');
		field.setAttribute('value', '');
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		field = document.createElement('input');
		field.setAttribute('name', 'data[Notaite][codigo_cliente]');
		field.setAttribute('value', (agrupamento == 1 || (agrupamento == 3 && codigo.substr(0,1) == 'C') ? (codigo.substr(0,1) == 'C' ? codigo.substr(1) : codigo) : '{$this->data['Notaite']['codigo_cliente']}'));
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		field = document.createElement('input');
		field.setAttribute('name', 'data[Notaite][mes]');
		field.setAttribute('value', '{$this->data['Notaite']['mes']}');
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		field = document.createElement('input');
		field.setAttribute('name', 'data[Notaite][ano]');
		field.setAttribute('value', '{$this->data['Notaite']['ano']}');
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		document.body.appendChild(form);
		var janela = window_sizes();
		window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
		form.submit();
	}") ?>
	<?php echo $this->Js->writeBuffer(); ?>
<?php endif; ?>
