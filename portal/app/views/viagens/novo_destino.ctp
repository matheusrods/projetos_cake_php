<table class='table table-striped destino' data-index='<?php echo $contador ?>'>
	<thead>
		<th>

			<div class="row-fluid inline">
				<div class="row-fluid inline">
					<?php echo $this->BForm->input('TViagViagemAlvoDestino'.$contador, array('label' => 'Local Destino', 'class' => 'input-xlarge refe-complete', 'name' => 'data[TViagViagem][AlvoDestino]['.$contador.']')) ?>
					<?php echo $this->BForm->input('TViagViagemDataDestino'.$contador , array('label' => 'Previsão Chegada', 'class' => 'data input-small', 'name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$contador.'][dataFinal]')) ?>
					<?php echo $this->BForm->input('TViagViagemHoraDestino'.$contador , array('label' => 'Hora', 'class' => 'hora input-mini', 'name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$contador.'][horaFinal]')) ?>

					<?php echo $this->BForm->input('JanelaInicio'.$contador, array('label' => 'Janela', 'class' => 'hora input-mini', 'name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$contador.'][janela_inicio]')) ?>
					<?php echo $this->BForm->input('JanelaFim'.$contador, array('label' => '&nbsp', 'class' => 'hora input-mini', 'name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$contador.'][janela_fim]')) ?>

					<label for="TViagViagemDtaFim">&nbsp</label>
					<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-destino-remove', 'escape' => false)); ?>
				</div>
				
				<div class="row-fluid inline">
				    <?php echo $this->BForm->input('OperacaoDestino', array('label' => 'Tipo de Mercadoria', 'class' => 'input-medium','options' => $mercadorias ,'name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$contador.'][operacao]', 'empty' => 'Selecione um Tipo')) ?>
				    
				    <?php echo $this->BForm->input("TViagViagem.TViagViagemAlvoDestino.{$contador}.tipo_parada", array('label' => 'Tipo de Parada', 'class' => 'input-medium', 'options' => $paradas, 'empty' => 'Selecione um Tipo')) ?>
				</div>
			</div>
		</th>
	</thead>
	<tbody>
		<tr>
			<td>
				
				<div class="row-fluid inline">
					<?php echo $this->BForm->input('notaNumero', array('name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$contador.'][TViagViagemNota][0][notaNumero]', 'class' => 'input-mini', 'label' => false,'placeholder' => 'Nº NF')); ?>
					<?php echo $this->BForm->input('notaVolume', array('name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$contador.'][TViagViagemNota][0][notaVolume]', 'class' => 'input-small', 'label' => false,'placeholder' => 'Volume')); ?>
					<?php echo $this->BForm->input('notaPeso', array('name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$contador.'][TViagViagemNota][0][notaPeso]', 'class' => 'input-small', 'label' => false,'placeholder' => 'Peso')); ?>
					<?php echo $this->BForm->input('notaSerie', array('name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$contador.'][TViagViagemNota][0][notaSerie]', 'class' => 'input-micro', 'label' => false,'placeholder' => 'Série')); ?>
					<?php echo $this->BForm->input('notaValor', array('name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$contador.'][TViagViagemNota][0][notaValor]', 'class' => 'input-medium moeda', 'label' => false,'placeholder' => 'Valor da Nota')); ?>
					<?php echo $this->BForm->input('notaLoadplan', array('name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$contador.'][TViagViagemNota][0][notaLoadplan]', 'class' => 'input-small', 'label' => false,'placeholder' => 'Loadplan')); ?>
					<?php echo $this->Html->link('<i class="icon-plus icon-white "></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-nota-fiscal', 'escape' => false)); ?>
				</div>
			</td>

		</tr>
	</tbody>
</table>