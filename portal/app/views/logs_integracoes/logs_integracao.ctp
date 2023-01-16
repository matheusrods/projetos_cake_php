<tr>
	<td>
		<?php echo $this->BForm->input("Recebsm.loadplan.{$key}.codigo",array('class' => 'input-small', 'readonly' => TRUE)); ?>
	</td>
	<td>
		<?php echo $this->BForm->input("Recebsm.loadplan.{$key}.data_inicio",array('class' => 'input-small')); ?>
		<?php echo $this->BForm->input("Recebsm.loadplan.{$key}.hora_inicio",array('class' => 'input-mini')); ?>
	</td>
	<td>
		<?php echo $this->BForm->input("Recebsm.loadplan.{$key}.data_fim",array('class' => 'input-small')); ?>
		<?php echo $this->BForm->input("Recebsm.loadplan.{$key}.hora_fim",array('class' => 'input-mini')); ?>
	</td>
	<td>
		<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error remove-loadplan', 'escape' => false)); ?>
	</td>
</tr>