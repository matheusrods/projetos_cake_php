<?php
	if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
		$this->Session->delete('Message.flash');
		echo $javascript->codeBlock("atualiza_informacoes_prestadores(".$pagina.");close_dialog();");
		exit;
	}else{
		echo $this->Buonny->flash();
		$this->Session->delete('Message.flash');
	}
?>
	<?php echo $bajax->form('HistoricoSmPrestador',array('url' => array('controller' => 'prestadores', 'action' => 'alterar_valores', $codigo, $pagina),'type' => 'post') ) ?>
	<?php echo $this->BForm->input('codigo', array('type' => 'hidden', 'value' => $codigo)) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input("valor_honorarios", array('label' => 'Valor Honorários', 'class' => 'input-small moeda  numeric ', 'maxlength' => 10 )) ?>
		<?php echo $this->BForm->input("valor_despesas", array('label' => 'Valor Despesas', 'class' => 'input-small moeda  numeric ', 'maxlength' => 10)) ?>
		<?php echo $this->BForm->input("quantia_km", array('label' => 'Quilômetro', 'class' => 'input-small  moeda numeric ', 'maxlength' => 10)) ?>
	</div>

	<div class="form-actions">
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		<?= $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
	</div>
	<?php echo $this->BForm->end();?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
		setup_mascaras();
	});
');
?>