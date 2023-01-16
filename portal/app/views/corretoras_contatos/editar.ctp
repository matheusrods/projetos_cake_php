<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        
        $session->delete('Message.flash');
        echo $javascript->codeBlock("carrega_contatos_cliente('{$this->data['CorretoraContato']['codigo_corretora']}','div#contatos-corretora');close_dialog();"); 
        exit;
    }
?>
<?php echo $bajax->form('CorretoraContato',array('url' => array('controller'=>'corretoras_contatos','action' => 'editar', $this->data['CorretoraContato']['codigo_corretora']))) ?>
<?php echo $this->element('corretoras_contatos/fields'); ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php  echo $this->BForm->end() ?>