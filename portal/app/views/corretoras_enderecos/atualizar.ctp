<?php 
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();carrega_endereco_corretora('{$codigo_corretora}')");
        exit;
    }
?>
<?php echo $bajax->form('CorretoraEndereco', array('url' => array('action' => 'atualizar', $this->passedArgs[0]))); ?>
<?php echo $this->element('corretoras_enderecos/fields', array('edit_mode' => false)); ?>
<div class="form-actions">
  <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
  <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>   
<?php echo $this->BForm->end(); ?>
<?php echo $javascript->codeblock("jQuery(document).ready(function() {  

	$('.evt-endereco-cep').attr('callback', 'RetornoCep').blur()

});"); ?> 