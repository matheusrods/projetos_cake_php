<?php if(empty($historicoJornada)): ?>
	<div class='well'>
		<div class="row-fluid inline">
			<strong>Empregado: <?php echo !empty($motorista['Motorista']['Nome']) ? $motorista['Motorista']['Nome']: '' ?></strong> <br/>
			<strong>CNH: <?php echo !empty($motorista['Motorista']['CNH']) ? $motorista['Motorista']['CNH']: '' ?><strong><br/>
		</div>	
	</div>	
<?php else: ?>
<?php foreach($historicoJornada as $jornada): ?>
	<div class='well'>
		<div class="row-fluid inline">
			<strong>Empregador: <?php echo $jornada['TPjurPessoaJuridica']['pjur_razao_social'] ?></strong><br/>
			<strong>Endereço: 
					<?php echo $jornada['TPjurPessoaJuridica']['Endereco']['EnderecoTipo']['descricao'] ?> 
					<?php echo $jornada['TPjurPessoaJuridica']['Endereco']['Endereco']['descricao'] ?>,
					<?php echo $jornada['TPjurPessoaJuridica']['Endereco']['EnderecoCidade']['descricao'] ?> - 
					<?php echo $jornada['TPjurPessoaJuridica']['Endereco']['EnderecoEstado']['abreviacao'] ?>
			<strong><br/>
			<strong>CNPJ : <?php echo $jornada['TPjurPessoaJuridica']['pjur_cnpj'] ?></strong><br/>
		</div>	
	</div>	
	<div class='well'>
		<div class="row-fluid inline">
			<strong>Empregado: <?php echo !empty($motorista['Motorista']['Nome']) ? $motorista['Motorista']['Nome']: '' ?></strong> <br/>
			<strong>CNH: <?php echo !empty($motorista['Motorista']['CNH']) ? $motorista['Motorista']['CNH']: '' ?><strong><br/>
		</div>	
	</div>	
	<div id="jornada_motorista" >
	<table class='table table-borded'>
		<thead>
			<tr style="border:none">
				<th class='input-small' style="vertical-align:middle;" rowspan='2'>SM E VEÍCULO</th>
				<th class='input-small' style="vertical-align:middle;" rowspan='2'>DATA DE SAÍDA</th>
				<th class='input-small' style="vertical-align:middle;" rowspan='2'>HORA DE SAÍDA</th>
				<th class='input-small' style="vertical-align:middle;" rowspan='2'>DATA DE CHEGADA</th>
				<th class='input-small' style="vertical-align:middle;" rowspan='2'>HORA DE CHEGADA</th>
				<th class='input-large' colspan="4">
					Intervalo de Descanço/Refeição
				</th>
				<th class='input-large' colspan="2">
					Hora parada Carregamento/Espera
				</th>			
			</tr>
			<tr  style="border:none;box-shadow:none">
				<th  style="border-top:none"></th>
				<th  style="border-top:none">Início</th>
				<th  style="border-top:none">Término</th>
				<th  style="border-top:none">Observação</th>
				<th  style="border-top:none">Início</th>
				<th  style="border-top:none">Término</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($jornada['TViagViagem'] as $sm => $jornada_sms): ?>
				<?php $num_rows = count($jornada['TViagViagem'][$sm]['Eventos'])?>
				<?php $num_rows = ($num_rows < 1)?1:$num_rows ?>
				<tr>
					<td rowspan='<?php echo $num_rows ?>'><?php echo $sm.' '.$jornada_sms['viag_veic_placa'] ?></td>
					<td rowspan='<?php echo $num_rows ?>'><?php echo !empty($jornada['TViagViagem'][$sm]['viag_data_inicio']) ? substr($jornada['TViagViagem'][$sm]['viag_data_inicio'], 0, 10): '' ?></td>
					<td rowspan='<?php echo $num_rows ?>'><?php echo !empty($jornada['TViagViagem'][$sm]['viag_data_inicio']) ? substr($jornada['TViagViagem'][$sm]['viag_data_inicio'], 11, 8):'' ?></td>
					<td rowspan='<?php echo $num_rows ?>'><?php echo !empty($jornada['TViagViagem'][$sm]['viag_data_fim']) ? substr($jornada['TViagViagem'][$sm]['viag_data_fim'], 0, 10): '' ?></td>
					<td rowspan='<?php echo $num_rows ?>'><?php echo !empty($jornada['TViagViagem'][$sm]['viag_data_fim']) ? substr($jornada['TViagViagem'][$sm]['viag_data_fim'], 11, 8): '' ?></td>
				<?php if($jornada['TViagViagem'][$sm]['Eventos']): ?>
					<?php $cont = 0 ?>
					<?php foreach($jornada['TViagViagem'][$sm]['Eventos'] as $evento): ?>
						<td><?php echo ++$cont ?></td>
						<?php if(!$evento['CargaDescarga']): ?>
							<td><?php echo !empty($evento['TRmacRecebimentoMacro']['rmac_data_inicio']) ? date('d/m/Y H:i:s', strtotime($evento['TRmacRecebimentoMacro']['rmac_data_inicio'])): '' ?></td>
							<td><?php echo !empty($evento['TRmacRecebimentoMacro']['rmac_data_fim']) ? date('d/m/Y H:i:s', strtotime($evento['TRmacRecebimentoMacro']['rmac_data_fim'])): '' ?></td>
							<td><?php echo $evento['TMpadMacroPadrao']['mpad_descricao'] ?></td>
							<td></td>
							<td></td>
						<?php else: ?>
							<td></td>
							<td></td>
							<td></td>
							<td><?php echo !empty($evento['TRmacRecebimentoMacro']['rmac_data_inicio']) ? date('d/m/Y H:i:s', strtotime($evento['TRmacRecebimentoMacro']['rmac_data_inicio'])): ''?></td>
							<td><?php echo !empty($evento['TRmacRecebimentoMacro']['rmac_data_fim']) ? date('d/m/Y H:i:s', strtotime($evento['TRmacRecebimentoMacro']['rmac_data_fim'])): ''?></td>
						<?php endif; ?>
						</tr>
						<tr>
					<?php endforeach; ?>
				<?php else: ?>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
				<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	</div>
<?php endforeach; ?>
<?php endif; ?>