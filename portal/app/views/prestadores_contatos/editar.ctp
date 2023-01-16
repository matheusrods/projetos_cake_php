<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("
    		close_dialog();
    		var div = jQuery('#prestador-contatos');
    		bloquearDiv(div);
    		div.load(baseUrl + 'prestadores_contatos/contatos_por_prestador/' + {$this->data['PrestadorContato']['codigo_prestador']} + '/' + Math.random() );");
        exit;
    }
?>
<?php echo $this->Bajax->form('PrestadorContato',array('url' => array('controller'=>'prestadores_contatos','action' => 'editar', $this->passedArgs[0] ))) ?>
<?php echo $this->element('prestadores_contatos/fields'); ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>