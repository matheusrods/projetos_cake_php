<tr>
	<td>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('notaLoadplan', array('name' => 'data[RecebsmAlvoDestino]['.$tabela.'][RecebsmNota]['.$index.'][notaLoadplan]', 'class' => 'input-medium', 'label' => false,'placeholder' => 'Loadplan/Chassi', 'maxlength' => 15)); ?>
			<?php echo $this->BForm->input('notaNumero', array('name' => 'data[RecebsmAlvoDestino]['.$tabela.'][RecebsmNota]['.$index.'][notaNumero]', 'class' => 'input-mini', 'label' => false,'placeholder' => 'Nº NF', 'maxlength' => 15)); ?>
			<?php echo $this->BForm->input('notaSerie', array('name' => 'data[RecebsmAlvoDestino]['.$tabela.'][RecebsmNota]['.$index.'][notaSerie]', 'class' => 'input-micro', 'label' => false,'placeholder' => 'Série', 'maxlength' => 10)); ?>
			<?php echo $this->BForm->input('notaProduto', array('name' => 'data[RecebsmAlvoDestino]['.$tabela.'][RecebsmNota]['.$index.'][carga]','class' => 'input-medium','options' => $tipo_carga , 'empty' => 'Produto','label' => false)) ?>
			<?php echo $this->BForm->input('notaValor', array('name' => 'data[RecebsmAlvoDestino]['.$tabela.'][RecebsmNota]['.$index.'][notaValor]', 'class' => 'input-small moeda', 'label' => false,'placeholder' => 'Valor da Nota')); ?>
			<?php echo $this->BForm->input('notaVolume', array('name' => 'data[RecebsmAlvoDestino]['.$tabela.'][RecebsmNota]['.$index.'][notaVolume]', 'class' => 'input-mini just-number', 'label' => false,'placeholder' => 'Volume', 'maxlength' => 9)); ?>
			<?php echo $this->BForm->input('notaPeso', array('name' => 'data[RecebsmAlvoDestino]['.$tabela.'][RecebsmNota]['.$index.'][notaPeso]', 'class' => 'input-mini just-number', 'label' => false,'placeholder' => 'Peso', 'maxlength' => 9)); ?>
			
			<?php echo $this->Html->link('<i class="icon-plus icon-white " ></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-nota-fiscal', 'escape' => false, 'style'=>'display: none;')); ?>
			<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-nota-remove', 'escape' => false)); ?>
		</div>
	</td>

</tr>