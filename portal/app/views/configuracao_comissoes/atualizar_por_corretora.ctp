<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock("close_dialog();atualizaListaConfiguracaoComissaoCorretora('#lista');");
        exit;
    }
?>
<?php echo $this->Bajax->form('ConfiguracaoComissaoCorre', array('url' => array('controller' => 'configuracao_comissoes','action' => 'atualizar_por_corretora'))) ?>
<?php echo $this->BForm->hidden('codigo'); ?>
<?php echo $this->element('configuracao_comissoes/fields_por_corretora'); ?>
<?php echo $this->BForm->end(); ?>