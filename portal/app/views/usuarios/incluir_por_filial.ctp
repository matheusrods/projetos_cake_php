<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        echo $javascript->codeBlock("close_dialog('{$this->Buonny->flash()}');atualizaListaUsuariosPorFilial({$this->passedArgs[0]})");
        exit;
    }
?>
<?php echo $this->Buonny->flash(); ?>
<?php echo $this->Bajax->form('Usuario', array('url' => array('action' => 'incluir_por_filial', $this->passedArgs[0]))); ?>
<?php echo $this->element('usuarios/fields_por_filial'); ?>