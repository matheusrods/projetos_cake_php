<table class="table table-striped" style=''>
	<thead>
		<tr>
			<th class='input-medium'>Data Evento</th>
			<th class='input-medium'>Operador Gravação</th>
			<th>Evento</th>
			<th class='input-medium'>Data Leitura</th>
			<th class='input-medium'>Operador Tratou</th>
			<th class='input-small'>Tempo</th>
			<th style="text-align: center; width: 30px;">Status</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($this->data['Eventos'] as $evento): ?>
			<?
				$data_evento = AppModel::dbDateToDate($evento['TEsisEventoSistema']['esis_data_cadastro']);

				$data_leitura = '';
				//$tempo = '';
				if (!empty($evento['TEsisEventoSistema']['esis_data_leitura'])) {
					$data_leitura = AppModel::dbDateToDate($evento['TEsisEventoSistema']['esis_data_leitura']);
				}
				$tempo = Comum::decimal_to_time($evento[0]['tempo_total'],'minutos',false);
			?>
			<tr>
				<td><?=$data_evento?></td>
				<td><?=$evento['TPessPessoaAdicionou']['pess_nome']?></td>
				<td><?=$this->Buonny->evento($evento['TEspaEventoSistemaPadrao']['espa_descricao'],$evento['TEspaEventoSistemaPadrao']['espa_codigo'])?></td>
				<td><?=$data_leitura?></td>
				<td><?=$evento['TPessPessoaLeitura']['pess_nome']?></td>
				<td><?=$tempo?></td>
				<td style="text-align: center; width: 30px;">
					<? if ($evento[0]['dentro_sla']>=0) :?>
						<span class="badge-empty badge <?=($evento[0]['dentro_sla']==1?"badge-success":"badge-important")?>" title="<?=($evento[0]['dentro_sla']==1?"Dentro do Intervalo da SLA":"Fora do Intervalo da SLA")?>"></span>
					<? endif; ?>
				</td>
			</tr>
		<?php endforeach ?>
	</tbody>
</table>
