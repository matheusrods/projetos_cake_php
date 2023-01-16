<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
//        echo $javascript->codeBlock("close_dialog();carrega_endereco_cliente('{$this->passedArgs[0]}')");
          echo $javascript->codeBlock("close_dialog();");
        exit;
    }
?>

<?php echo $this->BForm->create('ClienteProdutoContrato');?>
<?php echo $this->element('clientes_produtos_contratos/fields', array('edit_mode' => true)); ?>

