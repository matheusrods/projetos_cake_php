<table class='table table-striped' data-index="<?php echo $contador ?>">
	<thead>
		<th>
			<div class="row-fluid inline">
				<?php echo $this->BForm->input("TPjurEscolta.{$contador}.pjur_razao_social", array('label' => 'Empresa de Escolta', 'class' => 'input-xxlarge escolta-complete')) ?>
				<div class="control-group input text">
					<label for="RecebsmDtaFim">&nbsp</label>
					<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-escolta-remove', 'escape' => false)); ?>
				</div>
			</div>
		</th>
	</thead>
	
	<tbody>
		<tr>
			<td>
				<div class="row-fluid inline">
					<?php echo $this->BForm->input("TPjurEscolta.{$contador}.Equipes.0.equipe", array('class' => 'input-large','label' => false, 'placeholder' => 'Equipe')); ?>
					<?php echo $this->BForm->input("TPjurEscolta.{$contador}.Equipes.0.telefone", array('class' => 'input-medium telefone','label' => false, 'placeholder' => 'Telefone')); ?>
					<?php echo $this->BForm->input("TPjurEscolta.{$contador}.Equipes.0.placa", array( 'class' => 'input-small placa-veiculo','label' => false, 'placeholder' => 'Placa')); ?>
					<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success novo-equipe', 'escape' => false)); ?>
				</div>
			</td>
		</tr>
	</tbody>

</table>