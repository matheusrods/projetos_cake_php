<?php if(isset($dados)): ?>
	<?php
	    echo $paginator->options(array('update' => 'div.lista'));
	    $total_paginas = $this->Paginator->numbers();
	?>
	<?php $codigo_empresa = isset($empresa['LojaNaveg']['codigo']) ? $empresa['LojaNaveg']['codigo'] : ''; ?>
	<table class="table table-striped table-bordered tablesorter">
		<thead class="head_table">
			<tr>
				<th class="input-mini numeric">Código</th>
				<th class="cliente">Seguradora</th>
				<th class="input-small numeric">Valor(R$)</th>
				<th class="input-small numeric">Posição</th>
				<th class="input-small numeric">Participação(%)</th>
				<th class="input-small numeric">Acumulado(%)</th>
				<th class='action-icon'></th>
				<th class='action-icon'></th>
				<th class='action-icon'></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($dados as $dado): ?>
				<tr>
					<td class="input-mini numeric"><?php echo $dado['Seguradora']['codigo']; ?></td>
					<td class="cliente"><?php echo empty($dado['Seguradora']['codigo']) ? 'Outros' : $dado['Seguradora']['nome']; ?></td>
					<td class="input-small numeric"><?php echo $this->Buonny->moeda($dado['Notafis']['vlmerc']); ?></td>
					<td class="input-small numeric"><?php echo $dado[0]['registro']; ?></td>
					<td class="input-small numeric"><?php echo number_format($dado[0]['porcentagem'], 4, ',', '.'); ?></td>
					<td class="input-small numeric"><?php echo number_format($dado[0]['acumulado'], 4, ',', '.'); ?></td>
					<td class='action-icon'><?php echo empty($dado['Seguradora']['codigo']) ? '' : $this->Html->link('', 'javascript:void(0)', array('onclick' => "visualizar_faturamento_seguradora({$ano}, '{$grupo_empresa}', '{$codigo_empresa}', {$dado['Seguradora']['codigo']}, '{$dado['Seguradora']['nome']}', 0)", 'class' => 'icon-list-alt', 'title' => 'Comparativo Anual')) ?></td>
					<td class='action-icon'><?php echo empty($dado['Seguradora']['codigo']) ? '' : $this->Html->link('', 'javascript:void(0)', array('onclick' => "produtos_faturados('{$grupo_empresa}', '{$codigo_empresa}', {$dado['Seguradora']['codigo']}, '{$periodo[0]}', '{$periodo[1]}')", 'class' => 'icon-list-alt', 'title' => 'Produtos Faturados')) ?></td>
					<td class='action-icon'><?php echo empty($dado['Seguradora']['codigo']) ? '' : $this->Html->link('', 'javascript:void(0)', array('onclick' => "clientes_por_seguradora('{$grupo_empresa}', '{$codigo_empresa}', {$dado['Seguradora']['codigo']}, '{$mes}', '{$ano}', '{$dado['Seguradora']['nome']}')", 'class' => 'icon-list-alt', 'title' => 'Clientes por Seguradora')) ?></td>

				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="2">Total do período</td>
				<td class="numeric"><?= $this->Buonny->moeda($totalNotas) ?></td>
				<td colspan="3"></td>
				<td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "visualizar_faturamento_seguradora({$ano}, '{$grupo_empresa}', '{$codigo_empresa}', '', '', 1)", 'class' => 'icon-list-alt', 'title' => 'Comparativo Anual')) ?></td>
				<td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "produtos_faturados('{$grupo_empresa}', '{$codigo_empresa}', '', '{$mes}', '{$ano}')", 'class' => 'icon-list-alt', 'title' => 'Produtos Faturados')) ?></td>
				<td class='action-icon'><?php echo $this->Html->link('', 'javascript:void(0)', array('onclick' => "clientes_por_seguradora('{$grupo_empresa}', '{$codigo_empresa}', '', '{$mes}', '{$ano}')", 'class' => 'icon-list-alt', 'title' => 'Todos Clientes')) ?></td>
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

	function visualizar_faturamento_seguradora(ano, grupo_empresa, empresa, codigo_seguradora, nome, flag_total){	
		
		var newwindow = window.open('/portal/notas_fiscais/comparativo_anual/newwindow','_blank','scrollbars=yes,top=0,left=0,width=1000,height=800');								
		newwindow.document.write(
			'<div id=\"postlink\"><form accept-charset=\"utf-8\" method=\"post\" id=\"Notafis\" action=\"/portal/notas_fiscais/comparativo_anual/newwindow\"><input type=\"text\" id=\"NotafisGrupo\" value='+'\"'+grupo_empresa+'\"'+' name=\"data[Notafis][grupo_empresa]\"><input type=\"text\" id=\"NotafisEmpresa\" value='+'\"'+empresa+'\"'+' name=\"data[Notafis][empresa]\"><input type=\"text\" id=\"NotafisSeguradora\" value='+'\"'+codigo_seguradora+'\"'+' name=\"data[Notafis][codigo_seguradora]\"><input type=\"text\" id=\"NotafisAno\" value='+'\"'+ano+'\"'+' name=\"data[Notafis][ano]\"><input type=\"text\" id=\"NotafisTotal\" value='+'\"'+flag_total+'\"'+' name=\"data[Notafis][total]\"><input type=\"text\" id=\"NotafisRanking\" value=\"seguradoras\" name=\"data[Notafis][tipo_ranking]\"><input type=\"text\" id=\"NotafisNome\" value='+'\"'+nome+'\"'+' name=\"data[Notafis][nome]\"></form></div>'
		);
		newwindow.document.getElementById('postlink').style.display = 'none';
		newwindow.document.getElementById('Notafis').submit();	
	}

	function produtos_faturados(grupo_empresa, empresa, codigo_seguradora, data_inicial, data_final) {	

		var newwindow = window.open('/portal/itens_notas_fiscais/por_produto/newwindow','_blank','scrollbars=yes,top=0,left=0,width=1000,height=800');								
		newwindow.document.write(
			'<div id=\"postlink\"><form accept-charset=\"utf-8\" method=\"post\" id=\"Notaite\" action=\"/portal/itens_notas_fiscais/por_produto/newwindow\"><input type=\"text\" id=\"NotaiteGrupo\" value='+'\"'+grupo_empresa+'\"'+' name=\"data[Notaite][grupo_empresa]\"><input type=\"text\" id=\"NotaiteEmpresa\" value='+'\"'+empresa+'\"'+' name=\"data[Notaite][empresa]\"><input type=\"text\" id=\"NotaiteSeguradora\" value='+'\"'+codigo_seguradora+'\"'+' name=\"data[Notaite][codigo_seguradora]\"><input type=\"text\" id=\"NotaiteInicial\" value='+'\"'+data_inicial+'\"'+' name=\"data[Notaite][data_inicial]\"><input type=\"text\" id=\"NotaiteFinal\" value='+'\"'+data_final+'\"'+' name=\"data[Notaite][data_final]\"><input type=\"text\" id=\"NotaiteRanking\" value=\"seguradoras\" name=\"data[Notaite][tipo_ranking]\"></form></div>'
		);
		newwindow.document.getElementById('postlink').style.display = 'none';
		newwindow.document.getElementById('Notaite').submit();
	}

	function clientes_por_seguradora(grupo_empresa, empresa, codigo_seguradora, data_inicial, data_final, nome) {
		jQuery.post('/portal/notas_fiscais/ranking_faturamento_listagem',
			{
				'data[Notafis][grupo_empresa]' : grupo_empresa,
                'data[Notafis][empresa]' 	   : empresa,
				'data[Notafis][seguradoras]'   : codigo_seguradora,
				'data[Notafis][data_inicial]'  : data_inicial,
				'data[Notafis][data_final]'    : data_final,					
                'data[Notafis][nome]' 		   : nome,
                'data[Notafis][mes]' 		   : data_inicial,
                'data[Notafis][ano]' 		   : data_final,
                'data[Notafis][produtos]' 	   : '',                
				'data[Notafis][gestores]'      : '',
				'data[Notafis][corretoras]'	   : ''
			}, function( data ) {
				var newwindow = window.open('/portal/notas_fiscais/ranking_faturamento_listagem/janela_seguradoras','_blank','scrollbars=yes,top=0,left=0,width=1000,height=800');
				/*generate_modal_dialog();
				jQuery('#modal_dialog').html(data);
				var name = (nome != undefined) ? nome : 'Todas';
				var txt  = '<strong>Seguradora: </strong>' + name;
				jQuery('#modal_dialog .well').html(txt);
				var modal_params_default = {modal: true, resizable: false, width: 1000, title: 'Clientes por Seguradora'};
				jQuery('#modal_dialog').dialog(modal_params_default);*/
			}
		);
	}

	"
);
?>