<?php
	
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();window.location = window.location;");
        exit;
    }
?>

<?php echo $this->Bajax->form('TBandBandeira',array('url' => array('controller' => 'Bandeiras','action' => 'remover',$bandeira['TBandBandeira']['band_codigo']))) ?>
	<div class="form-actions">
		<?php if($remover): ?>
			<?php echo $this->BForm->hidden('band_codigo') ?>
			<?php echo $this->BForm->error_menssage('Deseja remover a bandeira ID '.$bandeira['TBandBandeira']['band_codigo'].' - '.$bandeira['TBandBandeira']['band_descricao']) ?>
			<?php echo $this->BForm->submit('Remover', array('div' => false, 'class' => 'btn btn-danger')); ?>
		<?php else: ?>
			<?php echo $this->BForm->error_menssage('Não é possível remover a bandeira ID '.$bandeira['TBandBandeira']['band_codigo'].' - '.$bandeira['TBandBandeira']['band_descricao']) ?>
		<?php endif; ?>

		<?php echo $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>