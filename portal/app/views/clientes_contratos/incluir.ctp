<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        echo $javascript->codeBlock("close_dialog('{$this->Buonny->flash()}');atualizaListaClientesProdutos('clientes_produtos')");
        exit;
    }
?>
<div id="ClienteProdutoContrato">
    <?= $bajax->form('ClienteProdutoContrato',array('url' => array('controller' => 'clientes_contratos',  'action' => 'incluir', $cliente_produto['ClienteProduto']['codigo']))) ?>
    <?= $this->element('clientes_contratos/fields'); ?>
        <div class="acao">
            <?= $this->BForm->submit('Salvar'); ?>
            <?= $html->link('Cancelar', 'javascript:void(0)', array('onclick' => 'close_dialog()')); ?>
        </div>
</div>
<?php  echo $this->BForm->end() ?>