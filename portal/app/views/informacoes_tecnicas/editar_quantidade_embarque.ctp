<?php

    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaInformacoesTecnicas();");
        exit;
    }
    if($session->read('Message.flash.params.type') == MSGT_ERROR){        
        echo '<div class="'.MSGT_ERROR.'">'.$session->read('Message.flash.message').'</div>';
        $session->delete('Message.flash');
    }
?>
<?php echo $bajax->form('QuantidadeEmbarque', array('url' => array('controller' => 'informacoes_tecnicas', 'action' => 'editar_quantidade_embarque')) ) ?>
	<?php echo $this->BForm->hidden('codigo') ?>
	<?php echo $this->BForm->hidden('codigo_cliente') ?>
	<div class='row-fluid inline'>
		<?php echo $this->BForm->input('diario', array('label' => 'Diário','class' => 'input-medium just-number')) ?>
		<?php echo $this->BForm->input('semanal', array('label' => 'Semanal','class' => 'input-medium just-number')) ?>
		<?php echo $this->BForm->input('mensal', array('label' => 'Mensal','class' => 'input-medium just-number')) ?>
	</div>
	<div class="form-actions">
		  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		  <?= $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
	</div>

<?php echo $this->BForm->end(); ?>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras();
        
    });', false);