
<?php
	
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();window.location = window.location;");
        exit;
    }
?>

<?php echo $this->Bajax->form('TRegiRegiao',array('url' => array('controller' => 'Regioes','action' => 'remover',$regiao['TRegiRegiao']['regi_codigo']))) ?>
	<div class="form-actions">
		<?php if($remover): ?>
			<?php echo $this->BForm->hidden('regi_codigo') ?>
			<?php echo $this->BForm->error_menssage('Deseja remover a região ID '.$regiao['TRegiRegiao']['regi_codigo'].' - '.$regiao['TRegiRegiao']['regi_descricao']) ?>
			<?php echo $this->BForm->submit('Remover', array('div' => false, 'class' => 'btn btn-danger')); ?>
		<?php else: ?>
			<?php echo $this->BForm->error_menssage('Não é possível remover a região ID '.$regiao['TRegiRegiao']['regi_codigo'].' - '.$regiao['TRegiRegiao']['regi_descricao']) ?>
		<?php endif; ?>

		<?php echo $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>