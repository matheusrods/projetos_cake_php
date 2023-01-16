<tr>
	<td>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('notaNumero', array('name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$tabela.'][TViagViagemNota]['.$index.'][notaNumero]', 'class' => 'input-medium', 'label' => false,'placeholder' => 'Nº Nota Fiscal')); ?>
			<?php echo $this->BForm->input('notaVolume', array('name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$tabela.'][TViagViagemNota]['.$index.'][notaVolume]', 'class' => 'input-small', 'label' => false,'placeholder' => 'Volume')); ?>
			<?php echo $this->BForm->input('notaPeso', array('name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$tabela.'][TViagViagemNota]['.$index.'][notaPeso]', 'class' => 'input-small', 'label' => false,'placeholder' => 'Peso')); ?>
			<?php echo $this->BForm->input('notaSerie', array('name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$tabela.'][TViagViagemNota]['.$index.'][notaSerie]', 'class' => 'input-small', 'label' => false,'placeholder' => 'Série')); ?>
			<?php echo $this->BForm->input('notaValor', array('name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$tabela.'][TViagViagemNota]['.$index.'][notaValor]', 'class' => 'input-medium moeda', 'label' => false,'placeholder' => 'Valor da Nota')); ?>
			<?php echo $this->BForm->input('notaLoadplan', array('name' => 'data[TViagViagem][TViagViagemAlvoDestino]['.$tabela.'][TViagViagemNota]['.$index.'][notaLoadplan]', 'class' => 'input-small', 'label' => false,'placeholder' => 'Loadplan')); ?>
			<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-nota-remove', 'escape' => false)); ?>
		</div>
	</td>

</tr>