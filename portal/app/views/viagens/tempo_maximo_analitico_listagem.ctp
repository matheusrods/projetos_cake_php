<?php 
    echo $paginator->options(array('update' => 'div.lista')); 
?>
<table class='table table-striped' style='max-width:none;width:2700px'>
	<thead>
		<th class='input-small'>Placa/Chassi</th>
		<th class='input-medium'>CD</th>
		<th class='input-large'>Loja</th>
	    <?php if ($this->data['TViagViagem']['proximo_alvo']): ?>
	    	<th class='input-large'>Próxima Loja</th>
	 	<?php endif ?>
		<th class='input-small'>Bandeira</th>
		<th class='input-small'>Região</th>
	    <th class='input-medium'>Início Janela</th>
        <th class='input-medium'>Fim Janela</th> 
	    <th class='input-medium'>Data Previsão</th>
	    <th class='date input-medium'>Data Entrada</th>
	    <th class='input-medium'>Data Saída</th>
	    <th class='input-medium'>Permanência</th>
	    <?php if (!empty($dados['mesclar_prazo_adiantado'])): ?>
	    	<th class='input-medium'>Status</th> 
    	<?php endif ?>
	    <?php if (empty($dados['mesclar_prazo_adiantado'])): ?>
		    <th class='input-medium'>Status</th> 
		    <th class='input-medium'>Status Janela</th>
		<?php endif ?>
	    <th class='input-medium'>SM</th>
	    <th class='input-medium'>Pedido Cliente</th>
	    <?php if ($tem_pcp): ?>
	    	<th class='input-medium'>Motivo Atraso</th>
		<?php endif?>
	</thead>
	<tbody>
		<?php $total = 0 ?>
		<?php foreach ($dados as $dado): ?>
			<?php
				$inicioReal = AppModel::dbDateToDate(empty($dado[0]['viag_data_inicio']) ? (empty($dado[0]['viag_previsao_inicio']) ? date('Y-m-d H:i:s') : $dado[0]['viag_previsao_inicio']) : $dado[0]['viag_data_inicio']);
				$fimReal = AppModel::dbDateToDate(empty($dado[0]['viag_data_fim']) ? date('Y-m-d H:i:s') : $dado[0]['viag_data_fim']);
				$total++; 
			?>
			<tr>
				<td><?php echo isset($dado[0]['veic_placa'][0]) && ctype_alpha($dado[0]['veic_placa'][0])
				    ? $this->Buonny->placa(preg_replace('/(\w{3})(\d+)/i', "$1-$2", $dado[0]['veic_placa']), $inicioReal, $fimReal)
				    : $dado[0]['veic_chassi'];
				?></td>
				<td class='input-small'><?= $dado[0]['refe_descricao_cd'] ?></td>
				<td class='input-medium'><?= $dado[0]['refe_descricao_entrega'] ?></td>
			    <?php if ($this->data['TViagViagem']['proximo_alvo']): ?>
			    	<td class='input-medium'><?= $dado[0]['refe_descricao_proximo'] ?></td>
				<?php endif ?>
				<td class='input-small'><?= $dado[0]['band_descricao'] ?></td>
				<td class='input-medium'><?= $dado[0]['regi_descricao'] ?></td>
				<td class='input-medium'><?= AppModel::dbDateToDate( $dado[0]['vloc_data_janela_inicio']); ?></td>
				<td class='input-medium'><?= AppModel::dbDateToDate( $dado[0]['vloc_data_janela_fim']); ?></td>
				
				<td class='date input-medium'><?= AppModel::dbDateToDate( $dado[0]['vlev_data_previsao_entrada']); ?></td>

				<td class='input-medium'><?= AppModel::dbDateToDate($dado[0]['vlev_data_entrada']); ?></td>
				<td class='input-medium'><?= AppModel::dbDateToDate($dado[0]['vlev_data_saida']); ?></td>
				<td class='input-medium'><?= Comum::convertToHoursMins($dado[0]['minutos_no_local']); ?></td>
				<?php $status_chegada = '' ?>
				<?php $status_janela = '' ?>
				<?php if (!empty($dado[0]['vlev_data_entrada'])): ?>
					<?php $status_chegada = (isset($status_chegadas[$dado['0']['status_chegada']]) ? $status_chegadas[$dado['0']['status_chegada']] : null); ?>
					<?php $status_janela = (isset($status_janelas[$dado['0']['status_janela']]) ? $status_janelas[$dado['0']['status_janela']] : null); ?>
				<?php endif ?>
				<?php if (!empty($dados['mesclar_prazo_adiantado'])): ?>
					<td class='input-medium'><?php echo $status_chegada ?></td>
			    <?php endif ?>
			    <?php if (empty($dados['mesclar_prazo_adiantado'])): ?>
	                <td class='input-medium'><?php echo $status_chegada ?></td>
				    <td class='input-medium'><?php echo $status_janela ?></td>
			    <?php endif ?>
				    <td class='input-medium'><?= $this->Buonny->codigo_sm($dado[0]['viag_codigo_sm']); ?></td>
				    <td class='input-medium'><?= $dado[0]['viag_pedido_cliente'] ?></td>
			    <?php if ($tem_pcp): ?>
			    	<td class='input-medium'><?= $dado[0]['matr_descricao'] ?></td>
				<?php endif?>
			</tr>
			
		<?php endforeach; ?>
		
	</tbody>
	<tfoot>
		<td colspan='14'>Total: <?= $this->Paginator->counter('{:count}') ?></td>
		<?php if ($this->data['TViagViagem']['proximo_alvo']): ?>
			<td></td>
		<?php endif ?>
		<td></td>
		<?php if ($tem_pcp): ?>
	    	<td></td>
		<?php endif?>
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