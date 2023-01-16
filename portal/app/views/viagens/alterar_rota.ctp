<?php echo $this->BForm->create('Viagens', array('type' => 'post', 'autocomplete' => 'off', 'url' => array('controller' => 'viagens', 'action' => 'alterar_rota'))) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('codigo_sm', array('label' => false, 'placeholder' => 'Código SM','class' => 'input-small just-number', 'type' => 'text', 'maxlength' => 12 )); ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
<?php echo $this->BForm->end();?>