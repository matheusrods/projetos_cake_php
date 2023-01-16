<div class='well'>
	<?php echo $this->BForm->create('TViagViagem', array('autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'eventos_logisticos_por_placa'))) ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('placa', array('class' => 'input-small placa-veiculo', 'label' => false, 'placeholder' => 'Placa', 'readonly' => $this->layout == 'new_window')) ?>
			<?php echo $this->BForm->hidden('data_inicial') ?>
			<?php echo $this->BForm->hidden('data_final') ?>
		</div>
		<?php echo $this->BForm->submit('Gerar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $this->BForm->end() ?>
</div>
<?php $this->addScript($this->Javascript->codeBlock("jQuery(document).ready(function() {
	setup_mascaras();
})")) ?>