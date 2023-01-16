<div id="loadplan" class='row-fluid inline destino' max-id="<?php echo count($this->data['RecebsmAlvoDestino']) ?>">
<?php if(count($this->data['RecebsmAlvoDestino']) > 0): ?>
	<h4>Origem</h4>
	<table>
		<tr>
			<td>
				<?php echo $this->BForm->input('Recebsm.operacao', array('label' => 'Tipo de Transporte', 'class' => 'input-medium','options' => $tipo_transporte , 'empty' => 'Selecione um Tipo')) ?>
				<?php echo $this->Buonny->input_referencia($this, '#RecebsmCodigoCliente', 'Recebsm', 'refe_codigo_origem', false, 'Local Origem', true, true, '#RecebsmEmbarcador') ?>
				<div class="control-group input text">
					<?php echo $this->BForm->input('Recebsm.dta_inc', array('label' => 'Data Inicio', 'class' => 'data input-small')) ?>
					<?php echo $this->BForm->input('Recebsm.hora_inc', array('label' => 'Hora', 'class' => 'hora input-mini')) ?>
					<?php echo $this->BForm->error('Recebsm.dta_hora_inc', null, array('style'=>"color:#b94a48; clear:both; margin-top: 60px; position: absolute;")) ?>
				</div>
			</td>
		</tr>

	</table>
	
	<h4>Loadplan</h4>
	<?php foreach ($this->data['RecebsmAlvoDestino'] AS $key => $Destino): ?>
		<table class='table table-striped destino' max-id="<?php echo $key ?>">
		<thead>
			<th>
				<div class="row-fluid inline">
					<?php echo $this->Buonny->input_referencia($this, '#RecebsmCodigoCliente', 'RecebsmAlvoDestino', 'refe_codigo', $key, 'Itinerario Alvo', true, true, '#RecebsmEmbarcador') ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.dataFinal", array('label' => 'Previsão Chegada', 'class' => 'data input-small')) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.horaFinal", array('label' => 'Hora', 'class' => 'hora input-mini')) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.janela_inicio", array('label' => 'Janela Inicio', 'class' => 'hora input-mini')) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.janela_fim", array('label' => 'Janela Fim', 'class' => 'hora input-mini')) ?>				    
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.tipo_parada", array('label' => 'Tipo Itinerario', 'class' => 'input-small', 'options' => $tipo_parada, 'empty' => 'Selecione um Tipo')) ?>
				</div>
			</th>
		</thead>
		<tbody>
			<tr>
				<td>
					<?php for ($keyNotas = 0; $keyNotas < (isset($this->data['RecebsmAlvoDestino'][$key]['RecebsmNota']) ? count($this->data['RecebsmAlvoDestino'][$key]['RecebsmNota']) : 1); $keyNotas++): ?>
						<div class="row-fluid inline">
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaLoadplan", array('class' => 'input-mini load-field', 'label' => 'Loadplan', 'maxlength' => 15, 'readonly' => TRUE)); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaNumero", array('class' => 'input-medium', 'label' => 'Nº NF', 'maxlength' => 15, 'readonly' => TRUE)); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaSerie", array('class' => 'input-micro', 'label' => 'Série', 'maxlength' => 10, 'readonly' => TRUE)); ?>
							<?php echo $this->BForm->hidden("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.carga") ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.produtoDescricao", array('class' => 'input-medium', 'empty' => 'Produto','label' => 'Produto', 'readonly' => TRUE)) ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaValor", array('class' => 'input-small moeda', 'label' => 'Valor da Nota', 'readonly' => TRUE)); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaVolume", array('class' => 'input-mini just-number', 'label' => 'Volume', 'maxlength' => 9, 'readonly' => TRUE)); ?>
							<?php echo $this->BForm->input("RecebsmAlvoDestino.{$key}.RecebsmNota.{$keyNotas}.notaPeso", array('class' => 'input-mini just-number', 'label' => 'Peso', 'maxlength' => 9, 'readonly' => TRUE)); ?>
						</div>
					<?php endfor ?>
				</td>

			</tr>
		</tbody>
	</table>
	<?php endforeach ?>
<?php endif ?>
</div>