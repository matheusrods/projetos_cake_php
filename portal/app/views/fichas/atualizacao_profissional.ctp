<?php
if ($session->read('Message.flash.params.type') == MSGT_SUCCESS) {
    $session->delete('Message.flash');
    echo $javascript->codeBlock("close_dialog();");
    echo $javascript->codeBlock("atualizaListaFichas('fichas');");
    exit;
}
?>
<?= $bajax->form('Ficha', array('url' => array('action' => 'atualizacao_profissional', $ficha['Ficha']['codigo']))); ?>
<div class="fullwide">
    <?= $this->BForm->hidden('codigo'); ?>
    <?= $this->BForm->input('Cliente.codigo', array('type' => 'text', 'readonly' => true, 'label' => 'Código', 'class' => 'text-smallest')); ?>
    <?= $this->BForm->input('Cliente.razao_social', array('readonly' => true, 'label' => 'Razão Social', 'class' => 'text-large')) ?>
    <?= $this->BForm->input('Ficha.codigo_usuario_solicitacao', array('options' => $usuariosDoCliente, 'empty'=> 'Selecione',  'label' => 'Usuário', 'class' => 'text-small2')); ?>
</div>
<div class="fullwide">
    <?= $this->BForm->input('ProfissionalLog.nome', array('type' => 'text', 'label' => 'Nome do Profissional', 'class' => 'text-medium')); ?>
    <?= $this->BForm->input('Produto.descricao', array('readonly' => true, 'label' => 'Produto', 'class' => 'text-medium')) ?>
</div>
<div class="fullwide">
    <?= $this->BForm->input('Status.descricao', array('type' => 'text', 'readonly' => true, 'label' => 'Status Anterior', 'class' => 'text-medium')); ?>
</div>
<div class="fullwide">
<?php
    echo $this->BForm->input('codigo_status', array(
        'options' => $novosStatusPermitidos,
        'label' => 'Novo Status'
    ));
    ?>
</div>
<div class="fullwide">
    <?= $this->BForm->input('LogAtendimento.observacao', array('label' => 'Observação', 'class' => 'text-large')); ?>
</div>
<div class="acao">
        <?= $this->BForm->submit('Salvar'); ?>
        <?= $html->link('Cancelar', 'javascript:void(0)', array('onclick' => 'close_dialog()')); ?>
</div>
<?= $this->BForm->end(); ?>

<script type="text/javascript">
    var inputCodigoUsuarioSolicitacao = $('#FichaCodigoUsuarioSolicitacao');
    var inputProfissionalLogNome = $('#ProfissionalLogNome');
    var textAreaObservacao = $('#LogAtendimentoObservacao');

    inputProfissionalLogNome.css('text-transform', 'uppercase');
    textAreaObservacao.css('text-transform', 'uppercase');
</script>