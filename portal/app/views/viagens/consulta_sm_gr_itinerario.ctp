	<div class='row-fluid inline' style='display:none' id='tempo_restante'>
		<table class='table'>
			<thead>
				<th>Posicao Atual</th>
				<th>Destino</th>
				<th class='numeric'>Distancia restante</th>
				<th>Tempo restante</th>
			</thead>
			<tr>
				<td id="PosicaoAtual"></td>
				<td id="PosicaoDestino"></td>
				<td id="RestanteDistancia" class='numeric'></td>
				<td id="RestanteTempo"></td>
			</tr>
		</table>
		<?php $this->addScript($this->Javascript->codeBlock("tempo_restante_sm({$this->data['TViagViagem']['viag_codigo_sm']})")) ?>
	</div>
	<?php echo $this->element('viagens/origem_destino') ?>
	<?php echo $this->element('viagens/itinerario') ?>
