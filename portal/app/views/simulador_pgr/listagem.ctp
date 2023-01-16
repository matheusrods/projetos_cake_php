<?php if(isset($listagem) && !empty($listagem)):?>
	<div class="well">
		<table width="100%">
			<tr>
				<td width="50%"><strong>PGR: </strong><?= $listagem['pgr']?></td>
				<td><strong>Tipo: </strong><?= $listagem['tipo_pgr']?></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Placa: </strong><?= strtoupper($listagem['placa'])?></td>
				<td><strong>Embarcador: </strong><?= $listagem['embarcador']?></td>
			</tr>
			<tr>
				<td><strong>Tecnlogia: </strong><?= $listagem['tecnologia']?></td>
				<td><strong>Transportador: </strong><?= $listagem['transportador']?></td>
			</tr>
			<tr>
				<td colspan="2"><strong>Versão: </strong><?= $listagem['versao']?></td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td><strong>Valor: </strong><?= $listagem['valor']?></td>
				<td><strong>Alvo Origem: </strong><?= $listagem['alvo_origem']?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><strong>Tipo de Transporte: </strong><?= $listagem['tipo_transporte']?></td>
			</tr>
		</table>
	</div>	
	<?php $classe = 'btn-warning';?>
	<table class='table table-striped' style='max-width:none;white-space:nowrap'>
		<thead>
			<th class="input-mini">Regra</th>
			<th>Descrição</th>
			<th>Sistema</th>
		</thead>
		<tbody>
			<tr>
				<td class=<?= ($listagem['regra'] == 1) ? $classe : ''?>>
					1
				</td>
				<td class=<?= ($listagem['regra'] == 1) ? $classe : ''?>>
					PGR por Alvo Origem
				</td>
				<td class=<?= ($listagem['regra'] == 1) ? $classe : ''?>>
					Portal
				</td>
			</tr>
			<tr>
				<td class=<?= ($listagem['regra'] == 2) ? $classe : ''?>>
					2
				</td>
				<td class=<?= ($listagem['regra'] == 2) ? $classe : ''?>>
					PGR para Embarcador , Transportador e Tipo de Transporte.
				</td>
				<td class=<?= ($listagem['regra'] == 2) ? $classe : ''?>>
					Portal
				</td>
			</tr>
			<tr>
				<td class=<?= ($listagem['regra'] == 3) ? $classe : ''?>>
					3
				</td>
				<td class=<?= ($listagem['regra'] == 3) ? $classe : ''?>>
					PGR para Embarcador e Tipo de Transporte.
				</td>
				<td class=<?= ($listagem['regra'] == 3) ? $classe : ''?>>
					Portal
				</td>
			</tr>
			<tr>
				<td class=<?= ($listagem['regra'] == 4) ? $classe : ''?>>
					4
				</td>
				<td class=<?= ($listagem['regra'] == 4) ? $classe : ''?>>
					PGR para Transportador e Tipo de Transporte.
				</td>
				<td class=<?= ($listagem['regra'] == 4) ? $classe : ''?>>
					Portal
				</td>
			</tr>
			<tr>
				<td class=<?= ($listagem['regra'] == 5) ? $classe : ''?>>
					5
				</td>
				<td class=<?= ($listagem['regra'] == 5) ? $classe : ''?>>
					PGR para Embarcador ,Valor e Tecnologia.
				</td>
				<td class=<?= ($listagem['regra'] == 5) ? $classe : ''?>>
					Guardian
				</td>
			</tr>
			<tr>
				<td class=<?= ($listagem['regra'] == 6) ? $classe : ''?>>
					6
				</td>
				<td class=<?= ($listagem['regra'] == 6) ? $classe : ''?>>
					PGR para Embarcador e Tecnologia.
				</td>
				<td class=<?= ($listagem['regra'] == 6) ? $classe : ''?>>
					Guardian
				</td>
			</tr>
			<tr>
				<td class=<?= ($listagem['regra'] == 7) ? $classe : ''?>>
					7
				</td>
				<td class=<?= ($listagem['regra'] == 7) ? $classe : ''?>>
					PGR para Embarcador e Valor.
				</td>
				<td class=<?= ($listagem['regra'] == 7) ? $classe : ''?>>
					Guardian
				</td>
			</tr>
			<tr>
				<td class=<?= ($listagem['regra'] == 8) ? $classe : ''?>>
					8
				</td>
				<td class=<?= ($listagem['regra'] == 8) ? $classe : ''?>>
					PGR para Embarcador (Sem valor e tecnologia).
				</td>
				<td class=<?= ($listagem['regra'] == 8) ? $classe : ''?>>
					Guardian
				</td>
			</tr>
			<tr>
				<td class=<?= ($listagem['regra'] == 9) ? $classe : ''?>>
					9
				</td>
				<td class=<?= ($listagem['regra'] == 9) ? $classe : ''?>>
					PGR para Transportador ,Valor e Tecnologia.
				</td>
				<td class=<?= ($listagem['regra'] == 9) ? $classe : ''?>>
					Guardian
				</td>
			</tr>
			<tr>
				<td class=<?= ($listagem['regra'] == 10) ? $classe : ''?>>
					10
				</td>
				<td class=<?= ($listagem['regra'] == 10) ? $classe : ''?>>
					PGR para Transportador e Tecnologia.
				</td>
				<td class=<?= ($listagem['regra'] == 10) ? $classe : ''?>>
					Guardian
				</td>
			</tr>
			<tr>
				<td class=<?= ($listagem['regra'] == 11) ? $classe : ''?>>
					11
				</td>
				<td class=<?= ($listagem['regra'] == 11) ? $classe : ''?>>
					PGR para Transportador e Valor.
				</td>
				<td class=<?= ($listagem['regra'] == 11) ? $classe : ''?>>
					Guardian
				</td>
			</tr>
			<tr>
				<td class=<?= ($listagem['regra'] == 12) ? $classe : ''?>>
					12
				</td>
				<td class=<?= ($listagem['regra'] == 12) ? $classe : ''?>>
					PGR para Transportador (Sem valor e tecnologia).
				</td>
				<td class=<?= ($listagem['regra'] == 12) ? $classe : ''?>>
					Guardian
				</td>
			</tr>
			<tr>
				<td class=<?= ($listagem['regra'] == 'P_L') ? $classe : ''?>>
					Padrão Logistico
				</td>
				<td class=<?= ($listagem['regra'] == 'P_L') ? $classe : ''?>>
					PGR Padrão Logistico.
				</td>
				<td class=<?= ($listagem['regra'] == 'P_L') ? $classe : ''?>>
					Portal
				</td>
			</tr>
			<tr>
				<td class=<?= ($listagem['regra'] == 'P_GR') ? $classe : ''?>>
					Padrão GR
				</td>
				<td class=<?= ($listagem['regra'] == 'P_GR') ? $classe : ''?>>
					PGR Padrão GR.
				</td>
				<td class=<?= ($listagem['regra'] == 'P_GR') ? $classe : ''?>>
					Portal
				</td>
			</tr>
		</tbody>	

	</table>
<?php else:?>
	<div class="alert">Nenhum registro encontrado</div>	
<?php endif;?>