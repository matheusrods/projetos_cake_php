<table class='table table-striped destino' data-index='<?php echo $contador ?>'>
	<thead>
		<th>

			<div class="row-fluid inline">
				<div class="row-fluid inline">
					<?php $localizador_cliente2 = ($this->data['Recebsm']['codigo_cliente'] == $this->data['Recebsm']['embarcador'] ? '#RecebsmTransportador' : '#RecebsmEmbarcador') ?>
					<?php echo $this->Buonny->input_referencia($this, '#RecebsmCodigoCliente', 'RecebsmAlvoDestino', 'refe_codigo', $contador, 'Itinerario Alvo', true, true, $localizador_cliente2) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.dataFinal" , array('label' => 'Previsão Chegada', 'class' => 'data input-small', 'name' => 'data[RecebsmAlvoDestino]['.$contador.'][dataFinal]')) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.horaFinal" , array('label' => 'Hora', 'class' => 'hora input-mini', 'name' => 'data[RecebsmAlvoDestino]['.$contador.'][horaFinal]')) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.janela_inicio", array('label' => 'Janela Inicio', 'class' => 'hora input-mini', 'name' => 'data[RecebsmAlvoDestino]['.$contador.'][janela_inicio]')) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.janela_fim", array('label' => 'Janela Fim', 'class' => 'hora input-mini', 'name' => 'data[RecebsmAlvoDestino]['.$contador.'][janela_fim]')) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.tipo_parada", array('label' => 'Tipo Itinerario', 'class' => 'input-small', 'options' => $tipo_parada, 'empty' => 'Selecione um Tipo')) ?>
				</div>
				<div class="row-fluid inline">
					<div class="control-group input text">
						<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-destino-remove', 'escape' => false)); ?>
					</div>
				</div>
			</div>
		</th>
	</thead>
	<tbody>
		<tr>
			<td>
				<div class="row-fluid inline">
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.RecebsmNota.0.notaLoadplan", array('class' => 'input-medium', 'label' => false,'placeholder' => 'Loadplan/Chassi', 'maxlength' => 15)); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.RecebsmNota.0.notaNumero", array('class' => 'input-mini', 'label' => false,'placeholder' => 'Nº NF', 'maxlength' => 15)); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.RecebsmNota.0.notaSerie", array('class' => 'input-micro', 'label' => false,'placeholder' => 'Série', 'maxlength' => 10)); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.RecebsmNota.0.carga", array('class' => 'input-medium','options' => $tipo_carga , 'empty' => 'Produto','label' => false)) ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.RecebsmNota.0.notaValor", array('class' => 'input-small moeda', 'label' => false,'placeholder' => 'Valor da Nota')); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.RecebsmNota.0.notaVolume", array('class' => 'input-mini just-number', 'label' => false,'placeholder' => 'Volume', 'maxlength' => 9)); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.RecebsmNota.0.notaPeso", array('class' => 'input-mini just-number', 'label' => false,'placeholder' => 'Peso', 'maxlength' => 9)); ?>
					<?php echo $this->Html->link('<i class="icon-plus icon-white "></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-nota-fiscal', 'escape' => false)); ?>
				</div>
			</td>
		</tr>
	</tbody>
</table>