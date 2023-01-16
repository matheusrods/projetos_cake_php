<?php
if ($this->Session->read('Message.flash.params.type') == MSGT_SUCCESS):
    echo $this->Javascript->codeBlock("close_dialog('{$this->Buonny->flash()}');atualizaListaClientesProdutos('gerenciar', codigo_cliente);");
    $this->Session->delete('Message.flash');
    exit;
endif;
?>
<?php echo $bajax->form('ClienteProdutoServico2', array('url' => array('action' => 'incluir', $this->passedArgs[0]))); ?>
<?php echo $this->element('clientes_produtos_servicos/fields', array('edit_mode' => false)); ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?php echo $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>   
<?php echo $this->BForm->end(); ?>