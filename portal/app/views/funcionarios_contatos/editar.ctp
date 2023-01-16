<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();carrega_contatos_funcionario(".$this->data['FuncionarioContato']['codigo_funcionario'].")");
        exit;
    }
?>
<?php echo $bajax->form('FuncionarioContato',array('url' => array('controller' => 'funcionarios_contatos', 'action' => 'editar', $this->data['FuncionarioContato']['codigo']))) ?>
<?php echo $this->element('funcionarios_contatos/fields'); ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end() ?>