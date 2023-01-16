<div style="overflow-x:auto">
	<table class="table table-striped horizontal-scroll" style='width:2500px;max-width:none'>
		<thead>
			<tr>
				<th>Empresa</th>
				<th>Endereço</th>
				<th>Bairro</th>
				<th>Cidade</th>
				<th>Estado</th>
				<th>Produto</th>
				<th class='numeric'>Peso</th>
				<th class='numeric'>Volume</th>
				<th>Nota Fiscal</th>
				<th>LoadPlan</th>
				<th>Série NF</th>
				<th class='numeric'>Valor Nota</th>
				<th>Tipo Itin.</th>
				<th>Data Previsão</th>
				<th>Início Janela</th>
				<th>Final Janela</th>
				<th>Status Chegada</th>
				<th>Status Janela</th>
				<th>Data Entrada</th>
				<th>Data Saída</th>
				<th>Data Abertura Baú</th>
				<th>Data Fechamento Baú</th>
				<th>Tempo Baú</th>
			</tr>
		</thead>
		<tbody>
			<?php $total_notas = 0;?>
			<?php $total_descarga = (!empty($this->data['tempo_bau_origem']) ? $this->data['tempo_bau_origem'] : 0);?>
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
				<td><?php echo AppModel::dbDateToDate($entrega['TVlocViagemLocal']['vloc_data_abertura_bau']) ?></td>
				<td><?php echo AppModel::dbDateToDate($entrega['TVlocViagemLocal']['vloc_data_fechamento_bau']) ?></td>
				<td><?php echo Comum::convertToHoursMinsSecs($entrega[0]['tempo_descarga']) ?></td>
			</tr>
			<?php $total_notas = $total_notas + $entrega['TVnfiViagemNotaFiscal']['vnfi_valor'] ?>
			<?php $total_descarga = $total_descarga + $entrega[0]['tempo_descarga'] ?>
			<?php endforeach ?>
		</tbody>
		<tfoot>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td class='numeric'><?php echo number_format($total_notas, 2, ',', '.')?></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
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
<div class='well'>
	<?php $viag_data_inicio = !empty($this->data['TViagViagem']['viag_data_inicio']) ? $this->data['TViagViagem']['viag_data_inicio'] : ( !empty($this->data['QViagViagem']['viag_data_inicio']) ? $this->data['QViagViagem']['viag_data_inicio'] : NULL ); ?>
	<?php $viag_data_fim    = !empty($this->data['TViagViagem']['viag_data_fim']) ? $this->data['TViagViagem']['viag_data_fim'] : ( !empty($this->data['QViagViagem']['viag_data_fim']) ? $this->data['QViagViagem']['viag_data_fim'] : NULL ); ?>
	<?php $viag_data_fim    = empty($viag_data_fim) ? date('Y-m-d H:i:s') : AppModel::dateToDbDate2($viag_data_fim); ?>
	<?php if (empty($viag_data_inicio)): ?>
		<?php $tempo_viagem = 0 ?>
	<?php else: ?>
		<?php $tempo_viagem = (strtotime($viag_data_fim) - strtotime(AppModel::dateToDbDate2($viag_data_inicio)) ) / 60 ?>
	<?php endif ?>
	<strong>Tempo da SM: </strong><?php echo Comum::convertToHoursMinsSecs($tempo_viagem) ?>
	<strong>Tempo Baú: </strong><?php echo Comum::convertToHoursMinsSecs($total_descarga) ?>
	<strong>Tempo Rota: </strong><?php echo Comum::convertToHoursMinsSecs($tempo_viagem - $total_descarga) ?>
</div>