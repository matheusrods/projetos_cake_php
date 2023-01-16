<div class="content">
	<div class="tab-pane active" id="dados">
		<?php echo $this->BForm->create('Usuario', array('action' => 'editar_configuracao', $this->passedArgs[0] )); ?>
		<?php echo $this->element('usuarios/fields_configuracao'); ?>
	</div>
</div>
<?php $this->addScript($this->Javascript->codeBlock("
	jQuery(document).ready(function() {
		setup_mascaras();	
	})")) ;?>