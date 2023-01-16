<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock(
            "close_dialog();
            var div = jQuery('div.lista');
            bloquearDiv(div);
            div.load(baseUrl + 'supervisores_equipes/listagem/' + Math.random());");
        exit;
    } else if($session->read('Message.flash.params.type') == MSGT_ERROR){
        $session->delete('Message.flash');
    }
?>
<?php echo $bajax->form('Usuario', array('url' => array('controller' => 'supervisores_equipes', 'action' => 'remanejamento_geral', $codigo_usuario_pai, $codigo_uperfil ))); ?>
<div class='row-fluid inline'>
    <div class='well'>
        <div class="row-fluid inline" >
            <span class="span4">
                De: <strong><?php echo strtoupper($dados_responsavel_atual['Usuario']['apelido']); ?></strong> para:
            </span>
            <span class="span8">
                <?php echo $this->BForm->input('Usuario.codigo_usuario_pai', array('label' => false, 'empty'=>'Selecione o responsável' , 'options' => $lista_usuarios_pais,'class'=>'input-xlarge' ));?>
            </span>            
        </div>
    </div>
</div>
<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
    <?php echo $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end();?>
