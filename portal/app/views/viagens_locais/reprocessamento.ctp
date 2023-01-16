<?php if ($running): ?>
	<h2>Reprocessamento em execução</h2>
<?php endif ?>
<?php echo $this->BForm->create('TVlocViagemLocal', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens_locais', 'action' => 'reprocessamento'))) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('tipo_processamento', array('options' => array(1 => 'Período', 2 => 'SM'))); ?>
	</div>
	<div class="row-fluid inline tipos tipo-sm">
		<?php echo $this->BForm->input('TViagViagem.viag_codigo_sm', array('label' => false, 'placeholder' => 'Código SM', 'class' => 'input-small', 'type' => 'text')); ?>
	</div>
	<div class="row-fluid inline tipos tipo-periodo">
		<?php echo $this->Buonny->input_periodo($this, 'TVlocViagemLocal') ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
<?php echo $this->BForm->end();?>
<?php echo $this->Javascript->codeBlock("
	function habilitar() {
		$('.tipos').hide();
		if ($('#TVlocViagemLocalTipoProcessamento').val() == 1) {
			$('.tipo-periodo').show();
		} else {
			$('.tipo-sm').show();
		}
	}
	jQuery(document).ready(function() {
		$('#TVlocViagemLocalTipoProcessamento').change(function() {habilitar()});
		habilitar();	
	})
") ?>