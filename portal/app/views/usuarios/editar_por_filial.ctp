<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        echo $javascript->codeBlock("close_dialog('{$this->Buonny->flash()}');atualizaListaUsuariosPorFilial({$this->data['Usuario']['codigo_filial']});");
        exit;
    }
    echo $javascript->codeBlock("
    	var number=Math.floor((Math.random()*999999)+1);
    	$('.novaSenha').click(function(){document.getElementById('UsuarioSenha').value=number});
    ");
?>
<?php echo $this->Bajax->form('Usuario', array('url' => array('action' => 'editar_por_filial', $this->passedArgs[0]) )); ?>
<?php echo $this->element('usuarios/fields_por_filial'); ?>