<?php
if ($session->read('Message.flash.params.type') == MSGT_SUCCESS):
    $session->delete('Message.flash.params.type');
    echo $javascript->codeBlock("close_dialog('{$this->Buonny->flash()}');carrega_relacionamentos_cliente({$this->passedArgs[0]});");
    exit;
endif;
?>
<?php echo $bajax->form('ClienteRelacionamento', array('url' => array('action' => 'incluir', $this->data['ClienteRelacionamento']['codigo_cliente']))); ?>
<?php echo $this->BForm->hidden('codigo_cliente')?>
<?php echo $this->BForm->input('codigo_cliente_relacao', array('label' => 'Código Cliente', 'maxlength' => 10, 'class' => 'input-small')); ?>
<?php echo $this->BForm->input('razao_social', array('label' => 'Razão Social','disabled' => true, 'class' => 'input-xxlarge')); ?>
<?php echo $this->BForm->input('codigo_tipo_relacionamento', array('label' => 'Tipo do relacionamento:', 'empty' => 'Selecione...', 'options' => $tipo_relacionamentos, 'class' => 'input-medium')); ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>  
<?php echo $this->BForm->end() ?>