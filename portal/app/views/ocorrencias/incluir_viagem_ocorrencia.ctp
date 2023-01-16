
<?php
	
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $this->Javascript->codeBlock("
        	$(function(){
        		listaOcorrencias({$viag_codigo});
        		close_dialog();
        	})
        ");
        exit;
    }
?>

<?php echo $this->Bajax->form('TVocoViagemOcorrencia',array('url' => array('controller' => 'Ocorrencias','action' => 'incluir_viagem_ocorrencia',$viag_codigo))) ?>

	<div class="form-actions">
		<?php echo $this->BForm->hidden('voco_viag_codigo') ?>
		<?php echo $this->BForm->input('voco_descricao',array('class' => 'input-xxlarge', 'label' => 'Descrição', 'type' => 'textarea')) ?>
	</div>
	<div class="form-actions">
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		<?php echo $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>
