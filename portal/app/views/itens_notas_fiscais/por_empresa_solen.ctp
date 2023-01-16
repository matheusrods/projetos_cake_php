<?php if (empty($empresas)): ?>
	<div class='well'>
		<?php echo $this->BForm->create('Notaite', array('autocomplete' => 'off', 'url' => array('controller' => 'itens_notas_fiscais', 'action' => 'por_empresa_solen'))) ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('mes', array('label' => false, 'placeholder' => 'Mês', 'class' => 'input-small', 'options' => $meses)) ?>
			<?php echo $this->BForm->input('ano', array('label' => false, 'placeholder' => 'Ano','class' => 'input-small', 'options' => $anos)) ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $this->BForm->end() ?>
	</div>
<?php else: ?>
	<div class='well'>
	    <strong>Mês: </strong><?php echo $this->Buonny->mes_extenso($this->data['Notaite']['mes']); ?><strong> Ano: </strong><?php echo $this->data['Notaite']['ano']; ?>
	</div>	
    <?php if (count($solen_do_ano) > 0): ?>
		<table class='table table-striped table-bordered'>
			<thead>
				<th>Empresa</ht>
				<th class='span2 numeric'>Mês(R$)</ht>
				<th class='span2 numeric'>Ano(R$)</ht>
				<th class='action-icon'></th>
			</thead>
			<?php $total_mes = 0 ?>
			<?php $total_ano = 0 ?>
			<?php //debug($empresas); ?>
			<?php foreach ($empresas as $codigo => $empresa): ?>
				<?php $total_mes += (isset($solen_do_mes[$codigo]) ? $solen_do_mes[$codigo]['0']['total'] : 0) ?>
				<?php $total_ano += (isset($solen_do_ano[$codigo]) ? $solen_do_ano[$codigo]['0']['total'] : 0) ?>
				<?php $valor_mes = (isset($solen_do_mes[$codigo]) ? $this->Buonny->moeda($solen_do_mes[$codigo]['0']['total']) : '') ?>
				<?php $valor_ano = (isset($solen_do_ano[$codigo]) ? $this->Buonny->moeda($solen_do_ano[$codigo]['0']['total']) : '') ?>
				<?php if (!empty($valor_ano)): ?>
					<tr>
						<td><?= $empresas['15']?></td>
						<td class='numeric'><?= $this->Html->link($valor_mes, 'javascript:void(0)', array('onclick' => "por_produto('".LojaNaveg::GRUPO_SOLEN."', '{$codigo}', 'mes', {$this->data['Notaite']['mes']}, {$this->data['Notaite']['ano']})")) ?></td>
						<td class='numeric'><?= $this->Html->link($valor_ano, 'javascript:void(0)', array('onclick' => "por_produto('".LojaNaveg::GRUPO_SOLEN."', '{$codigo}', 'ano', {$this->data['Notaite']['mes']}, {$this->data['Notaite']['ano']})")) ?></td>
						<td class='action-icon'><?= $this->Html->link('', 'javascript:void(0)', array('onclick' => "comparativo_anual('{$this->data['Notaite']['ano']}','".LojaNaveg::GRUPO_SOLEN."', '{$codigo}')", 'class' => 'icon-list-alt', 'title' => 'Comparativo Anual')) ?></td>
					</tr>
				<?php endif ?>
			<?php endforeach ?>
			<tfoot>
				<td>Total</td>
				<td class='numeric'><?= $this->Html->link($this->Buonny->moeda($total_mes), 'javascript:void(0)', array('onclick' => "por_produto('".LojaNaveg::GRUPO_SOLEN."', '', 'mes', {$this->data['Notaite']['mes']}, {$this->data['Notaite']['ano']})")) ?></td>
				<td class='numeric'><?= $this->Html->link($this->Buonny->moeda($total_ano), 'javascript:void(0)', array('onclick' => "por_produto('".LojaNaveg::GRUPO_SOLEN."', '', 'ano', {$this->data['Notaite']['mes']}, {$this->data['Notaite']['ano']})")) ?></td>
				<td class='action-icon'><?= $this->Html->link('', 'javascript:void(0)', array('onclick' => "comparativo_anual('{$this->data['Notaite']['ano']}','".LojaNaveg::GRUPO_SOLEN."', '')", 'class' => 'icon-list-alt', 'title' => 'Comparativo Anual')) ?></td>
			</tfoot>
		</table>
	<?php endif ?>


<?php endif; ?>
<?= $this->Javascript->codeBlock("
	function comparativo_anual(ano, grupo_empresa, empresa){
		var field = null;
		var form = document.createElement(\"form\");
        form.setAttribute(\"target\", \"formresult\");
		form.setAttribute(\"method\", \"post\");
        form.setAttribute(\"action\", \"/portal/itens_notas_fiscais/comparativo_anual/1\");
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Notaite][ano]\");
        field.setAttribute(\"value\", ano);
        form.appendChild(field);
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Notaite][grupo_empresa]\");
        field.setAttribute(\"value\", grupo_empresa);
        form.appendChild(field);
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Notaite][empresa]\");
        field.setAttribute(\"value\", empresa);
        form.appendChild(field);
        document.body.appendChild(form);

        var janela = window_sizes();
        window.open('', 'formresult', 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');

        form.submit();
        $(form).remove();
	}

	function por_produto(grupo_empresa, empresa, tipo, mes, ano){
		var field = null;
		var data_inicial = null;
		var data_final = null;
        if (tipo == 'mes') {
			var ultimo_dia = str_pad(new Date(ano, mes, 0).getUTCDate(), 2, '0', 'STR_PAD_LEFT');
			mes = str_pad(mes, 2, '0', 'STR_PAD_LEFT');
			data_inicial = '01/' + mes + '/' + ano;
			data_final = ultimo_dia + '/' + mes + '/' + ano;
		} else {
			data_inicial = '01/01/' + ano;
			data_final = '31/12/' + ano;
		}

		var form = document.createElement(\"form\");
        form.setAttribute(\"target\", \"formresult\");
		form.setAttribute(\"method\", \"post\");
        form.setAttribute(\"action\", \"/portal/itens_notas_fiscais/por_produto_solen/1\");
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Notaite][data_inicial]\");
        field.setAttribute(\"value\", data_inicial);
        form.appendChild(field);
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Notaite][data_final]\");
        field.setAttribute(\"value\", data_final);
        form.appendChild(field);
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Notaite][grupo_empresa]\");
        field.setAttribute(\"value\", grupo_empresa);
        form.appendChild(field);
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Notaite][empresa]\");
        field.setAttribute(\"value\", empresa);
        form.appendChild(field);
        document.body.appendChild(form);
        field = document.createElement(\"input\");
        field.setAttribute(\"name\", \"data[Notaite][codigo_cliente]\");
        form.appendChild(field);
        document.body.appendChild(form);

        var janela = window_sizes();
        window.open('', 'formresult', 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');

        form.submit();
        $(form).remove();
	}"
	);
?>