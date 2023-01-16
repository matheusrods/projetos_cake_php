<?php
if ($this->Session->read('Message.flash.params.type') == MSGT_SUCCESS) {
    echo $this->Javascript->codeBlock("close_dialog('{$this->Buonny->flash()}');atualizaListaClientesProdutos('gerenciar', '{$codigo_cliente}');");
    exit;
}
?>
<?php echo $bajax->form('ClienteProdutoServico', array('url' => array('action' => 'atualizar_profissional_tipo', 'method' => 'put', $codigo_cliente, $codigo_produto, $codigo_servico, $codigo_profissional_tipo, $codigo_cliente_produto_servico))); ?>
<?php echo $this->BForm->input('Outros.codigo_produto', array('type' => 'hidden', 'value' => $codigo_produto));?>
<?php echo $this->BForm->input('Outros.codigo_servico', array('type' => 'hidden', 'value' => $codigo_servico));?>
<?php echo $this->BForm->input('Outros.codigo_profissional_tipo', array('type' => 'hidden', 'value' => $codigo_profissional_tipo));?>
<?php echo $this->BForm->input('Outros.codigo_cliente_produto_servico', array('type' => 'hidden', 'value' => $codigo_cliente_produto_servico));?>
<?php echo $this->BForm->input('codigo', array('type' => 'hidden')); ?>

<div class="row-fluid">
    <div>
        <strong>Produto..:</strong>
        <?php echo $produto_nome ?>
    </div>

    <div>
        <strong>Serviço..:</strong>
        <?php echo $servico_nome ?>
    </div>

    <div>
        <strong>Tipo Profissional..:</strong>
        <?php echo $profissional_tipo_nome ?>
    </div>
</div>

<div class="row-fluid inline">
    <?php echo $this->BForm->input('validade', array('placeholder' => $placeholder['validade'], 'label' => 'Validade (meses)', 'class' => 'numero input-medium', 'maxlength' => 2)); ?>
    <?php echo $this->BForm->input('tempo_pesquisa', array('placeholder' => $placeholder['tempo_pesquisa'], 'label' => 'Pesquisa (minutos)', 'class' => 'numero input-medium', 'maxlength' => 3)); ?>
</div>

<?php if ($codigo_profissional_tipo != 'todos'): ?>
<!-- Comentado a pedido do Nelson , OS ocommon :18537
<div class="row-fluid inline">
    <?php //echo $this->BForm->input('consistencia_motorista', array('label' => 'Consistência Motorista', 'options' => array('0' => 'Não', '1' => 'Sim'))); ?>
 
    <?php //echo $this->BForm->input('consulta_embarcador', array('label' => 'Consulta Embarcador', 'options' => array('0' => 'Não', '1' => 'Sim'))); ?>

</div>
-->
<?php endif; ?>

<div class="form-actions submit_box">
    <input type="submit" value="Salvar" class="btn btn-primary" />
    <?php echo $html->link('Voltar', '#', array('class' => 'btn', 'onclick' => 'close_dialog();')); ?>
</div>

<?php echo $this->BForm->end() ?>
<?php echo $this->Javascript->codeBlock("setup_mascaras();"); ?>