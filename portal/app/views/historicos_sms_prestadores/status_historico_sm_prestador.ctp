<?php  
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock(
            "close_dialog();
            var div = jQuery('div#historicos-sms-prestadores');
            bloquearDiv(div);
            div.load(baseUrl + 'historicos_sms_prestadores/prestador_por_atendimento/{$codigo_atendimento}/{$codigo_sm}' + '/' +Math.random());            
            var div = jQuery('div.listagem');
            bloquearDiv(div);
            div.load(baseUrl + 'historicos_sms/listagem/{$codigo_sm}' + '/' +Math.random());
            ");
        exit;
    } else if($session->read('Message.flash.params.type') == MSGT_ERROR){
        $session->delete('Message.flash');
    }
?>
<?php echo $bajax->form('HistoricoSmPrestador', array(
'url' => array('controller' => 'historicos_sms_prestadores', 'action' => 'status_historico_sm_prestador', $acao, $codigo_prestador, $codigo_atendimento, $codigo_sm))); ?>
<?php echo $this->BForm->input('observacao', array('label' => 'Observações', 'type' => 'textarea', 'class' => 'input-xxlarge')) ?>
<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-primary')); ?>
    <?= $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div> 
<?php echo $this->BForm->end(); ?>