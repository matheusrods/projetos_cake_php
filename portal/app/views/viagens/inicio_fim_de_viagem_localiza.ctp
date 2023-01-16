
<?php echo $this->BForm->create('TViagViagem', array('action' => 'post', 'url' => array('controller' => 'Viagens','action' => 'inicio_fim_de_viagem_localiza')));?>

<div class='row-fluid inline'>	
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('viag_codigo_sm', array('class' => 'input-small', 'label' => 'SM')) ?> 
	</div>
</div>

<?php echo $this->BForm->submit('Localizar', array('div' => false, 'class' => 'btn btn-success')) ?>
<?php echo $this->BForm->end() ?>