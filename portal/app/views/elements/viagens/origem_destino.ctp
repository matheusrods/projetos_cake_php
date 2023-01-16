<div style="overflow-x:auto">
	<table class="table table-striped horizontal-scroll" style='width:2500px;max-width:none'>
		<thead>
			<tr>
				<th></th>
				<th>Empresa</th>
				<th>Endereço</th>
				<th>Bairro</th>
				<th>Cidade</th>
				<th>Estado</th>
				<th>Data Previsão</th>
				<th>Status Chegada</th>
				<th>Data Entrada</th>
				<th>Data Saída</th>
				<th>Data Abertura Baú</th>
				<th>Data Fechamento Baú</th>
				<th>Tempo Baú</th>
			</tr>
		</thead>
		<tbody>
			<?php $tempo_bau_origem = 0; ?>
			<?php foreach($origem_destino as $tipo_parada): ?>
				<?php $tempo_bau_origem += $tipo_parada[0]['tempo_descarga']?>
				<tr>
				<td><?php echo $tipo_parada['TTparTipoParada']['tpar_descricao'] ?></td>
				<td><?php echo (isset($tipo_parada['TRefeReferencia']['refe_latitude']) && !empty($tipo_parada['TRefeReferencia']['refe_latitude']) ? $this->Buonny->posicao_geografica($tipo_parada['TRefeReferencia']['refe_descricao'], $tipo_parada['TRefeReferencia']['refe_latitude'], $tipo_parada['TRefeReferencia']['refe_longitude']) : $tipo_parada['TRefeReferencia']['refe_descricao']) ?></td>
				<td><?php echo $tipo_parada['TRefeReferencia']['refe_endereco_empresa_terceiro'] ?></td>
				<td><?php echo $tipo_parada['TRefeReferencia']['refe_bairro_empresa_terceiro'] ?></td>
				<td><?php echo $tipo_parada['TCidaCidade']['cida_descricao'] ?></td>
				<td><?php echo $tipo_parada['TEstaEstado']['esta_sigla'] ?></td>
				<td><?php echo AppModel::dbDateToDate($tipo_parada['TVlevViagemLocalEventoEntrada']['vlev_data_previsao']) ?></td>
				<td><?php echo $tipo_parada['status_chegada']?></td>
				<td><?php echo AppModel::dbDateToDate($tipo_parada['TVlevViagemLocalEventoEntrada']['vlev_data']) ?></td>
				<td><?php echo AppModel::dbDateToDate($tipo_parada['TVlevViagemLocalEventoSaida']['vlev_data']) ?></td>
				<td><?php echo AppModel::dbDateToDate($tipo_parada['TVlocViagemLocal']['vloc_data_abertura_bau']) ?></td>
				<td><?php echo AppModel::dbDateToDate($tipo_parada['TVlocViagemLocal']['vloc_data_fechamento_bau']) ?></td>
				<td><?php echo Comum::convertToHoursMinsSecs($tipo_parada[0]['tempo_descarga']) ?></td>
				</tr>
			<?php endforeach ?>
		</tbody>
	</table>
</div>
<?$this->data['tempo_bau_origem'] = $tempo_bau_origem;?>