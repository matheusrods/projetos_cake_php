<table class='table table-striped destino' data-index='<?php echo $contador ?>'>
	<thead>
		<th>

			<div class="row-fluid inline">
				<div class="row-fluid inline">
					<div class="refe_codigo_destino_select">
						<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.refe_codigo_select",array('label' => 'Itinerário Alvo','options' => array())); ?>
					</div>
					<div class="refe_codigo_destino">
						<?php echo $this->Buonny->input_referencia($this, '#RecebsmCodigoCliente', 'RecebsmAlvoDestino', 'refe_codigo', $contador, 'Itinerario Alvo', true, true, '#RecebsmCodigoCliente2') ?>
					</div>
					<div class="janela_select" style="display:none">
						<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.ccja_codigo",array('label' => 'Janela','options' => array())); ?>
					</div>
					<div class="janela">
						<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.janela_inicio", array('label' => 'Janela Inicio', 'class' => 'hora input-mini janela-inicio', 'name' => 'data[RecebsmAlvoDestino]['.$contador.'][janela_inicio]')) ?>
						<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.janela_fim", array('label' => 'Janela Fim', 'class' => 'hora input-mini janela-fim', 'name' => 'data[RecebsmAlvoDestino]['.$contador.'][janela_fim]')) ?>
					</div>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.dataFinal" , array('label' => 'Previsão Chegada', 'class' => 'data input-small', 'name' => 'data[RecebsmAlvoDestino]['.$contador.'][dataFinal]', 'default' => date('d/m/Y'))) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.horaFinal" , array('label' => 'Hora', 'class' => 'hora input-mini hora-final', 'name' => 'data[RecebsmAlvoDestino]['.$contador.'][horaFinal]')) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.tipo_parada", array('label' => 'Tipo Itinerario', 'class' => 'input-small', 'value' => 'ENTREGA', 'readonly' => true)) ?>
					<div class="control-group input text">
						<label>&nbsp;</label>
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
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.RecebsmNota.0.notaNumero", array('class' => 'input-mini', 'label' => 'Nº NF', 'maxlength' => 15, 'default' => '000000')); ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.RecebsmNota.0.carga", array('class' => 'input-medium carga-produtos','options' => $tipo_carga , 'empty' => 'Produto','label' => 'Produto', 'default' => $unico_produto == null ? '' : $unico_produto)) ?>
					<?php echo $this->BForm->input("RecebsmAlvoDestino.{$contador}.RecebsmNota.0.notaValor", array('class' => 'input-small moeda', 'label' => 'Valor da Nota', 'default' => '0,00')); ?>
					<label>&nbsp;</label>
					<?php echo $this->Html->link('<i class="icon-plus icon-white "></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-nota-fiscal', 'escape' => false)); ?>
				</div>
			</td>
		</tr>
	</tbody>
</table>