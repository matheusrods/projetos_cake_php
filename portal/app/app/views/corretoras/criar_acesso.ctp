<?php echo $this->Buonny->flash(); ?>
<?php echo $bajax->form('Corretora', 
	array(	'model'=> 'Corretoras' ,
			'method' => 'POST' ,
			'url' => array('action' => 'criar_acesso', $dados['Corretora']['codigo']),
			'callback' => "function (){ AfterSave( ) }"
	)); ?>
<?php echo $this->BForm->input('success', array('type' => 'hidden', 'value' => $success))?>
<?php echo $this->BForm->input('codigo', array('type' => 'hidden', 'value' => $dados['Corretora']['codigo']))?>
<?php echo $this->BForm->input('nome', array('label' => 'Nome Corretora', 'value' => $dados['Corretora']['nome']))?>
<?php echo $this->BForm->input('user', array('label' => 'Nome Usuário', 'value' => $dados['Corretora']['nome']))?>
<?php echo $this->BForm->input('email', array('label' => 'E-mail (login)', 'value' => ''))?>

<div class="fullwide submit_box">
   <input type="submit" value="Enviar"></input>
</div>

<div class="clear"></div>

<script type="text/javascript">
	function AfterSave(  ){
		if(  $('#CorretoraSuccess').val() == 'yes' ){
			$("#modal_dialog").dialog('close');
			location.reload();
		}
	}
</script>

<?php echo $this->BForm->end() ?>