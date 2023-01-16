<?php if (count($dados)): ?>
	<?php echo $paginator->options(array('update' => 'div.comissoes_por_corretora_sintetico_listagem')); ?>
	<table class='table table-striped'>
		<thead>
			<th>Corretora</th>
			<th class='numeric'>Valor</th>
			<th class='numeric'>Valor Impostos</th>
			<th class='numeric'>Valor Líquido</th>
			<th class='numeric'>Valor Comissão</th>
		</thead>
		<tbody>
			<?php foreach ($dados as $dado): ?>
				<tr>
					<td><?php echo $dado[0]['corretora_nome'] ?></td>
					<td class='numeric'><?php echo $this->Html->link($this->Buonny->moeda($dado[0]['valor_servico'], array('nozero' => true)),'javascript:void(0)',array('onclick' => "comissoes_por_corretora_analitico({$dado[0]['codigo_corretora']},'{$dado[0]['corretora_nome']}')") ) ?></td>
					<td class='numeric'><?php echo $this->Buonny->moeda($dado[0]['valor_impostos'], array('nozero' => true)) ?></td>
					<td class='numeric'><?php echo $this->Buonny->moeda($dado[0]['valor_servico_liquido'], array('nozero' => true)) ?></td>
					<td class='numeric'><?php echo $this->Buonny->moeda($dado[0]['valor_comissao'], array('nozero' => true)) ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td></td>
				<td class='numeric'><strong><?= $this->Buonny->moeda($totais[0][0]['valor_servico'], array('nozero' => true)) ?></strong></td>
				<td></td>
				<td class='numeric'><strong><?= $this->Buonny->moeda($totais[0][0]['valor_servico_liquido'], array('nozero' => true)) ?></strong></td>
				<td class='numeric'><strong><?= $this->Buonny->moeda($totais[0][0]['valor_comissao'], array('nozero' => true)) ?></strong></td>
			</tr>
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
	<?php echo $this->Js->writeBuffer(); ?>
<?php endif ?>
<?php echo $this->Javascript->codeBlock("
	function comissoes_por_corretora_analitico(codigo_corretora,codigo_corretora_visual) {
		var form = document.createElement('form');
		var form_id = ('formresult' + Math.random()).replace('.','');
		form.setAttribute('method', 'post');
		form.setAttribute('action', '/portal/transacoes_de_recebimento/comissoes_por_corretora_analitico');
		form.setAttribute('target', form_id);
		field = document.createElement('input');
		field.setAttribute('name', 'data[Tranrec][mes_faturamento]');
		field.setAttribute('value', '{$this->data['Tranrec']['mes_faturamento']}');
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		field = document.createElement('input');
		field.setAttribute('name', 'data[Tranrec][ano_faturamento]');
		field.setAttribute('value', '{$this->data['Tranrec']['ano_faturamento']}');
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		field = document.createElement('input');
		field.setAttribute('name', 'data[Tranrec][codigo_corretora]');
		field.setAttribute('value', codigo_corretora);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		field = document.createElement('input');
		field.setAttribute('name', 'data[Tranrec][codigo_corretora_visual]');
		field.setAttribute('value', codigo_corretora_visual);
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		field = document.createElement('input');
		field.setAttribute('name', 'data[Tranrec][tipo_faturamento]');
		field.setAttribute('value', '{$this->data['Tranrec']['tipo_faturamento']}');
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		field = document.createElement('input');
		field.setAttribute('name', 'data[Tranrec][codigo_cliente]');
		field.setAttribute('value', '{$this->data['Tranrec']['codigo_cliente']}');
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		field = document.createElement('input');
		field.setAttribute('name', 'data[Tranrec][configuracao_comissao]');
		field.setAttribute('value', '{$this->data['Tranrec']['configuracao_comissao']}');
		field.setAttribute('type', 'hidden');
		form.appendChild(field);
		document.body.appendChild(form);
		var janela = window_sizes();
		window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-100)+',width='+(janela.width-50).toString()+',resizable=yes,toolbar=no,status=no');
		form.submit();
	}
") ?>
<?php echo $this->Javascript->codeBlock("
    jQuery(document).ready(function(){
		$('.numbers a[id^=\"link\"]').bind('click', function (event) { bloquearDiv($('.comissoes_por_corretora_sintetico_listagem')); });
    });", false);
?>