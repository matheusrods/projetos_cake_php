<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("
          close_dialog();
          var div = jQuery('#endereco-prestador');
          bloquearDiv(div);
          div.load(baseUrl + 'prestadores_enderecos/listar/' + {$this->passedArgs[0]} + '/' + Math.random() );          
        	");
        exit;
    }
?>
<?php echo $bajax->form('PrestadorEndereco', array('url' => array('controller' => 'prestadores_enderecos','action' => 'incluir', $this->passedArgs[0]))); ?>
<?php echo $this->element('prestadores_enderecos/fields', array('edit_mode' => false)); ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>   
<?php echo $this->BForm->end(); ?>