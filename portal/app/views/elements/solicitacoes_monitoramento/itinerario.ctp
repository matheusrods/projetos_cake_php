<div style="overflow-x:auto">
	<table class="table table-striped horizontal-scroll" style='width:2500px;max-width:none'>
		<thead>
			<tr>
				<th>Empresa</th>
				<th>Endereço</th>
				<th>Bairro</th>
				<th>Cidade</th>
				<th>Estado</th>
				<th>Telefone</th>
				<th class='numeric'>Peso Volume</th>
				<th>Nota Fiscal</th>
				<th>LoadPlan</th>
				<th>Série NF</th>
				<th class='numeric'>Valor Nota</th>
				<th>Data Previsão</th>
				<th>Início Janela</th>
				<th>Final Janela</th>
				<th>Status Chegada</th>
				<th>Status Janela</th>
				<th>Data Entrada</th>
				<th>Data Saída</th>
			</tr>
		</thead>
		<tbody>
			<?php $total_notas = 0 ?>
			<?php foreach($itinerario as $entrega): ?>
			<tr>
				<td><?php echo (isset($entrega['TRefeReferencia']['refe_latitude']) && !empty($entrega['TRefeReferencia']['refe_latitude']) ? $this->Buonny->posicao_geografica($entrega['TRefeReferencia']['refe_descricao'], $entrega['TRefeReferencia']['refe_latitude'], $entrega['TRefeReferencia']['refe_longitude']) : $entrega['TRefeReferencia']['refe_descricao']) ?></td>
				<td><?php echo $entrega['TRefeReferencia']['refe_endereco_empresa_terceiro'] ?></td>
				<td><?php echo $entrega['TRefeReferencia']['refe_bairro_empresa_terceiro'] ?></td>
				<td><?php echo $entrega['TCidaCidade']['cida_descricao'] ?></td>
				<td><?php echo $entrega['TEstaEstado']['esta_sigla'] ?></td>
				<td><?php echo $entrega['TProdProduto']['prod_descricao'] ?></td>
				<td class='numeric'><?php echo isset($entrega['TVnfiViagemNotaFiscal']['vnfi_peso']) ? $entrega['TVnfiViagemNotaFiscal']['vnfi_peso']: '' ?></td>
				<td class='numeric'><?php echo isset($entrega['TVnfiViagemNotaFiscal']['vnfi_volume']) ? $entrega['TVnfiViagemNotaFiscal']['vnfi_volume']: '' ?></td>
				<td><?php echo $entrega['TVnfiViagemNotaFiscal']['vnfi_numero'] ?></td>
				<td><?php echo $entrega['TVnfiViagemNotaFiscal']['vnfi_pedido'] ?></td>
				<td><?php echo isset($entrega['TVnfiViagemNotaFiscal']['vnfi_serie']) ? $entrega['TVnfiViagemNotaFiscal']['vnfi_serie']: '' ?></td>
				<td class='numeric'><?php echo number_format($entrega['TVnfiViagemNotaFiscal']['vnfi_valor'], 2, ',', '.') ?></td>
				<td><?php echo $entrega['TTparTipoParada']['tpar_descricao'] ?></td>
				<td><?php echo AppModel::dbDateToDate($entrega['TVlevViagemLocalEventoEntrada']['vlev_data_previsao']) ?></td>
				<td><?php echo AppModel::dbDateToDate($entrega['TVlocViagemLocal']['vloc_data_janela_inicio']) ?></td>
				<td><?php echo AppModel::dbDateToDate($entrega['TVlocViagemLocal']['vloc_data_janela_fim']) ?></td>
				<td><?php echo $entrega['status_chegada']?></td>
				<td><?php echo empty($entrega['TVlocViagemLocal']['vloc_data_janela_inicio']) ? '': $entrega['status_janela']?></td>
				<td><?php echo AppModel::dbDateToDate($entrega['TVlevViagemLocalEventoEntrada']['vlev_data']) ?></td>
				<td><?php echo AppModel::dbDateToDate($entrega['TVlevViagemLocalEventoSaida']['vlev_data']) ?></td>
				<td><?php echo Comum::convertToHoursMins($entrega[0]['tempo_descarga']) ?></td>
			</tr>			
			<?php $total_notas = $total_notas + $entrega['TVnfiViagemNotaFiscal']['vnfi_valor'] ?>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="10"></td>
				<td class='numeric'><?php echo number_format($total_notas, 2, ',', '.')?></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>
		</tfoot>
	</table>
</div>