<tr>
	<td>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input("TPjurEscolta.{$tabela}.Equipes.{$index}.equipe", array('type' => 'text', 'class' => 'input-large','label' => false, 'placeholder' => 'Equipe')); ?>
			<?php echo $this->BForm->input("TPjurEscolta.{$tabela}.Equipes.{$index}.telefone", array('type' => 'text', 'class' => 'input-medium telefone','label' => false, 'placeholder' => 'Telefone')); ?>
			<?php echo $this->BForm->input("TPjurEscolta.{$tabela}.Equipes.{$index}.placa", array('type' => 'text', 'class' => 'input-small placa-veiculo','label' => false, 'placeholder' => 'Placa')); ?>
			<?php echo $this->Html->link('<i class="icon-minus icon-black"></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-equipe-remove', 'escape' => false)); ?>
		</div>
	</td>
</tr>