<?php
if ($this->Session->read('Message.flash.params.type') == MSGT_SUCCESS):
        echo $this->Javascript->codeBlock("close_dialog('{$this->Buonny->flash()}');atualizaListaClientesProdutos('gerenciar', '{$codigo_cliente}');");
    exit;
endif;
?>

<div>
    <strong>Produto..:</strong>
    <?php echo $this->data['Produto']['descricao']; ?>
</div>

<?php
    echo $bajax->form('ClienteProduto', array('url' => array('action' => 'atualizar_status', $this->data['ClienteProduto']['codigo'], $codigo_cliente)));
?>
<?php echo $this->BForm->input('codigo', array('type' => 'hidden')) ?>
<?php echo $this->BForm->input('codigo_cliente', array('type' => 'hidden', 'value' => $codigo_cliente)) ?>
<?php
echo $this->BForm->input('codigo_motivo_bloqueio', array(
    'label' => 'Status',
    'empty' => 'Selecione',
    'options' => $motivos,
));
echo $this->BForm->input('data_faturamento', array('label' => 'Data do faturamento', 'type' => 'text', 'class' => 'data input-small'));
?>

<div class="form-actions">
    <input type="submit" value="Salvar" class="btn 
           -primary" />
    <?php echo $html->link('Voltar', '#', array('class' => 'btn', 'onclick' => 'close_dialog();')); ?>
</div>

<?php echo $this->BForm->end(); ?>

<script type="text/javascript">
$(document).ready(function() {
	setup_mascaras();
	setup_datepicker();
});
</script>