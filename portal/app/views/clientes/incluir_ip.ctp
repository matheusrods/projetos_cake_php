<?php
	
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        echo $this->Javascript->codeBlock("
        	$(function(){
        		window.location = window.location;
        		close_dialog();
        	})
        ");
        exit;
    }
?>

<?php echo $this->Bajax->form('ClienteIp',array('url' => array('controller' => 'Clientes','action' => 'incluir_ip',$codigo_cliente))) ?>

	<div class="form-actions">
		<?php echo $this->BForm->hidden('codigo_cliente') ?>
		<?php echo $this->BForm->input('descricao',array('class' => 'input-large', 'label' => 'Endereço IP')) ?>
	</div>
	<div class="form-actions">
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
		<?php echo $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
	</div>
<?php echo $this->BForm->end(); ?>
