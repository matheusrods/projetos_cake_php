<tr>
	<td>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('notaNumero', array('name' => 'data[RecebsmAlvoDestino]['.$tabela.'][RecebsmNota]['.$index.'][notaNumero]', 'class' => 'input-mini', 'label' => 'Nº NF', 'maxlength' => 15, 'default' => '000000',)); ?>
			<?php echo $this->BForm->input('notaProduto', array('name' => 'data[RecebsmAlvoDestino]['.$tabela.'][RecebsmNota]['.$index.'][carga]','class' => 'input-medium carga-produtos','options' => $tipo_carga , 'empty' => 'Produto','label' => 'Produto', 'default' => $unico_produto == null ? '' : $unico_produto)) ?>
			<?php echo $this->BForm->input('notaValor', array('name' => 'data[RecebsmAlvoDestino]['.$tabela.'][RecebsmNota]['.$index.'][notaValor]', 'class' => 'input-small moeda', 'label' => 'Valor da Nota', 'default' => '0,00')); ?>
			<label>&nbsp;</label>
			<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-nota-remove', 'escape' => false)); ?>
		</div>
	</td>

</tr>