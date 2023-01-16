<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        echo $javascript->codeBlock("close_dialog('{$this->Buonny->flash()}');carrega_historico_cliente('{$codigo_cliente}')");
        exit;
    }
?>
<?php echo $bajax->form('ClienteHistorico',array('url' => array('controller' => 'clientes_historicos', 'action' => 'incluir', $codigo_cliente))) ?>
<?php echo $this->BForm->hidden('codigo_cliente') ?>
<?php echo $this->BForm->hidden('codigo_tipo_historico') ?>
<?php echo $this->BForm->input('observacao', array('type' => 'textarea', 'class' => 'input-xxlarge')) ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>