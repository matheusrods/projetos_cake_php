
<?php
	
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();window.location = window.location;");
        exit;
    }
?>

<?php echo $this->Bajax->form('TRefeReferencia',array('url' => array('controller' => 'Referencias','action' => 'ativar',$codigo_referencia))) ?>
	<?php echo $this->BForm->hidden('refe_codigo') ?>
	<div class="form-actions">
		<div class="form-actions alert-error veiculo-error" style="display: block;">
			<?php echo 'Deseja ativar a Referencia ID '.$codigo_referencia ?>
		</div>

		<?php echo $this->BForm->submit('Ativar', array('div' => false, 'class' => 'btn btn-success')); ?>
		<?php echo $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>