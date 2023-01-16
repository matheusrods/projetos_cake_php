<?php if(isset($dados)): ?>
	<?php 
	    echo $paginator->options(array('update' => 'div.lista')); 
	    $total_paginas = $this->Paginator->numbers();
	?>

	<?php $codigo_empresa = isset($empresa['LojaNaveg']['codigo']) ? $empresa['LojaNaveg']['codigo'] : ''; ?>                    
	
	<?php if( !$nova_janela ): ?>
		<div class="well">
	        <strong>Grupo: </strong><?php echo $nome_grupo;  ?>
	        <strong>Empresa: </strong><?php echo (!empty($empresa) ? $empresa['LojaNaveg']['razaosocia'] : 'Todas empresas'); ?>                        
	        <strong>Período de: </strong><?= $periodo[0] ?> <strong>à</strong>  <?= $periodo[1] ?>
	        <?php if (!empty($cliente)): ?>
	                <strong>Código: </strong><?php echo $cliente['Cliente']['codigo']; ?>
	                <strong>Cliente: </strong><?php echo $cliente['Cliente']['razao_social']; ?>
	        <?php endif ?>
	    </div>  
	<?php else: ?>
		<div class="well">
			<strong><?php echo $nova_janela; ?></strong>: <?php echo $filtros['nome']; ?>
		</div>
	<?php endif; ?>

	<table class="table table-striped table-bordered tablesorter">
		<thead class="head_table">
			<tr>
				<th class="input-mini numeric">Código</th>
				<th class="cliente">Cliente</th>
				<th class="input-small numeric">Valor(R$)</th>
				<th class="input-small numeric">Posição</th>
				<th class="input-small numeric">Participação(%)</th>
				<th class="input-small numeric">Acumulado(%)</th>
				<th class='action-icon'></th>
				<th class='action-icon'></th>
			</tr>
		</thead>
		<tbody>
			<?php $total = 0 ?>
			<?php $acumulado = 0 ?>
			<?php if ($dados !== false): ?>
				<?php foreach ($dados as $dado): ?>
					
					<tr>
						<td class="input-mini numeric"><?php echo $dado['Cliente']['codigo']; ?></td>
						<td class="cliente"><?php echo $dado['Cliente']['razao_social']; ?></td>
						<td class="input-small numeric"><?php echo $this->Buonny->moeda($dado['Notafis']['vlmerc']); ?></td>
						<td class="input-small numeric"><?php echo $dado[0]['registro']; ?></td>
						<td class="input-small numeric"><?php echo number_format($dado[0]['porcentagem'], 4, ',', '.'); ?></td>
						<td class="input-small numeric"><?php echo number_format($dado[0]['acumulado'], 4, ',', '.'); ?></td>
						<td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "visualizar_faturamento_cliente({$ano}, '{$grupo_empresa}', '{$codigo_empresa}', {$dado['Cliente']['codigo']}, 0)", 'class' => 'icon-list-alt', 'title' => 'Comparativo Anual')) ?></td>
						<td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "produtos_faturados('{$grupo_empresa}', '{$codigo_empresa}', {$dado['Cliente']['codigo']}, '{$mes}', '{$ano}')", 'class' => 'icon-list-alt', 'title' => 'Produtos Faturados')) ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2"><strong>Total do período</strong></td>
				<td class="numeric"><?= $this->Buonny->moeda($totalNotas) ?></td>
				<td colspan="3"></td>
				<td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "visualizar_faturamento_cliente({$ano}, '{$grupo_empresa}', '{$codigo_empresa}', '', 0)", 'class' => 'icon-list-alt', 'title' => 'Comparativo Anual')) ?></td>
				<td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "produtos_faturados('{$grupo_empresa}', '{$codigo_empresa}', '', '{$mes}', '{$ano}')", 'class' => 'icon-list-alt', 'title' => 'Produtos Faturados')) ?></td>
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
	<?php echo $this->Js->writeBuffer(); ?>
<?php endif; ?>
<?= $this->Javascript->codeBlock("	

	function visualizar_faturamento_cliente(ano, grupo_empresa, empresa, codigo_cliente, flag_total){	

		var newwindow = window.open('/portal/notas_fiscais/comparativo_anual/newwindow','_blank','scrollbars=yes,top=0,left=0,width=1000,height=800');								
		newwindow.document.write(
			'<div id=\"postlink\"><form accept-charset=\"utf-8\" method=\"post\" id=\"Notafis\" action=\"/portal/notas_fiscais/comparativo_anual/newwindow\"><input type=\"text\" id=\"NotafisGrupo\" value='+'\"'+grupo_empresa+'\"'+' name=\"data[Notafis][grupo_empresa]\"><input type=\"text\" id=\"NotafisEmpresa\" value='+'\"'+empresa+'\"'+' name=\"data[Notafis][empresa]\"><input type=\"text\" id=\"NotafisAno\" value='+'\"'+ano+'\"'+' name=\"data[Notafis][ano]\"><input type=\"text\" id=\"NotafisTotal\" value='+'\"'+flag_total+'\"'+' name=\"data[Notafis][total]\"><input type=\"text\" id=\"NotafisGestor\" value='+'\"'+codigo_cliente+'\"'+' name=\"data[Notafis][codigo_cliente]\"></form></div>'
		);
		newwindow.document.getElementById('postlink').style.display = 'none';
		newwindow.document.getElementById('Notafis').submit();
	}

	function produtos_faturados(grupo_empresa, empresa, codigo_cliente, data_inicial, data_final) {		

		var newwindow = window.open('/portal/itens_notas_fiscais/por_produto/newwindow','_blank','scrollbars=yes,top=0,left=0,width=1000,height=800');								
		newwindow.document.write(
			'<div id=\"postlink\"><form accept-charset=\"utf-8\" method=\"post\" id=\"Notaite\" action=\"/portal/itens_notas_fiscais/por_produto/newwindow\"><input type=\"text\" id=\"NotaiteGrupo\" value='+'\"'+grupo_empresa+'\"'+' name=\"data[Notaite][grupo_empresa]\"><input type=\"text\" id=\"NotaiteEmpresa\" value='+'\"'+empresa+'\"'+' name=\"data[Notaite][empresa]\"><input type=\"text\" id=\"NotaiteCliente\" value='+'\"'+codigo_cliente+'\"'+' name=\"data[Notaite][codigo_cliente]\"><input type=\"text\" id=\"NotaiteInicial\" value='+'\"'+data_inicial+'\"'+' name=\"data[Notaite][data_inicial]\"><input type=\"text\" id=\"NotaiteFinal\" value='+'\"'+data_final+'\"'+' name=\"data[Notaite][data_final]\"></form></div>'
		);
		newwindow.document.getElementById('postlink').style.display = 'none';
		newwindow.document.getElementById('Notaite').submit();
	}
	"
);
?>
