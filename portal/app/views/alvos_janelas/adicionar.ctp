<?php

if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
	//$session->delete('Message.flash');
	echo $javascript->codeBlock("close_dialog();atualizaListaConfiguracaoAlvoJanela();");
	exit;
}else{
	echo $this->Buonny->flash();
}
?>
<?php echo $bajax->form('TCajaConfAlvoJanela',array('url' => array('controller' => 'alvos_janelas', 'action' => 'adicionar', $codigo_cliente_pjur, $codigo_referencia),'type' => 'post') ) ?>

<div class="row-fluid inline">
	<?php echo $this->BForm->input("janela_inicio", array('label' => 'Janela Inicio', 'class' => 'hora input-mini')) ?>
	<?php echo $this->BForm->input("janela_fim", array('label' => 'Janela Fim', 'class' => 'hora input-mini')) ?>
</div>

<div class="form-actions">
	<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	<?= $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function(){
		setup_time();
	});
'); ?>