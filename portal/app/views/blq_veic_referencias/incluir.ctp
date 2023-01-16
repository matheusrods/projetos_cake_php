<?php echo $this->BForm->create('TBvreBlqVeicReferencia', array('action' => 'post', 'url' => array('controller' => 'BlqVeicReferencias','action' => 'incluir')));?>

<?php echo $this->element('blq_veic_referencias/fields') ?>

	<br>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')) ?>
	<?php echo $html->link('Voltar', array('controller' =>'BlqVeicReferencias', 'action' => 'index'), array('class' => 'btn')) ;?>
<?php echo $this->BForm->end() ?>

<?php echo $this->Javascript->codeBlock('

	$(document).ready(function(){
		setup_mascaras();

	});', false);
?>