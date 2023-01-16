<div class='form-procurar well'>
	<?php echo $this->BForm->create('Sistema', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'sistemas', 'action' => 'conversor_folha'))); ?>
		<div class="row-fluid inline">
			<?php echo $this->BForm->input('tipo_arquivo', array('label' => false, 'class' => 'input-small', 'options' => array('1' => 'Sal', '2' => 'Contrib'))); ?>
			<?php echo $this->BForm->input('conta', array('label' => false, 'class' => 'input-large', 'placeholder' => 'Conta')); ?>
		</div>
		<?php echo $this->BForm->input('arquivo', array('type'=>'file', 'label' => false)); ?>
		<?php echo $this->BForm->submit('Converter', array('div' => false)); ?>
	<?php echo $this->BForm->end(); ?>
</div>