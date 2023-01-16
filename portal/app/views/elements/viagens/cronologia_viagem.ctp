<div class="well">
	<strong>Estação:</strong>
	<?=$estacao?>
</div>
<table class="table table-striped" style=''>
	<thead>
		<tr>
			<th class='input-medium'>Data Evento</th>
			<th class='input-medium'>Tipo Evento</th>
			<th>Descrição</th>
			<th class='input-medium'>Alvo</th>
			<th class='input-medium'>Operador Gravação</th>
			<th class='input-medium'>Operador Tratou</th>
			<th class='input-medium'>Data Leitura</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($cronologia)): ?>
			<?php foreach($cronologia as $evento): ?>
				<?
					$data_evento = AppModel::dbDateToDate($evento[0]['data_inclusao']);
					$data_leitura = '';
					if (!empty($evento[0]['data_leitura'])):
						$data_leitura = AppModel::dbDateToDate($evento[0]['data_leitura']);
					endif;
					/*$tempo = '';
					if (!empty($evento[0]['tempo_total'])): 
						$tempo = Comum::decimal_to_time($evento[0]['tempo_total'],'minutos',false);
					endif;*/
				?>
				<tr>
					<td><?=$data_evento?></td>
					<td><?=$evento[0]['tipo']?></td>
					<td><?=nl2br($evento[0]['texto'])?></td>
					<td><?=$this->Buonny->posicao_geografica($evento[0]['refe_descricao'],$evento[0]['refe_latitude'],$evento[0]['refe_longitude'])?></td>
					<td><?=$evento[0]['operador']?></td>
					<td><?=$evento[0]['operador_tratou']?></td>
					<td><?=$data_leitura?></td>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>
</table>
