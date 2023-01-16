<?php 
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();carrega_endereco_cliente('{$codigo_cliente}')");
        exit;
    }
?>

<?php echo $bajax->form('ClienteEndereco', array('url' => array('action' => 'atualizar', $this->passedArgs[0]))); ?>
	<?php echo $this->element('clientes_enderecos/fields_clientes_enderecos', array('edit_mode' => true)); ?>
<?php echo $this->BForm->end(); ?>