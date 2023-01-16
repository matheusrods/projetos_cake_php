<?php 
$placa = null;

?>

<div class='row-fluid inline'>
	<?php echo $this->BForm->input('embarcador', array('value'=>$embarcador, 'class' => 'input-xxlarge', 'readonly'=>'readonly', 'label'=> 'Embarcador' )); ?>
	<?php echo $this->BForm->input('transportador',array('value'=>$transportadora ,'class' => 'input-xxlarge', 'readonly'=>'readonly', 'label'=> 'Transportador' ));?>
</div>

<div class='row-fluid inline'>

	<?php echo $this->BForm->input('TMviaModeloViagem.mvia_cpf_motorista',array('label'=>'CPF', 'readonly'=>'readonly','class' => 'input-medium'));?>
	<?php echo $this->BForm->input('TMviaModeloViagem.nome',array('label'=>'Nome', 'readonly'=>'readonly','class' => 'input-large'));?>
</div>
<div class='row-fluid'>
	<table class='table table-striped'>
		<thead>
			<th class='input-large'>Placa</th>
			<th class='input-large'>Tipo</th>
		</thead>
		<tbody>
			<?php if(isset($this->data['TMvveModeloViagemVeiculos']) && $this->data['TMvveModeloViagemVeiculos']): ?>
				<?php foreach ($this->data['TMvveModeloViagemVeiculos'] as $key => &$mvve):?>
					<tr class='tablerow-input'>
						<td>
							<?php echo $this->BForm->input("TMvveModeloViagemVeiculos.{$key}.TMvveModeloViagemVeiculo.mvve_placa",array('label'=>false, 'readonly'=>'readonly','class' => 'input-small placa'));?>
						</td>
						<td>
							<?php echo $this->BForm->input("TMvveModeloViagemVeiculos.{$key}.TTveiTipoVeiculo.tvei_descricao",array('label'=>false, 'readonly'=>'readonly','class' => 'input-small'));?>
						</td>
					</tr>
				<?php endforeach?>
			<?php endif ?>	
		</tbody>		
	</table>
</div>

<div class='row-fluid'>
	<table class='table table-striped'>
		<thead>
			<th class='input-large'>Itinerario</th>
			<th>Tipo Itinerario</th>
		</thead>
		<tbody>
			<?php if(isset($this->data['TMvloModeloViagemLocais']) && $this->data['TMvloModeloViagemLocais']): ?>
				<?php foreach ($this->data['TMvloModeloViagemLocais'] as $key => &$mvlo):?>
					<tr class='tablerow-input'>
						<td>
							<?php echo $this->BForm->input("TMvloModeloViagemLocais.{$key}.TRefeReferencia.refe_descricao",array('label'=>false, 'readonly'=>'readonly','class' => 'input-xlarge'));?>
						</td>
						<td>
							<?php echo $this->BForm->input("TMvloModeloViagemLocais.{$key}.TTparTipoParada.tpar_descricao",array('label'=>false, 'readonly'=>'readonly','class' => 'input-small'));?>
						</td>
					</tr>
				<?php endforeach?>
			<?php endif ?>	
		</tbody>		
	</table>


		
<?php echo $this->BForm->end(); ?>