<div class='row-fluid inline destino' data-index="<?php echo $contador ?>">
	<?php echo $this->Buonny->input_referencia($this, '#TRotaRotaCodigoCliente', 'TRotaRota.Itinerario', 'refe_codigo_destino', $contador); ?>
	<?php echo $this->BForm->input("TRotaRota.Itinerario.{$contador}.tipo_parada", array('label' => false, 'class' => 'input-medium', 'options' => $tipo_parada, 'empty' => 'Tipo Itinerario')) ?>
	<?php echo $this->Html->link('<i class="icon-minus icon-black "></i>', 'javascript:void(0)',array('class' => 'btn btn-error novo-destino-remove', 'escape' => false)); ?>
</div>