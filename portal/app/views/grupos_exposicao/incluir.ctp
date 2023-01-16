<?php 
	echo $this->BForm->create('GrupoExposicao', 
			array('url' => Comum::splitURL( $_SERVER['REQUEST_URI'] ) )); 
?>
<?php echo $this->element('grupos_exposicao/fields', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>
