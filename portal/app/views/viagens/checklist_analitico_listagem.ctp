<?php $this->addScript($this->Buonny->link_js('estatisticas')) ?>
<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<div class="well">
	<?php if(!empty($refe_descricao)): ?>
		<strong>CD: </strong><?= $refe_descricao ?>&nbsp;&nbsp;&nbsp;&nbsp;
	<?php endif; ?>
	<?php if(!empty($tipo_checklist)): ?>
		<strong>Tipo Checklist: </strong><?=$tipos_checklist[$tipo_checklist]?>
	<?php endif; ?>
	<span class="pull-right">
		<?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>
	</span>
</div>
<table class='table table-striped' >
	<thead>
		<tr>
			<th class='input-small' rowspan="2">Placa Veículo</th>
			<th colspan="8" style="border-left: 1px solid #ddd;">Entrada</th>
			<th colspan="12" style="border-left: 1px solid #ddd;">Saída</th>
		</tr>
		<tr>
			<!-- Campos Checklist Entrada -->
			<th class='input-small' style="border-left: 1px solid #ddd;">Data</th>
			<th class='input-small'>Hora Início</th>
			<th class='input-small'>Hora Fim</th>
			<th class='input-small'>Placa Carreta</th>
			<th class='input-small numeric'>Ocorrências</th>
			<th class='input-medim'>Operador</th>
			<th class='input-medim'>Status</th>
			<th class='input-small numeric'>Fotos</th>

			<!-- Campos Checklist Saída -->
			<th class='input-small' style="border-left: 1px solid #ddd;">Data</th>
			<th class='input-small'>Hora Início</th>
			<th class='input-small'>Hora Fim</th>
			<th class='input-small'>SM</th>
			<th class='input-small'>Placa Carreta</th>
			<th class='input-large'>Transportador</th>
			<th class='input-medium'>Data Saída</th>
			<th class='input-large'>Loadplan</th>
			<th class='input-small numeric'>Ocorrências</th>
			<th class='input-medim'>Operador</th>
			<th class='input-medim'>Status</th>
			<th class='input-small numeric'>Fotos</th>
		</tr>
	</thead>
	<tbody>
		<?php $total = 0 ?>
		<?php foreach ($checklists as $checklist): ?>
			<?php
				$data_cadastro_entrada = '';
				$hora_cadastro_entrada = '';
				$data_cadastro_saida = '';
				$hora_cadastro_saida = '';
				$data_inicio_entrada = '';
				$hora_inicio_entrada = '';
				$data_inicio_saida = '';
				$hora_inicio_saida = '';

				if (!empty($checklist[0]['data_cadastro_entrada'])) {
					$arrDataCadastro = explode(' ',$checklist[0]['data_cadastro_entrada']);
					$data_cadastro_entrada = $arrDataCadastro[0];
					$hora_cadastro_entrada = preg_replace("/(\d{2})\/(\d{2})\/(\d{2})/", "$1:$2:$3", $arrDataCadastro[1]);
				}
				if (!empty($checklist[0]['data_cadastro_saida'])) {
					$arrDataCadastro = explode(' ',$checklist[0]['data_cadastro_saida']);
					$data_cadastro_saida = $arrDataCadastro[0];
					$hora_cadastro_saida = preg_replace("/(\d{2})\/(\d{2})\/(\d{2})/", "$1:$2:$3", $arrDataCadastro[1]);
				}

				if (!empty($checklist[0]['data_inicio_entrada'])) {
					$arrDataInicio = explode(' ',$checklist[0]['data_inicio_entrada']);
					$data_inicio_entrada = $arrDataInicio[0];
					$hora_inicio_entrada = preg_replace("/(\d{2})\/(\d{2})\/(\d{2})/", "$1:$2:$3", $arrDataInicio[1]);
				}
				if (!empty($checklist[0]['data_inicio_saida'])) {
					$arrDataInicio = explode(' ',$checklist[0]['data_inicio_saida']);
					$data_inicio_saida = $arrDataInicio[0];
					$hora_inicio_saida = preg_replace("/(\d{2})\/(\d{2})\/(\d{2})/", "$1:$2:$3", $arrDataInicio[1]);
				}

    	        $inicioReal = AppModel::dbDateToDate(empty($filtros['data_inicial']) ? date('Y-m-d H:i:s') : $filtros['data_inicial']);
    	        $fimReal = AppModel::dbDateToDate(empty($filtros['data_final']) ? date('Y-m-d H:i:s') : $filtros['data_final']);
                

				$total++; 
			?>
			<tr>
				<td><?php echo (!empty($checklist[0]['placa_veiculo']) ? $this->Buonny->placa(preg_replace('/(\w{3})(\d+)/i', "$1-$2", $checklist[0]['placa_veiculo']),$filtros['data_inicial'],$filtros['data_final'],$filtros['codigo_cliente_portal']):'')?></td>

				<!-- Campos Checklist Entrada -->
				<td><?php echo $this->Html->link(AppModel::dbDateToDate($data_inicio_entrada),'javascript:void(0)', array( 'onclick' => "consulta_checklist_entrada('".$checklist[0]['codigo_checklist_entrada']."')" ))?></td>
				<td><?php echo $hora_inicio_entrada?></td>
				<td><?php echo $hora_cadastro_entrada?></td>
				<td><?php echo (!empty($checklist[0]['placa_carreta_entrada']) ? $this->Buonny->placa(preg_replace('/(\w{3})(\d+)/i', "$1-$2", $checklist[0]['placa_carreta_entrada']),$filtros['data_inicial'],$filtros['data_final'],$filtros['codigo_cliente_portal']):'')?></td>
				<td class="numeric"><?php echo $checklist[0]['qtd_ocorrencia_entrada']?></td>
				<td><?php echo $checklist[0]['operador_entrada']?></td>
				<td><?php echo (!empty($checklist[0]['aprovado_entrada']) ? $status[$checklist[0]['aprovado_entrada']] : '')?></td>
				<td class="input-small numeric">
					<?php echo ($checklist[0]['qtd_fotos_entrada']>0 ? $this->Html->link( '<i class="icon-picture"></i>'.' ('.$checklist[0]['qtd_fotos_entrada'].')', 'javascript:void(0)', array('escape'=>false, 'onclick' => "consulta_fotos_checklist_entrada('".$checklist[0]['codigo_checklist_entrada']."')" )) : '');?>
				</td>
				<!--
				<td class="input-small numeric">
					<?php echo $checklist[0]['qtd_fotos_entrada']?>
					<?php //echo ($checklist[0]['qtd_fotos_entrada']>0 ? $this->Html->link( '<i class="icon-picture"></i>'.' ('.$checklist[0]['qtd_fotos_entrada'].')', 'javascript:void(0)', array('escape'=>false, 'onclick' => "consulta_fotos_checklist('".$checklist[0]['viag_codigo_sm']."')" )) : '');?>
				</td>
				-->
				<!-- Campos Checklist Saída -->
				<td><?php echo $this->Html->link(AppModel::dbDateToDate($data_inicio_saida),'javascript:void(0)', array( 'onclick' => "consulta_checklist_saida('".$checklist[0]['codigo_checklist_saida']."')" ))?></td>
				<td><?php echo $hora_inicio_saida?></td>
				<td><?php echo $hora_cadastro_saida?></td>
				<td><?= $this->Buonny->codigo_sm($checklist[0]['viag_codigo_sm_saida']);?></td>
				<td><?php echo (!empty($checklist[0]['placa_carreta_saida']) ? $this->Buonny->placa(preg_replace('/(\w{3})(\d+)/i', "$1-$2", $checklist[0]['placa_carreta_saida']),$filtros['data_inicial'],$filtros['data_final'],$filtros['codigo_cliente_portal']):'')?></td>
				<td><?php echo $checklist[0]['transportador_saida']?></td>
				<td><?php echo (!empty($checklist[0]['data_saida']) ? AppModel::dbDateToDate($checklist[0]['data_saida']) : '')?></td>
				<td><?php echo $checklist[0]['loadplan_saida']?></td>
				<td class="numeric"><?php echo $checklist[0]['qtd_ocorrencia_saida']?></td>
				<td><?php echo $checklist[0]['operador_saida']?></td>
				<td><?php echo (!empty($checklist[0]['aprovado_saida']) ? $status[$checklist[0]['aprovado_saida']] : '')?></td>
				<td class="input-small numeric">
					<?php echo ($checklist[0]['qtd_fotos_saida']>0 ? $this->Html->link( '<i class="icon-picture"></i>'.' ('.$checklist[0]['qtd_fotos_saida'].')', 'javascript:void(0)', array('escape'=>false, 'onclick' => "consulta_fotos_checklist('".$checklist[0]['viag_codigo_sm_saida']."')" )) : '');?>
				</td>
			</tr>
			
		<?php endforeach; ?>
		
	</tbody>
	<tfoot>
		<td colspan='21'>Total: <?= $this->Paginator->counter('{:count}') ?></td>
	</tfoot>
</table>
<div class='row-fluid'>
	<div class='numbers span6'>
		<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
		<?php echo $this->Paginator->numbers(); ?>
		<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	</div>
	<div class='counter span6'>
		<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%, total de registros %count%')); ?>
	</div>
</div>
<?php echo $this->Js->writeBuffer(); ?>