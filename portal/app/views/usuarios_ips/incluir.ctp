<?php	
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');        
        echo $javascript->codeBlock("close_dialog();atualizaListaIps('".$codigo_usuario."');");
        exit;
    }else if($session->read('Message.flash.params.type') == MSGT_ERROR){
      $session->delete('Message.flash');
    }
?>
<?php echo $bajax->form('UsuarioIp',array('url' => array('controller' => 'usuarios_ips', 'action' => 'incluir', $codigo_usuario),'type' => 'post') ) ?>
<div class="row-fluid">
	<?php echo $this->BForm->input('endereco_ip', array('label' => 'Endereço IP','empty' => NULL,'class' => 'input-large')); ?>
</div>
<div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
      <?= $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>