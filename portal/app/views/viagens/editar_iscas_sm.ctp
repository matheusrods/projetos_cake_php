<?php
	
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        echo $javascript->codeBlock("
        	var str = window.location.href;
        	str = str.substring(str.length - 1);
        	close_dialog();

        	if(str == '1'){
        		window.location = window.location;
        	}else{
        		window.location = window.location.href+'/1'
        	}

        ");       
        exit;
    }elseif($session->read('Message.flash.params.type') == MSGT_ERROR){
    	echo $this->Buonny->flash();
    }
?>

<?php echo $bajax->form('TTermTerminal', array('url' => array('controller' => 'Viagens', 'action' => 'editar_iscas_sm',$codigo_sm,$vter))); ?>
	<div class="row-fluid inline">
	    <?php echo $this->BForm->input("tecn_codigo", array('label' => 'Tecnologia', 'empty' => 'Tecnologia','class' => 'input-medium', 'options' => $tecnologias)) ?>
	    <?php echo $this->BForm->input("term_numero_terminal", array('label' => 'Terminal', 'class' => 'input-medium')) ?>
	</div>
	
	<div class="form-actions">
		<?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
	</div>
<?php echo $this->BForm->end(); ?>
