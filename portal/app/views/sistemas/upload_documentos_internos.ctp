<div class='form-procurar well'>
	<?php echo $this->BForm->create('Sistema', array('type' => 'file', 'autocomplete' => 'off', 'url' => array('controller' => 'sistemas', 'action' => 'upload_documentos_internos'))); ?>
		
		<?php echo $this->BForm->input('arquivo', array('type'=>'file', 'label' => false)); ?>
		<?php echo $this->BForm->submit('Enviar', array('div' => false)); ?>
	<?php echo $this->BForm->end(); ?>
</div>