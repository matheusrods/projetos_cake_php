<?php
	
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaClienteGerenciadoras(".$codigo_cliente.");");
        exit;
    }else if($session->read('Message.flash.params.type') == MSGT_ERROR){
    	$session->delete('Message.flash');
    }
?>
<?php echo $bajax->form('TGpjuGerenciadoraPessoaJur',array('url' => array('controller' => 'clientes', 'action' => 'adicionar_gerenciadora', $codigo_cliente),'type' => 'post') ) ?>
<?php echo $this->BForm->hidden('gpju_pjur_oras_codigo'); ?>
<div class="row-fluid">
	<?php echo $this->BForm->input('gpju_gris_oras_codigo', array('label' => 'Gerenciadora','empty' => 'Selecione uma gerenciadora' ,'options' => $gerenciadoras,'class' => 'input-xxlarge')); ?>
</div>
<div class="form-actions">
      <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
      <?= $html->link('Cancelar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>