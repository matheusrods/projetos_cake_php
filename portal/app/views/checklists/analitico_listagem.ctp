<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class='table table-striped'>
	<thead>
		<th>Data</th>
		<th>Transportador</th>
		<th>Placa</th>
		<th>Alvo Origem</th>
		<th>Operador</th>
		<th>Status</th>
	</thead>
	<tbody>
		<?php if($checklists): ?>
			<?php foreach ($checklists as $key => $checklist): ?>
				<tr>
					<td><?= $checklist['TCveiChecklistVeiculo']['cvei_data_cadastro'] ?></td>
					<td><?= $checklist['Transportador']['pjur_razao_social'] ?></td>
					<td><?= $this->Buonny->placa($checklist['TVeicVeiculo']['veic_placa'], $this->data['TCveiChecklistVeiculo']['data_inicial'], $this->data['TCveiChecklistVeiculo']['data_final'], $this->data['TCveiChecklistVeiculo']['codigo_cliente']) ?></td>
					<td><?= isset($checklist['TRefeReferencia']['refe_descricao']) ? $checklist['TRefeReferencia']['refe_descricao'] : '' ?></td>
					<td><?= $checklist['TCveiChecklistVeiculo']['cvei_usuario_adicionou'] ?></td>
					<?php if(empty($checklist['TCveiChecklistVeiculo']['cvei_data_cancelamento'])):?>
						<td><?= $this->Html->link(($checklist['TCveiChecklistVeiculo']['cvei_status'] ? 'Aprovado' : 'Reprovado') , array('controller' => 'veiculos', 'action' => 'visualizar_checklist', 'VeiculoSinteticoChecklist','cvei_codigo'=>$checklist['TCveiChecklistVeiculo']['cvei_codigo'],'veic_placa'=>$checklist['TVeicVeiculo']['veic_placa'],'codigo_cliente'=>$this->data['TCveiChecklistVeiculo']['codigo_cliente'])) ?></td>
					<?php else:?>
						<td><?= $this->Html->link(('Recusado') , array('controller' => 'veiculos', 'action' => 'visualizar_checklist', 'VeiculoSinteticoChecklist','cvei_codigo'=>$checklist['TCveiChecklistVeiculo']['cvei_codigo'],'veic_placa'=>$checklist['TVeicVeiculo']['veic_placa'],'codigo_cliente'=>$this->data['TCveiChecklistVeiculo']['codigo_cliente'])) ?></td>
					<?php endif;?>	
				</tr>
			<?php endforeach ?>
		<?php else: ?>
			<?php $key = 0 ?>
		<?php endif ?>
	</tbody>
	<tfoot>
		<td colspan=5></td>
		<td class='numeric'><?= $this->Paginator->params['paging']['TCveiChecklistVeiculo']['count']; ?></td>
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