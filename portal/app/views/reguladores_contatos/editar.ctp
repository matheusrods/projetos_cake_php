<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("
    		close_dialog();
    		var div = jQuery('#regulador-contatos');
    		bloquearDiv(div);
    		div.load(baseUrl + 'reguladores_contatos/contatos_por_regulador/' + {$this->data['ReguladorContato']['codigo_regulador']} + '/' + Math.random() );");
        exit;
    }
?>
<?php echo $this->Bajax->form('ReguladorContato',array('url' => array('controller'=>'reguladores_contatos','action' => 'editar', $this->passedArgs[0] ))) ?>
<?php echo $this->element('reguladores_contatos/fields'); ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>