<div class="well">
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('TViagViagem.viag_codigo_sm', array('class' => 'input-small', 'label' => 'SM', 'readonly' => TRUE)) ?>
		<?php echo $this->BForm->input('TVtemViagemTemperatura.vtem_valor_minimo', array('class' => 'input-small', 'label' => 'Valor Min', 'readonly' => TRUE)) ?>
		<?php echo $this->BForm->input('TVtemViagemTemperatura.vtem_valor_maximo', array('class' => 'input-small', 'label' => 'Valor Max', 'readonly' => TRUE)) ?>
	</div>
</div>

<div class='row-fluid'>
<table class='table table-striped tablesorter'>
	<thead>
		<th class='input-mini'><?= $this->Html->link('Valor', 'javascript:void(0)') ?></th>
		<th><?= $this->Html->link('Inicio', 'javascript:void(0)') ?></th>
		<th><?= $this->Html->link('Fim', 'javascript:void(0)') ?></th>
		<th class='input-xxlarge'><?= $this->Html->link('Posição', 'javascript:void(0)') ?></th>
		<th style="width:13px">&nbsp;</th>
	</thead>
	<tbody>
		<?php foreach($listagem as $rper): ?>
			<tr>
				<td><?php echo $rper[0]['valor_real'] ?></td>
				<td><?php echo AppModel::dbDateToDate($rper[0]['data_inicio']) ?></td>
				<td><?php echo AppModel::dbDateToDate($rper[0]['data_fim']) ?></td>
				<td><?php echo $this->Buonny->posicao_geografica($rper[0]['descricao'], $rper[0]['latitude'], $rper[0]['longitude']) ?></td>
				<td>
					<?php if(str_replace(',', '.', $rper[0]['valor_real']) >= $viagem['TVtemViagemTemperatura']['vtem_valor_minimo'] &&
							str_replace(',', '.', $rper[0]['valor_real']) <= $viagem['TVtemViagemTemperatura']['vtem_valor_maximo']): ?>
						<span class="badge-empty badge badge-success" title="Dentro da faixa de temperatura"></span>
					<?php else: ?>
						<span class="badge-empty badge badge-important" title="Fora da faixa de temperatura"></span>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
<div>
<?php echo $this->Buonny->link_css('tablesorter') ?>
<?php echo $this->Buonny->link_js('jquery.tablesorter.min') ?>
<?php echo $this->Javascript->codeBlock("jQuery('table.tablesorter').tablesorter()") ?>