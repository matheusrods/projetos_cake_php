<?php if (count($dados)): ?>
	<table class='table table-striped'>
		<thead>
			<th>Filial</th>
			<th class='numeric'>Valor</th>
			<th class='numeric'>Comissão</th>
		</thead>
		<tbody>
			<?php foreach ($dados as $dado): ?>
				<tr>
					<td><?= $dado[0]['filial_nome'] ?></td>
					<td class='numeric'><?= $this->Html->link($this->Buonny->moeda($dado[0]['valor'], array('nozero' => true)), 'javascript:void(0)', array('onclick' => "comissao_analitico('{$dado[0]['codigo_endereco_regiao']}')")) ?></td>
					<td class='numeric'><?= $this->Buonny->moeda($dado[0]['valor_comissao'], array('nozero' => true)) ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
	<?php echo $this->Javascript->codeBlock("
		function comissao_analitico(codigo_endereco_regiao) {
			var form = document.createElement('form');
	        var form_id = ('formresult' + Math.random()).replace('.','');
	        form.setAttribute('method', 'post');
	        form.setAttribute('action', '/portal/transacoes_de_recebimento/comissoes_analitico');
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
	        field.setAttribute('name', 'data[Tranrec][codigo_endereco_regiao]');
	        field.setAttribute('value', codigo_endereco_regiao);
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
	        field.setAttribute('name', 'data[Tranrec][codigo_gestor]');
	        field.setAttribute('value', '{$this->data['Tranrec']['codigo_gestor']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);
	        field = document.createElement('input');
	        field.setAttribute('name', 'data[Tranrec][codigo_seguradora]');
	        field.setAttribute('value', '{$this->data['Tranrec']['codigo_seguradora']}');
	        field.setAttribute('type', 'hidden');
	        form.appendChild(field);
	        field = document.createElement('input');
	        field.setAttribute('name', 'data[Tranrec][codigo_corretora]');
	        field.setAttribute('value', '{$this->data['Tranrec']['codigo_corretora']}');
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
<?php endif ?>