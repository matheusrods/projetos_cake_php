<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaListaRegrasAceiteSm({$this->data['TRacsRegraAceiteSm']['racs_pjur_pess_oras_codigo']});");
        exit;
    }
?>
<?= $this->BForm->create('TRacsRegraAceiteSm', array('type' => 'post', 'autocomplete' => 'off', 'url' => array('controller' => 'regras_aceite_sm', 'action' => 'editar', $racs_codigo, $pjur_pess_oras_codigo))) ?>
	<?= $this->element('regras_aceite_sm/fields') ?>
	<div class="form-actions">
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	  	<?= $html->link('Cancelar', array('controller' => 'clientes', 'action' => 'editar_configuracao', $this->data['TRacsRegraAceiteSm']['codigo_cliente']), array('class' => 'btn')); ?>
	</div>
<?= $this->BForm->end() ?>
<?= $this->Javascript->codeBlock("jQuery(document).ready(function() {setup_mascaras()})") ?>