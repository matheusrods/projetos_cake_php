<div>
<?php foreach ($dados_sm as $codigo_sm => $sm): ?>
	<div class="well" id="cliente">
		<strong>SM: </strong><?php echo $this->Buonny->codigo_sm( $codigo_sm );?>
	</div>
	<?php echo $this->element('viagens/origem_destino', array('origem_destino' => $sm['origem_destino'])) ?>
	<?php if ( count( $sm['itinerario'] ) ) : ?>			
	<?php echo $this->element('viagens/itinerario', array('itinerario' => $sm['itinerario'])) ?>			
	<?php endif ?>
	<hr />
<?php endforeach; ?>
</div>