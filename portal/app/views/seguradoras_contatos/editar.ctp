<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $this->Javascript->codeBlock("carrega_contatos_seguradora('{$this->data['SeguradoraContato']['codigo_seguradora']}','div#contatos-seguradoras');close_dialog();"); 
        exit;
    }
?>
<?php echo $this->Bajax->form('SeguradoraContato',array('url' => array('controller'=>'seguradoras_contatos','action' => 'editar', $this->data['SeguradoraContato']['codigo_seguradora']))) ?>
<?php echo $this->element('seguradoras_contatos/fields'); ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>