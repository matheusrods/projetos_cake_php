<div class='form-procurar'> 
	<div class='well'>
		<?php echo $this->BForm->create('Notafis', array('autocomplete' => 'off', 'url' => array('controller' => 'notas_fiscais', 'action' => 'comparativo_faturamento_cliente'))) ?>
		<div class="row-fluid inline">
			<?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', true) ?>
			<?php echo $this->BForm->input('codigo_gestor', array('class' => 'input-large', 'label' => 'Gestor', 'options' => $gestores, 'empty' => 'Todos os Gestores')); ?>
			<?php echo $this->BForm->input('codigo_produto', array('label' => 'produto', 'class' => 'input-large', 'options' => $produtos, 'empty' => 'Todos os Produtos')) ?>
		<span style="display:block;padding-bottom: 7px;">Mês e Ano a Comparar:</span>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('mes_inicial', array('label' => false, 'class' => 'input-medium', 'options' => $meses, 'default' => date('m'))) ?>
			<?php echo $this->BForm->input('ano_inicial', array('label' => false, 'class' => 'input-small', 'options' => $anos, 'default' => date('Y'))) ?>
			<?php echo $this->BForm->input('mes_final', array('label' => false, 'class' => 'input-medium', 'options' => $meses, 'default' => date('m', strtotime('-1 month')))) ?>
			<?php echo $this->BForm->input('ano_final', array('label' => false, 'class' => 'input-small', 'options' => $anos, 'default' => date('Y-m', strtotime('-1 month')))) ?>
		</div>
		</div>			
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('variacao', array('class' => 'input-mini', 'label' => 'Percentual')); ?>
            <span class="label label-info">Variação:</span>
            <div id='agrupamento'>
				<?php echo $this->BForm->input('sinal_variacao', array('label' => array('class' => 'radio inline input-small'), 'legend' => false, 'type' => 'radio', 'options' => array(1 => 'Positiva', 2 => 'Negativa'), 'default' => 1)); ?>
            </div>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
		<?php echo $this->BForm->end();?>
	</div>
</div>
<?php if (isset($dados)): ?>
	<table class='table table-striped'> 
		<thead>
			<th><?= $this->Html->link('Código', 'javascript:void(0)') ?></th>
			<th><?= $this->Html->link('Cliente', 'javascript:void(0)') ?></th>
			<th class='numeric'><?= $this->Html->link($ano_mes_inicial, 'javascript:void(0)') ?></th>
			<th class='numeric'><?= $this->Html->link($ano_mes_final, 'javascript:void(0)') ?></th>
			<th class='numeric'><?= $this->Html->link('Diferença', 'javascript:void(0)') ?></th>
			<th class='numeric'><?= $this->Html->link('Variação %', 'javascript:void(0)') ?></th>
			<th></th>
		</thead>
		<tbody>
			<?php $total_clientes   = 0 ?>
			<?php $total_valor_um   = 0 ?>
			<?php $total_valor_dois = 0 ?>
			<?php $total_variacao   = 0 ?>
			<?php if ($dados): ?>
				<?php foreach ($dados as $dado): ?>
					<?php $total_clientes++ ?>
					<?php $total_valor_um   += $dado[0]['ano_mes_um'] ?>
					<?php $total_valor_dois += $dado[0]['ano_mes_dois'] ?>
					<?php $total_variacao   += $dado[0]['variacao'] ?>
					<tr>
						<td><?= $dado[0]['codigo'] ?></td>
						<td><?= $dado[0]['razao_social'] ?></td>
						<td class='numeric'><?= $this->Buonny->moeda($dado[0]['ano_mes_um']) ?></td>
						<td class='numeric'><?= $this->Buonny->moeda($dado[0]['ano_mes_dois']) ?></td>
						<td class='numeric'><?= $this->Buonny->moeda($dado[0]['diferenca'] < 0 ? $dado[0]['diferenca'] * -1: $dado[0]['diferenca']) ?></td>
						<td class='numeric'><?= $this->Buonny->moeda($dado[0]['variacao']) ?></td>
						<td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "visualizar_faturamento_cliente({$ano}, '1', '', {$dado[0]['codigo']}, 0)", 'class' => 'icon-list-alt', 'title' => 'Comparativo Anual')) ?></td>
					</tr>
				<?php endforeach ?>
			<?php endif ?>
		</tbody>
			<tr>
				<td>Total</td>
				<td ><?= $total_clientes ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($total_valor_um) ?></td>
				<td class='numeric'><?= $this->Buonny->moeda($total_valor_dois) ?></td>
				<td colspan='2' class='numeric'><?= $this->Buonny->moeda(($total_valor_dois - $total_valor_um) / ($total_valor_um ? $total_valor_um: 1) * 100) ?></td>
				<td></td>
			</tr>
		<tfoot>
		</tfoot>
	</table>
<?php endif ?>
<?= $this->Javascript->codeBlock("	

	function visualizar_faturamento_cliente(ano, grupo_empresa, empresa, codigo_cliente, flag_total){	

		var newwindow = window.open('/portal/notas_fiscais/comparativo_anual/newwindow','_blank','scrollbars=yes,top=0,left=0,width=1000,height=800');								
		newwindow.document.write(
			'<div id=\"postlink\"><form accept-charset=\"utf-8\" method=\"post\" id=\"Notafis\" action=\"/portal/notas_fiscais/comparativo_anual/newwindow\"><input type=\"text\" id=\"NotafisGrupo\" value='+'\"'+grupo_empresa+'\"'+' name=\"data[Notafis][grupo_empresa]\"><input type=\"text\" id=\"NotafisEmpresa\" value='+'\"'+empresa+'\"'+' name=\"data[Notafis][empresa]\"><input type=\"text\" id=\"NotafisAno\" value='+'\"'+ano+'\"'+' name=\"data[Notafis][ano]\"><input type=\"text\" id=\"NotafisTotal\" value='+'\"'+flag_total+'\"'+' name=\"data[Notafis][total]\"><input type=\"text\" id=\"NotafisGestor\" value='+'\"'+codigo_cliente+'\"'+' name=\"data[Notafis][codigo_cliente]\"></form></div>'
		);
		newwindow.document.getElementById('postlink').style.display = 'none';
		newwindow.document.getElementById('Notafis').submit();
	}
	");
?>