<?php echo $this->BForm->input('TMviaModeloViagem.mvia_codigo') ?>

<h4>Embarcador / Transportador</h4>
<div class='row-fluid inline'>
	<?php 
		if($embarcador_read)
			$emb_readonly = array('label' => 'Embarcador','readonly' => $embarcador_read , 'options' => $embarcadores, 'class' => 'input-xxlarge');
		else
			$emb_readonly = array('label' => 'Embarcador','readonly' => $embarcador_read ,'empty' => 'Selecione um Embarcador', 'options' => $embarcadores, 'class' => 'input-xxlarge');

		echo $this->BForm->input('Recebsm.embarcador', $emb_readonly);
	?>
</div>		
<div class='row-fluid inline'>
	<?php 
		if($transportador_read)
			$tra_readonly = array('label' => 'Transportador','readonly' => $transportador_read, 'options' => $transportadores, 'class' => 'input-xxlarge', 'error'=>array('escape'=>false));
		else
			$tra_readonly = array('label' => 'Transportador','readonly' => $transportador_read, 'empty' => 'Selecione um Transportador', 'options' => $transportadores, 'class' => 'input-xxlarge', 'error'=>array('escape'=>false));

		echo $this->BForm->input('Recebsm.transportador', $tra_readonly);
	?>
</div>

<h4>Motorista</h4>
<?php if($remonta == 'S'): ?>
	<?php echo $this->BForm->input('Recebsm.sem_motorista', array('label' => 'Sem motorista', 'type' => 'checkbox')) ?>
<?php endif; ?>
<div class='row-fluid inline motorista-data'>
	<?php echo $this->BForm->hidden('Profissional.codigo') ?>
	<?php echo $this->BForm->hidden('Profissional.estrangeiro') ?>
	<?php echo $this->BForm->input('Recebsm.codigo_documento', array('label' => false, 'class' => 'input-medium formata-rne', 'placeholder' => 'CPF', 'div'=>array('class'=>'control-group input text documento'), 'error'=>array('escape'=>false))) ?>
	<?php echo $this->BForm->input('Recebsm.nome', array('label' => false, 'class' => 'input-large','placeholder' => 'Nome', 'readonly'=>true)) ?>
	<?php echo $this->BForm->input('Recebsm.telefone', array('label' => false, 'class' => 'input-medium telefone','placeholder' => 'Telefone')) ?>
	<?php echo $this->BForm->input('Recebsm.radio', array('label' => false, 'class' => 'input-medium','placeholder' => 'Radio')) ?>

</div>
	
<div class='row-fluid inline'>
	<h4>Veículos</h4>
	<table class='table table-striped veiculos'>
		<thead>
			<th class='input-medium'><?php echo $remonta == 'S' ? 'Chassi' : 'Placa' ?></th>
			<th class='input-medium'>Tipo Veiculo</th>
			<th class='input-large'><?php echo $remonta == 'S' ? NULL : 'Tecnologia' ?></th>
			<th></th>
		</thead>
		<tbody>
			<?php $campo = $remonta == 'S' ? 'chassi' : 'placa'; ?>
			<?php if (isset($this->data['Recebsm'][$campo])): ?>
				<?php if($remonta == 'S'): ?>	
					<tr class='tablerow-input'>
						<td><?php echo $this->BForm->input('Recebsm.chassi', array('label' => false, 'class' => 'input-medium chassi','placeholder' => 'Chassi' , 'name' => "data[Recebsm][chassi][]", 'value'=>$this->data['Recebsm']['chassi'])) ?></td>
						<td><?php echo $this->BForm->input('Recebsm.tipo', array('label' => false, 'class' => 'input-medium tipo','placeholder' => 'Tipo do Veículo', 'name' => "data[Recebsm][tipo][]", 'empty'=>'Selecione um tipo de veículo', 'value'=>$this->data['Recebsm']['tipo'], 'options'=>$tipos_veiculos)) ?></td>
					</tr>		
				<?php endif;?>
				<?php if($remonta != 'S'): ?>
					<?php foreach($this->data['Recebsm'][$campo] as $key => $campo_valor): ?>
					<tr class='tablerow-input'>
						<td><?php echo $this->BForm->input('Recebsm.placa', array('label' => false, 'class' => 'input-small placa-veiculo','placeholder' => 'Placa' , 'name' => "data[Recebsm][placa][]", 'onkeyup'=>'consulta_tipo_placa(jQuery(this).parent().parent().parent())', 'value'=>$this->data['Recebsm']['placa'][$key])) ?></td>
						<td><?php echo $this->BForm->input('Recebsm.tipo', array('label' => false, 'class' => 'input-medium tipo','placeholder' => 'Tipo do Veículo', 'name' => "data[Recebsm][tipo][]", 'readonly' => true, 'value'=>$this->data['Recebsm']['tipo'][$key])) ?></td>
						<td><?php echo $this->BForm->input('Recebsm.tecnologia', array('label' => false, 'class' => 'input-medium','placeholder' => 'Tecnologia', 'name' => "data[Recebsm][tecnologia][]", 'readonly' => true, 'value'=>$this->data['Recebsm']['tecnologia'][$key])) ?></td>
						<td>
							<?php if($key == 0): ?>
								<?php echo $this->Html->link('<i class="icon-plus icon-white"></i>', 'javascript:void(0)',array('class' => 'btn btn-success', 'escape' => false, 'onclick' => "adiciona_placa(jQuery(this).parent().parent())")); ?>
							<?php else: ?>
								<?php echo $this->Html->link('<i class="icon-minus"></i>', 'javascript:void(0)',array('class' => 'btn', 'escape' => false, 'onclick' => "remove_placa(jQuery(this).parent().parent())")); ?>
							<?php endif; ?>							
						</td>
					</tr>
				<?php endforeach ?>
				<?php endif;?>	
			<?php endif ?>
		</tbody>
	</table>
</div>