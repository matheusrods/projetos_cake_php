	<?php echo $this->BForm->create('TViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'historico_posicoes_alvos'))) ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('viag_codigo_sm', array('label' => false, 'placeholder' => 'Código SM', 'class' => 'input-small', 'type' => 'text')); ?>
			<?php echo $this->BForm->input('tipo', array('label' => false, 'class' => 'input-medium', 'options' => array(1 => 'Alvo vs Historico', '2' => 'Historico vs Alvo'))); ?>
			<?php echo $this->Buonny->input_codigo_cliente_base($this, 'codigo_cliente', 'Cliente', false, 'TViagViagem') ?>
			<?php echo $this->Buonny->input_referencia($this, '#TViagViagemCodigoCliente', 'TViagViagem') ?>
		</div>
		<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end();?>
<?php if (isset($dados) && count($dados)>0): ?>
	<div id="cliente" class='well'>
		<strong>SM: </strong><?= $this->Buonny->codigo_sm($viagem['TViagViagem']['viag_codigo_sm']) ?>
		<strong>Data Cadastro: </strong><?= $viagem['TViagViagem']['viag_data_cadastro'] ?>
		<strong>Data Início: </strong><?= $viagem['TViagViagem']['viag_data_inicio'] ?>
		<?= empty($viagem['TViagViagem']['viag_data_inicio']) ? '' : $tipoInicioFimViagem['inicio'] ?>
		<?= $mini_monitora_inicio['TMiniMonitoraInicio']['mini_observacao'].'-('.$mini_monitora_inicio['TMiniMonitoraInicio']['mini_data_inicializacao'].')' ?>
		<strong>Data Fim: </strong><?= $viagem['TViagViagem']['viag_data_fim'] ?>
		<?= empty($viagem['TViagViagem']['viag_data_inicio']) ? '' : $tipoInicioFimViagem['fim'] ?>
		<?php if (isset($outra_viagem) && !empty($outra_viagem)): ?>
			<?= ' Finalização forçada por '. $this->Buonny->codigo_sm($outra_viagem['TViagViagem']['viag_codigo_sm']) ?>
		<?php endif ?>
	</div>
	<?php if ($this->data['TViagViagem']['tipo'] == 1): ?>
		<table class='table table-striped' style="width:2800px;max-width:none">
			<THEAD>
				<tr>
					<th>Seq</th>
					<th>Alvo</th>
					<th>Status</th>
					<th>Data Posição</th>
					<th>Data Comp.Bordo</th>
					<th>No Alvo</th>
					<th>Em Viagem</th>
					<th>Dentro Lat</th>
					<th>Dentro Long</th>
					<th>Descrição Posição</th>
					<th>Lat Alvo</th>
					<th>Long Alvo</th>
					<th>Lat Posição</th>
					<th>Long Posição</th>
					<th>Lat Alvo Min</th>
					<th>Lat Alvo Max</th>
					<th>Long Alvo Min</th>
					<th>Long Alvo Max</th>
					<th class='numeric'>Hodômetro</th>
				</tr>
			</THEAD>
			<?php foreach ($dados as $dado): ?>
				<?php $color = (strtolower($dado['0']['em_viagem']) == 'sim' && strtolower($dado['0']['no_alvo']) == 'sim' ? 'background-color:#99CC99' : '') ?>
				<tr>
					<td style='<?= $color ?>'><?= $dado['TVlocViagemLocal']['vloc_sequencia'] ?></td>
					<td style='<?= $color ?>'><?= $dado['TRefeReferencia']['refe_descricao'] ?></td>
					<td style='<?= $color ?>'><?= (in_array($dado['TVlocViagemLocal']['vloc_status_viagem'], array('E','D'))) && AppModel::dateToDbDate2($dado['TRposRecebimentoPosicao']['rpos_data_computador_bordo']) >= $dado['0']['data_entrada']  ? $dado['TVlocViagemLocal']['vloc_status_viagem'] : '' ?></td>
					<td style='<?= $color ?>'><?= $dado['TRposRecebimentoPosicao']['rpos_data_cadastro'] ?></td>
					<td style='<?= $color ?>'><?= $dado['TRposRecebimentoPosicao']['rpos_data_computador_bordo'] ?></td>
					<td style='<?= $color ?>'><?= $dado['0']['no_alvo'] ?></td>
					<td style='<?= $color ?>'><?= $dado['0']['em_viagem'] ?></td>
					<td style='<?= $color ?>'><?= $dado['0']['dentro_latitude'] ?></td>
					<td style='<?= $color ?>'><?= $dado['0']['dentro_longitude'] ?></td>
					<td style='<?= $color ?>'><?= $dado['TRposRecebimentoPosicao']['rpos_descricao_sistema'] ?></td>
					<td style='<?= $color ?>'><?= $dado['TRefeReferencia']['refe_latitude'] ?></td>
					<td style='<?= $color ?>'><?= $dado['TRefeReferencia']['refe_longitude'] ?></td>
					<td style='<?= $color ?>'><?= $dado['TRposRecebimentoPosicao']['rpos_latitude'] ?></td>
					<td style='<?= $color ?>'><?= $dado['TRposRecebimentoPosicao']['rpos_longitude'] ?></td>
					<td style='<?= $color ?>'><?= $dado['TRefeReferencia']['refe_latitude_min'] ?></td>
					<td style='<?= $color ?>'><?= $dado['TRefeReferencia']['refe_latitude_max'] ?></td>
					<td style='<?= $color ?>'><?= $dado['TRefeReferencia']['refe_longitude_min'] ?></td>
					<td style='<?= $color ?>'><?= $dado['TRefeReferencia']['refe_longitude_max'] ?></td>
					<td class='numeric' style='<?= $color ?>'><?= $dado['TRposRecebimentoPosicao']['rpos_hodometro'] ?></td>
				</tr>
			<?php endforeach ?>
		</table>
	<?php else: ?>
		<table class='table table-striped'>
			<THEAD>
				<tr>
					<th>Data Posição</th>
					<th>Data Comp.Bordo</th>
					<th>Descrição Posição</th>
					<th>Seq</th>
					<th>Alvo</th>
					<th>Status</th>
					<th>Em Viagem</th>
					<th class='numeric'>Hodômetro</th>
				</tr>
			</THEAD>
			<?php foreach ($dados as $dado): ?>
				<?php $color = (strtolower($dado['0']['em_viagem']) == 'sim' && !empty($dado['0']['alvo']) ? 'background-color:#99CC99' : '') ?>
				<tr>
					<td style='<?= $color ?>'><?= $dado['TRposRecebimentoPosicao']['rpos_data_cadastro'] ?></td>
					<td style='<?= $color ?>'><?= $dado['TRposRecebimentoPosicao']['rpos_data_computador_bordo'] ?></td>
					<td style='<?= $color ?>'><?= $dado['TRposRecebimentoPosicao']['rpos_descricao_sistema'] ?></td>
					<td style='<?= $color ?>'><?= $dado['0']['vloc_sequencia'] ?></td>
					<td style='<?= $color ?>'><?= $dado['0']['alvo'] ?></td>
					<td style='<?= $color ?>'><?= (in_array($dado['0']['vloc_status_viagem'], array('E','D'))) && AppModel::dateToDbDate2($dado['TRposRecebimentoPosicao']['rpos_data_computador_bordo']) >= $dado['0']['data_entrada']  ? $dado['0']['vloc_status_viagem'] : '' ?></td>
					<td style='<?= $color ?>'><?= $dado['0']['em_viagem'] ?></td>
					<td class='numeric' style='<?= $color ?>'><?= $dado['TRposRecebimentoPosicao']['rpos_hodometro'] ?></td>
				</tr>
			<?php endforeach ?>
		</table>
	<?php endif; ?>
	<?php echo $this->Buonny->link_css('jquery.tablescroll'); ?>
		<?php echo $this->Buonny->link_js('jquery.tablescroll'); ?>
		<?php if ($this->data['TViagViagem']['tipo'] == 1): ?>
			<?php $width = 2800 ?>
		<?php else: ?>
			<?php $width = 1200 ?>
		<?php endif ?>
			<?php echo $this->Javascript->codeBlock("
			    jQuery(document).ready(function(){
			        $('.table').tableScroll({width:{$width}, height:(window.innerHeight-380)});
			    });", false);
			?>
<?php endif ?>