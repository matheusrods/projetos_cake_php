<?php if ($this->passedArgs[0] != 'export'): ?>
    
<?php endif; ?>
<div class="well">
		<span class="pull-right">
            <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array( 'controller' => $this->name, 'action' => $this->action, 'export'), array('escape' => false, 'title' =>'Exportar para Excel'));?>   
        </span>
</div>
<?php echo $paginator->options(array('update' => 'div#lista')); ?>
<div class='row-fluid'>
		<table class='table table-striped horizontal-scroll' style='width:2650px;max-width:none;'>
		<thead>
			<th class='numeric' class='input-small'>Loadplan</th>
			<th class='input-xlarge'>Load Origem</th>
			<th class='input-xlarge'>Load Destino</th>
			<th class='input-xlarge'>Load Data Cadastro</th>
			<th class='input-xlarge'>Load Data Finalização</th>
			<th class='input-xxlarge'>Transportadora</th>
			<th class='numeric'>SM</th>
			<th class='input-xlarge'>SM Origem</th>
			<th class='input-xlarge'>SM Destino</th>
			<th class='input-medium'>SM Data Início</th>
			<th class='input-medium'>SM Data Fim</th>
		</thead>		
		<tbody>		
			<?php foreach ($listagem as $key => $obj):?>
				<tr>
					<td class='numeric'><?php echo $this->Buonny->codigo_loadplan($obj[0]['load_loadplan']);?></td>
					<td><?php echo (isset($obj[0]['origem_refe_descricao']))?$obj[0]['origem_refe_descricao']:NULL?></td>
					<td><?php echo (isset($obj[0]['destino_refe_descricao']))? $obj[0]['destino_refe_descricao']:NULL?></td>
					<td><?php echo AppModel::dbDateToDate($obj[0]['load_data_cadastro'])?></td>
					<td><?php echo AppModel::dbDateToDate($obj[0]['load_data_finalizado'])?></td>
					<td><?php echo $obj[0]['loadplan_pjur_razao_social']?></td>
					<td class='numeric' ><?php echo $this->Buonny->codigo_sm($obj[0]['viag_codigo_sm']);?></td>
					<td><?php echo (isset($obj[0]['origem_sm']))?$obj[0]['origem_sm']:NULL?></td>
					<td><?php echo (isset($obj[0]['destino_sm']))?$obj[0]['destino_sm']:NULL?></td>
					<td><?php echo AppModel::dbDateToDate($obj[0]['viag_data_inicio'])?></td>
					<td><?php echo AppModel::dbDateToDate($obj[0]['viag_data_fim'])?></td>
				</tr>
			<?php endforeach;?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan = "14"><strong>Total</strong>
					<?php echo $this->Paginator->params['paging']['TLoadLoadplan']['count']; ?>
				</td>
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
</div>
<?php echo $this->Js->writeBuffer(); ?>
