<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();carrega_endereco_cliente('{$this->passedArgs[0]}')");
        exit;
    }
?>
<?php echo $bajax->form('ClienteEndereco', array('url' => array('action' => 'incluir', $this->passedArgs[0]))); ?>
<?php echo $this->element('clientes_enderecos/fields_clientes_enderecos', array('edit_mode' => false)); ?>
<?php echo $this->BForm->end(); ?>