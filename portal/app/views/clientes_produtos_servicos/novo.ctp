<?php
if ($session->read('Message.flash.params.type') == MSGT_SUCCESS):
    $session->delete('Message.flash.params.type');
    echo $javascript->codeBlock("close_dialog();");
    exit;
endif;
?>
<link href="/portal/css/gerenciar_clientes_produtos.css" rel='stylesheet' type='text/css'></link>
<?php echo $bajax->form('ClienteProdutoServico2', array('url' => array('action' => 'incluir'))); ?>
<?php echo $this->BForm->input('codigo_cliente', array('type' => 'hidden'))?>
<?php echo $this->BForm->input('codigo_produto', array('type' => 'hidden'))?>
<?php echo $this->BForm->input('codigo_cliente_produto', array('type' => 'hidden'))?>
<div class="gerenciar_clientes_servicos_modal">

    <div class='gerenciar_clientes_servicos_modal_text'>
        <span class='label'>Produto..:</span>
        <span><?php echo $produto['Produto']['descricao'] ?></span>
    </div>

    <div class="fullwide">
        <?php
        echo $this->BForm->input('codigo_servico', array(
            'label' => 'Escolha o serviço..',
            'empty' => 'Selecione um serviço',
            'options' => $servicos,
            'class' => 'codigo_servico'
        ));
        ?>
    </div>

     <div class="fullwide submit_box">
        <input type="submit" value="Adicionar"></input>
     </div>

    <div class="clear"></div>
</div>
<?php echo $this->BForm->end() ?>