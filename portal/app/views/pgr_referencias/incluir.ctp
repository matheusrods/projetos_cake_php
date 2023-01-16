<?php echo $this->BForm->create('TPrefPgrReferencia', array('action' => 'post', 'url' => array('controller' => 'PgrReferencias','action' => 'incluir')));?>

<?php echo $this->element('pgr_referencias/fields') ?>

	<br>
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')) ?>
	<?php echo $html->link('Voltar', array('controller' =>'PgrReferencias', 'action' => 'index'), array('class' => 'btn')) ;?>
<?php echo $this->BForm->end() ?>

<?php echo $this->Javascript->codeBlock('

	$(document).ready(function(){
		setup_mascaras();

	});', false);
?>