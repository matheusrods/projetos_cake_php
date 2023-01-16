<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();carrega_operacoes_cliente(".$this->passedArgs[0].")");
        exit;
    }
?>
<?php echo $bajax->form('ClienteOperacao',array('url' => array('action' => 'incluir', $this->passedArgs[0]))) ?>
<?php echo $this->BForm->input('codigo_operacao', array('label' => 'Operação','options' => $operacoes, 'class' => 'text-large', 'empty' => 'Selecione uma operação')) ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>  
<?php echo $this->BForm->end(); ?>