<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();carrega_endereco_fornecedor('{$this->passedArgs[0]}')");
        exit;
    }
?>
<?php echo $bajax->form('FornecedorEndereco', array('url' => array('action' => 'incluir', $this->passedArgs[0]))); ?>
<?php echo $this->element('fornecedores_enderecos/fields', array('edit_mode' => false)); ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>   
<?php echo $this->BForm->end(); ?>