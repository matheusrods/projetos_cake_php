<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();
    			var div = jQuery('#endereco-regulador');
    			bloquearDiv(div);
    			div.load(baseUrl + 'reguladores_enderecos/listar/' + {$codigo_regulador} + '/' + Math.random() );
        	");
        exit;
    }
?>
<?php echo $bajax->form('ReguladorEndereco', array('url' => array('controller' => 'reguladores_enderecos','action' => 'atualizar', $this->passedArgs[0]))); ?>
<?php echo $this->element('reguladores_enderecos/fields', array('edit_mode' => false)); ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>