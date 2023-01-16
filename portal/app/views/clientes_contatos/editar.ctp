<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();carrega_contatos_cliente(".$this->data['ClienteContato']['codigo_cliente'].")");
        exit;
    }
?>
<?php echo $bajax->form('ClienteContato',array('url' => array('action' => 'editar', $this->data['ClienteContato']['codigo']))) ?>
<?php echo $this->element('clientes_contatos/fields'); ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end() ?>